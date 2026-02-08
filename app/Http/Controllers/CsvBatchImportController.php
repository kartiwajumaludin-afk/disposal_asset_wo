<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CsvBatchImportController extends Controller
{
    private $chunkSize = 1000;

    public function process(Request $request)
    {
        // Validate
        $request->validate([
            'ticketFile' => 'required|file|mimes:csv,txt|max:524288',
            'assetFile' => 'required|file|mimes:csv,txt|max:524288',
            'workinfoFile' => 'required|file|mimes:csv,txt|max:524288',
            'manualFile' => 'nullable|file|mimes:csv,txt|max:524288',
        ]);

        try {
            // Get uploaded file temporary paths (DIRECT FROM PHP TEMP!)
            $ticketPath = $request->file('ticketFile')->getRealPath();
            $assetPath = $request->file('assetFile')->getRealPath();
            $workinfoPath = $request->file('workinfoFile')->getRealPath();

            // Verify temp files exist
            if (!file_exists($ticketPath)) {
                throw new \Exception("Ticket temp file not found");
            }
            if (!file_exists($assetPath)) {
                throw new \Exception("Asset temp file not found");
            }
            if (!file_exists($workinfoPath)) {
                throw new \Exception("Workinfo temp file not found");
            }

            Log::info("Files ready", [
                'ticket' => $ticketPath,
                'asset' => $assetPath,
                'workinfo' => $workinfoPath,
            ]);

            $results = [];

            // === TICKET IMPORT ===
            Log::info("Starting Ticket import");
            $ticketTotal = $this->countCsvRows($ticketPath);
            $this->uploadToStgChunked($ticketPath, 'ticket_raw_stg');
            
            $ticketStgCount = DB::table('ticket_raw_stg')->count();
            if ($ticketStgCount === 0) {
                throw new \Exception("Ticket STG upload failed: 0 rows");
            }
            
            Artisan::call('import:ticket');
            $ticketClean = DB::table('ticket_clean')->count();
            
            $results['ticket'] = [
                'total' => $ticketTotal,
                'stg' => $ticketStgCount,
                'clean' => $ticketClean,
            ];

            // === ASSET IMPORT ===
            Log::info("Starting Asset import");
            $assetTotal = $this->countCsvRows($assetPath);
            $this->uploadToStgChunked($assetPath, 'asset_raw_stg');
            
            $assetStgCount = DB::table('asset_raw_stg')->count();
            if ($assetStgCount === 0) {
                throw new \Exception("Asset STG upload failed: 0 rows");
            }
            
            Artisan::call('import:asset');
            $assetClean = DB::table('asset_clean')->count();
            
            $results['asset'] = [
                'total' => $assetTotal,
                'stg' => $assetStgCount,
                'clean' => $assetClean,
            ];

            // === WORKINFO IMPORT ===
            Log::info("Starting Workinfo import");
            $workinfoTotal = $this->countCsvRows($workinfoPath);
            $this->uploadToStgChunked($workinfoPath, 'workinfo_raw_stg');
            
            $workinfoStgCount = DB::table('workinfo_raw_stg')->count();
            if ($workinfoStgCount === 0) {
                throw new \Exception("Workinfo STG upload failed: 0 rows");
            }
            
            Artisan::call('import:workinfo');
            $workinfoClean = DB::table('workinfo_clean')->count();
            
            $results['workinfo'] = [
                'total' => $workinfoTotal,
                'stg' => $workinfoStgCount,
                'clean' => $workinfoClean,
            ];

            // === MANUAL UPDATE (OPTIONAL) ===
            if ($request->hasFile('manualFile')) {
                Log::info("Starting Manual update");
                $manualPath = $request->file('manualFile')->getRealPath();
                
                Artisan::call('import:manual-update', ['file' => $manualPath]);
                $manualRows = DB::table('tracker_manual_raw')->count();
                
                $results['manual'] = ['rows' => $manualRows];
            }

            // NO CLEANUP NEEDED - PHP auto-deletes temp files

            return redirect()->back()->with('success', 'Import completed successfully!')->with('results', $results);

        } catch (\Exception $e) {
            Log::error('Import Error: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
            
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    private function countCsvRows($filePath)
    {
        $handle = fopen($filePath, 'r');
        
        if (!$handle) {
            throw new \Exception("Cannot open file: {$filePath}");
        }
        
        $count = 0;
        while (fgets($handle) !== false) {
            $count++;
        }
        
        fclose($handle);
        
        return $count - 1;
    }

    private function uploadToStgChunked($filePath, $table)
    {
        Log::info("uploadToStgChunked START", [
            'filePath' => $filePath,
            'table' => $table,
            'fileExists' => file_exists($filePath),
            'fileSize' => file_exists($filePath) ? filesize($filePath) : 'N/A',
        ]);

        $handle = fopen($filePath, 'r');
        
        if (!$handle) {
            throw new \Exception("Cannot open file: {$filePath}");
        }
        
        $header = fgetcsv($handle);
        
        if (!$header) {
            fclose($handle);
            throw new \Exception("Invalid CSV: no header found");
        }
        
        Log::info("Header read", ['columns' => count($header)]);
        
        DB::table($table)->truncate();
        
        $chunk = [];
        $rowCount = 0;
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row)) === 0) continue;
            if (count($row) !== count($header)) continue;
            
            $rowData = array_combine($header, $row);
            
            if (!$rowData) continue;
            
            foreach ($rowData as $key => $value) {
                if ($value === '' || $value === 'NULL' || $value === 'null') {
                    $rowData[$key] = null;
                }
            }
            
            $chunk[] = $rowData;
            $rowCount++;
            
            if (count($chunk) >= $this->chunkSize) {
                DB::table($table)->insert($chunk);
                $chunk = [];
                usleep(10000);
            }
        }
        
        if (!empty($chunk)) {
            DB::table($table)->insert($chunk);
        }
        
        fclose($handle);
        
        Log::info("uploadToStgChunked COMPLETE", [
            'table' => $table,
            'rowsProcessed' => $rowCount,
        ]);
        
        return $rowCount;
    }
}