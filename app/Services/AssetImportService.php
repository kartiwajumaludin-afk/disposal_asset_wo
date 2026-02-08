<?php

namespace App\Services;

use App\Models\ImportAudit;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssetImportService
{
    /**
     * STG → RAW: Upsert data menggunakan SP
     */
    public function stgToRaw()
    {
        $stgCount = $this->getRowCount('asset_raw_stg');

        if ($stgCount === 0) {
            throw new \Exception('No data in asset_raw_stg. Please upload CSV first.');
        }

        DB::statement('CALL sp_upsert_asset_raw()');

        $this->logImport('asset', $stgCount);

        return $stgCount;
    }

    /**
     * RAW → CLEAN: Insert data menggunakan SP
     */
    public function rawToClean()
    {
        $beforeCount = $this->getRowCount('asset_clean');

        DB::statement('CALL sp_asset_raw_to_clean()');

        $afterCount = $this->getRowCount('asset_clean');
        $inserted = $afterCount - $beforeCount;

        return $inserted;
    }

    /**
     * Truncate semua table asset
     */
    public function reset()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('asset_raw_stg')->truncate();
        DB::table('asset_raw')->truncate();
        DB::table('asset_clean')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Get row count dari table
     */
    private function getRowCount($table)
    {
        return DB::table($table)->count();
    }

    /**
     * Log import ke audit table
     */
    private function logImport($fileType, $rowCount)
    {
        ImportAudit::updateOrCreate(
            ['file_type' => $fileType],
            [
                'status' => 'DONE',
                'row_count' => $rowCount,
                'uploaded_at' => Carbon::now(),
            ]
        );
    }

    /**
     * Get statistics
     */
    public function getStats()
    {
        return [
            'stg_count' => $this->getRowCount('asset_raw_stg'),
            'raw_count' => $this->getRowCount('asset_raw'),
            'clean_count' => $this->getRowCount('asset_clean'),
            'last_import' => ImportAudit::where('file_type', 'asset')->first(),
        ];
    }
}