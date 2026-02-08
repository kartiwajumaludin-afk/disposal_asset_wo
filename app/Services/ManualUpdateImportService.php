<?php

namespace App\Services;

use App\Models\ImportAudit;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ManualUpdateImportService
{
    /**
     * Import CSV to tracker_manual_raw
     * Using UPSERT (INSERT ... ON DUPLICATE KEY UPDATE)
     */
    public function importFromCsv($filePath)
    {
        // Validate file exists
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        // Read CSV
        $data = $this->readCsv($filePath);

        if (empty($data)) {
            throw new \Exception('No data in CSV file.');
        }

        // Validate ticket_numbers exist in ticket_clean
        $this->validateTicketNumbers($data);

        // Insert/Update to tracker_manual_raw
        $inserted = $this->upsertData($data);

        // Log to import_audit
        $this->logImport('manual_update', $inserted);

        return $inserted;
    }

    /**
     * Read CSV file and return array of data
     */
    private function readCsv($filePath)
    {
        $data = [];
        $header = null;

        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$header) {
                    $header = $row; // First row as header
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

    /**
     * Validate ticket_numbers exist in ticket_clean
     */
    private function validateTicketNumbers($data)
    {
        $ticketNumbers = array_column($data, 'ticket_number');
        
        $existingTickets = DB::table('ticket_clean')
            ->whereIn('ticket_number', $ticketNumbers)
            ->pluck('ticket_number')
            ->toArray();

        $missingTickets = array_diff($ticketNumbers, $existingTickets);

        if (!empty($missingTickets)) {
            $missing = implode(', ', array_slice($missingTickets, 0, 5));
            throw new \Exception("Ticket numbers not found in ticket_clean: {$missing}");
        }
    }

    /**
     * Insert or Update data to tracker_manual_raw
     */
    private function upsertData($data)
    {
        $inserted = 0;

        foreach ($data as $row) {
            // Prepare data (convert empty strings to NULL)
            $preparedData = $this->prepareRowData($row);

            // UPSERT using ON DUPLICATE KEY UPDATE
            DB::table('tracker_manual_raw')->updateOrInsert(
                ['ticket_number' => $preparedData['ticket_number']],
                $preparedData
            );

            $inserted++;
        }

        return $inserted;
    }

    /**
     * Prepare row data: convert empty strings to NULL
     */
    private function prepareRowData($row)
    {
        $prepared = [];

        // Columns mapping
        $columns = [
            'ticket_number',
            'tp_company',
            'latitude',
            'longitude',
            'caf_status',
            'general_status',
            'start_permit_tp_date_raw',
            'end_permit_tp_date_raw',
            'status_permit_tp',
            'ticket_batch',
            'site_status',
            'site_issue',
            'category_issue',
            'detail_issue',
            'remark_dismantle',
            'mom',
            'partner_company',
            'plan_dismantle_date_raw',
            'pic_team',
            'no_handphone',
        ];

        foreach ($columns as $col) {
            // Skip if column not in CSV
            if (!isset($row[$col])) {
                $prepared[$col] = null;
                continue;
            }

            $value = trim($row[$col]);

            // Convert empty string to NULL
            if ($value === '' || $value === 'NULL') {
                $prepared[$col] = null;
            } else {
                $prepared[$col] = $value;
            }
        }

        // Set created_at (jika belum ada) dan updated_at
        $prepared['created_at'] = Carbon::now();

        return $prepared;
    }

    /**
     * Truncate tracker_manual_raw
     */
    public function reset()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('tracker_manual_raw')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Get row count
     */
    private function getRowCount($table)
    {
        return DB::table($table)->count();
    }

    /**
     * Log import to audit table
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
            'manual_count' => $this->getRowCount('tracker_manual_raw'),
            'last_import' => ImportAudit::where('file_type', 'manual_update')->first(),
        ];
    }
}