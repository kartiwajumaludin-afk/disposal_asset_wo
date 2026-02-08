<?php

namespace App\Services;

use App\Models\ImportAudit;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkinfoImportService
{
    /**
     * STG → RAW: Insert data menggunakan SP
     */
    public function stgToRaw()
    {
        $stgCount = $this->getRowCount('workinfo_raw_stg');

        if ($stgCount === 0) {
            throw new \Exception('No data in workinfo_raw_stg. Please upload CSV first.');
        }

        DB::statement('CALL sp_insert_workinfo_raw()');

        $this->logImport('workinfo', $stgCount);

        return $stgCount;
    }

    /**
     * RAW → CLEAN: Insert data menggunakan SP
     */
    public function rawToClean()
    {
        $beforeCount = $this->getRowCount('workinfo_clean');

        DB::statement('CALL sp_workinfo_raw_to_clean()');

        $afterCount = $this->getRowCount('workinfo_clean');
        $inserted = $afterCount - $beforeCount;

        return $inserted;
    }

    /**
     * Truncate semua table workinfo
     */
    public function reset()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('workinfo_raw_stg')->truncate();
        DB::table('workinfo_raw')->truncate();
        DB::table('workinfo_clean')->truncate();
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
            'stg_count' => $this->getRowCount('workinfo_raw_stg'),
            'raw_count' => $this->getRowCount('workinfo_raw'),
            'clean_count' => $this->getRowCount('workinfo_clean'),
            'last_import' => ImportAudit::where('file_type', 'workinfo')->first(),
        ];
    }
}