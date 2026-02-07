<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Import Dashboard</title>
    <style>
        .import-section {
            border: 1px solid #ddd;
            padding: 18px 26px 22px 26px;
            border-radius: 8px;
            margin: 30px auto 20px auto;
            max-width: 480px;
            background: #fcfcfa;
            box-shadow: 0 4px 22px 0 #edeae7;
        }
        .import-section h2 {
            font-size: 21px;
            margin-bottom: 10px;
            margin-top: 0;
            letter-spacing: 1px;
        }
        .form-inline {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .filename-display {
            min-height: 20px;
            font-size: 14px;
            color: #5686C8;
            padding-top: 6px;
            padding-left: 2px;
            word-break: break-all;
        }
        .choose-file-label {
            padding: 6px 16px;
            background: #f5f5f5;
            border-radius: 4px;
            border: 1px solid #ccc;
            cursor: pointer;
            margin-right: 8px;
        }
        input[type="file"] {
            display: none;
        }
        .submit-btn {
            background: #5C7AEA;
            border: none;
            color: #fff;
            padding: 8px 20px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            margin-left: 8px;
        }
    </style>
</head>
<body>
    <h1 style="font-family:sans-serif;text-align:center;margin-top:32px;">Import Data CSV</h1>

    <?php
        // Pesan sukses/error dari sesi, jika ada
        if (session('success')) {
            echo '<div style="text-align:center;color:green;margin-bottom:18px;">'.htmlspecialchars(session('success')).'</div>';
        }
        if (session('error')) {
            echo '<div style="text-align:center;color:red;margin-bottom:18px;">'.htmlspecialchars(session('error')).'</div>';
        }
    ?>

    <?php
    // Helper untuk render kategori upload
    function renderImportBox($title, $type, $accept = '.csv') {
        $inputId = "csv-$type";
        $labelId = "label-$type";
        $filenameId = "file-name-$type";

        echo <<<HTML
        <div class="import-section">
            <h2>$title</h2>
            <form class="form-inline" method="POST" action="/import/$type" enctype="multipart/form-data" autocomplete="off">
                <!-- CSRF -->
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <label class="choose-file-label" for="$inputId">CHOOSE FILE</label>
                <input type="file" name="csv" id="$inputId" accept="$accept" />
                <button class="submit-btn" type="submit">UPLOAD</button>
            </form>
            <div class="filename-display" id="$filenameId"></div>
        </div>
HTML;
    }
    ?>

    <?php
    renderImportBox('Upload Ticket CSV', 'ticket');
    renderImportBox('Upload Asset CSV', 'asset');
    renderImportBox('Upload Workinfo CSV', 'workinfo');
    renderImportBox('Upload Manual Tracker CSV', 'manual');
    ?>

    <script>
    // Script JS agar nama file muncul setelah dipilih
    document.addEventListener('DOMContentLoaded', function() {
        ['ticket','asset','workinfo','manual'].forEach(function(type) {
            var input = document.getElementById('csv-' + type);
            var filename = document.getElementById('file-name-' + type);
            var label = document.querySelector('label[for="csv-' + type + '"]');
            input.addEventListener('change', function() {
                if (input.files && input.files.length > 0) {
                    filename.textContent = 'File: ' + input.files[0].name;
                } else {
                    filename.textContent = '';
                }
            });
            // Buat klik label = klik input file (jaga-jaga)
            if (label) {
                label.addEventListener('keydown', function(e){
                    if (e.key === 'Enter' || e.key === ' ') input.click();
                });
            }
        });
    });
    </script>
</body>
</html>
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class AssetController extends Controller
{
    public function index()
    {
        return view('import_dashboard');
    }

    public function importProcess(Request $request)
    {
        $type = $request->input('type');
        $targets = [
            'ticket'   => 'ticket_raw_stg',
            'asset'    => 'asset_raw_stg',
            'workinfo' => 'workinfo_raw_stg',
            'manual'   => 'tracker_manual_raw'
        ];

        if (!$request->hasFile('csv_file')) {
            return response()->json(['status' => 'ERROR', 'message' => 'File tidak ditemukan!']);
        }

        try {
            $path = $request->file('csv_file')->getRealPath();
            $file = fopen($path, 'r');
            $header = fgetcsv($file); 

            // Truncate staging table sebelum insert baru
            DB::table($targets[$type])->truncate();

            $data = [];
            while (($row = fgetcsv($file)) !== false) {
                $data[] = array_combine($header, $row);
                if (count($data) >= 500) {
                    DB::table($targets[$type])->insert($data);
                    $data = [];
                }
            }
            if (!empty($data)) {
                DB::table($targets[$type])->insert($data);
            }
            fclose($file);

            return response()->json(['status' => 'SUCCESS', 'message' => "Upload $type Berhasil!"]);
        } catch (Exception $e) {
            return response()->json(['status' => 'ERROR', 'message' => $e->getMessage()]);
        }
    }

    public function runPipeline()
    {
        set_time_limit(0);
        try {
            DB::beginTransaction();
            
            // 1. UPSERT TICKET & ASSET (Sesuai import_pipeline.php Aki)
            DB::statement("CALL sp_upsert_ticket_raw()");
            DB::statement("CALL sp_upsert_asset_raw()");
            
            // 2. WORKINFO SKIPSERT (Logika Manual Aki dipindah ke sini)
            DB::statement("
                INSERT INTO workinfo_raw (
                    `Ticket Number`,
                    `Ticket Sub Type Name`,
                    `Regional`,
                    `Network Operation and Productivity`,
                    `Teritory Operation`,
                    `Site ID`,
                    `Site Name`,
                    `Work Info Updated Date`,
                    `Work Info Status Name`,
                    `Work Info Note`,
                    `Work Info User Updater`,
                    `Work Info Role Updater`
                )
                SELECT
                    s.`Ticket Number`,
                    s.`Ticket Sub Type Name`,
                    s.`Regional`,
                    s.`Network Operation and Productivity`,
                    s.`Teritory Operation`,
                    s.`Site ID`,
                    s.`Site Name`,
                    s.`Work Info Updated Date`,
                    s.`Work Info Status Name`,
                    s.`Work Info Note`,
                    s.`Work Info User Updater`,
                    s.`Work Info Role Updater`
                FROM workinfo_raw_stg s
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM workinfo_raw r
                    WHERE r.`Ticket Number` = s.`Ticket Number`
                      AND r.`Work Info Status Name` = s.`Work Info Status Name`
                      AND r.`Work Info Updated Date` = s.`Work Info Updated Date`
                )
            ");
            DB::table('workinfo_raw_stg')->truncate();

            // 3. RAW -> CLEAN
            DB::statement("CALL sp_insert_ticket_clean()");
            DB::statement("CALL sp_asset_raw_to_clean()");
            DB::statement("CALL sp_workinfo_raw_to_clean()");

            // 4. BUSINESS LOGIC
            DB::statement("CALL sp_upsert_tracker_base()");
            DB::statement("CALL sp_tracker_business_logic_opt()");

            // 5. MANUAL UPDATE
            DB::statement("CALL sp_tracker_manual_raw_to_update()");
            DB::statement("CALL sp_apply_tracker_manual_update()");
            DB::table('tracker_manual_update')->truncate();

            DB::commit();
            return response()->json(['status' => 'SUCCESS', 'message' => 'PIPELINE SUCCESS!']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'ERROR', 'message' => $e->getMessage()]);
        }
    }
}