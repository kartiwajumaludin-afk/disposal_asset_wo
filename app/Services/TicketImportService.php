<?php

namespace App\Services;

use App\Models\TicketRawStg;
use App\Models\TicketRaw;
use App\Models\TicketClean;
use App\Models\ImportAudit;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * TicketImportService
 * Handle import ticket dari STG → RAW → CLEAN
 */
class TicketImportService extends BaseService
{
    /**
     * Process ticket: STG → RAW
     * Call SP: sp_upsert_ticket_raw()
     * 
     * @return array ['status' => 'success'|'failed', 'message' => string, 'rows' => int]
     */
    public function processStgToRaw(): array
    {
        $startTime = microtime(true);
        
        try {
            $this->logStep('TICKET STG → RAW', 'START');

            // Check if STG has data
            $stgCount = $this->getRowCount('ticket_raw_stg');
            
            if ($stgCount === 0) {
                return [
                    'status' => 'failed',
                    'message' => 'No data in ticket_raw_stg. Please upload CSV first.',
                    'rows' => 0
                ];
            }

            // Get initial RAW count
            $rawCountBefore = $this->getRowCount('ticket_raw');

            // Call SP: sp_upsert_ticket_raw()
            $this->executeStoredProcedure('sp_upsert_ticket_raw');

            // Get final RAW count
            $rawCountAfter = $this->getRowCount('ticket_raw');
            $processedRows = $rawCountAfter - $rawCountBefore;

            // Truncate STG after success
            $this->truncateTable('ticket_raw_stg');

            $duration = microtime(true) - $startTime;

            $this->logStep('TICKET STG → RAW', 'SUCCESS', [
                'stg_rows' => $stgCount,
                'processed_rows' => $processedRows,
                'raw_total' => $rawCountAfter,
                'duration' => $this->formatDuration($duration)
            ]);

            return [
                'status' => 'success',
                'message' => "✅ Ticket STG → RAW completed ({$processedRows} rows processed)",
                'rows' => $processedRows,
                'duration' => $this->formatDuration($duration)
            ];

        } catch (Exception $e) {
            $this->logStep('TICKET STG → RAW', 'FAILED', [
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'failed',
                'message' => "❌ Error: " . $e->getMessage(),
                'rows' => 0
            ];
        }
    }

    /**
     * Process ticket: RAW → CLEAN
     * Call SP: sp_insert_ticket_clean()
     * 
     * @return array ['status' => 'success'|'failed', 'message' => string, 'rows' => int]
     */
    public function processRawToClean(): array
    {
        $startTime = microtime(true);
        
        try {
            $this->logStep('TICKET RAW → CLEAN', 'START');

            // Check if RAW has data
            $rawCount = $this->getRowCount('ticket_raw');
            
            if ($rawCount === 0) {
                return [
                    'status' => 'failed',
                    'message' => 'No data in ticket_raw. Please process STG → RAW first.',
                    'rows' => 0
                ];
            }

            // Get initial CLEAN count
            $cleanCountBefore = $this->getRowCount('ticket_clean');

            // Call SP: sp_insert_ticket_clean()
            $this->executeStoredProcedure('sp_insert_ticket_clean');

            // Get final CLEAN count
            $cleanCountAfter = $this->getRowCount('ticket_clean');
            $insertedRows = $cleanCountAfter - $cleanCountBefore;

            $duration = microtime(true) - $startTime;

            $this->logStep('TICKET RAW → CLEAN', 'SUCCESS', [
                'raw_rows' => $rawCount,
                'inserted_rows' => $insertedRows,
                'clean_total' => $cleanCountAfter,
                'duration' => $this->formatDuration($duration)
            ]);

            return [
                'status' => 'success',
                'message' => "✅ Ticket RAW → CLEAN completed ({$insertedRows} rows inserted)",
                'rows' => $insertedRows,
                'duration' => $this->formatDuration($duration)
            ];

        } catch (Exception $e) {
            $this->logStep('TICKET RAW → CLEAN', 'FAILED', [
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'failed',
                'message' => "❌ Error: " . $e->getMessage(),
                'rows' => 0
            ];
        }
    }

    /**
     * Full process: STG → RAW → CLEAN (chained)
     * 
     * @return array ['status' => 'success'|'failed', 'message' => string, 'details' => array]
     */
    public function processTicket(): array
    {
        $startTime = microtime(true);
        $results = [];

        try {
            $this->logStep('TICKET FULL IMPORT', 'START');

            // Step 1: STG → RAW
            $step1 = $this->processStgToRaw();
            $results['stg_to_raw'] = $step1;

            if ($step1['status'] === 'failed') {
                return [
                    'status' => 'failed',
                    'message' => 'Ticket import failed at STG → RAW step',
                    'details' => $results
                ];
            }

            // Step 2: RAW → CLEAN
            $step2 = $this->processRawToClean();
            $results['raw_to_clean'] = $step2;

            if ($step2['status'] === 'failed') {
                return [
                    'status' => 'failed',
                    'message' => 'Ticket import failed at RAW → CLEAN step',
                    'details' => $results
                ];
            }

            // Update audit log
            ImportAudit::updateOrCreate(
                ['file_type' => 'ticket'],
                [
                    'status' => 'DONE',
                    'row_count' => $step1['rows'],
                    'uploaded_at' => now()
                ]
            );

            $totalDuration = microtime(true) - $startTime;

            $this->logStep('TICKET FULL IMPORT', 'SUCCESS', [
                'total_rows' => $step1['rows'],
                'duration' => $this->formatDuration($totalDuration)
            ]);

            return [
                'status' => 'success',
                'message' => "✅ Ticket import completed successfully",
                'details' => $results,
                'total_duration' => $this->formatDuration($totalDuration)
            ];

        } catch (Exception $e) {
            $this->logStep('TICKET FULL IMPORT', 'FAILED', [
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'failed',
                'message' => "❌ Ticket import failed: " . $e->getMessage(),
                'details' => $results
            ];
        }
    }

    /**
     * Get ticket import statistics
     * 
     * @return array
     */
    public function getStats(): array
    {
        return [
            'stg_count' => $this->getRowCount('ticket_raw_stg'),
            'raw_count' => $this->getRowCount('ticket_raw'),
            'clean_count' => $this->getRowCount('ticket_clean'),
            'last_import' => ImportAudit::where('file_type', 'ticket')->first(),
        ];
    }

    /**
     * Reset ticket data (for testing - DANGER!)
     * 
     * @param bool $confirm
     * @return array
     */
    public function resetData(bool $confirm = false): array
    {
        if (!$confirm) {
            return [
                'status' => 'failed',
                'message' => 'Reset not confirmed. Pass true to confirm.'
            ];
        }

        try {
            $this->truncateTable('ticket_raw_stg');
            $this->truncateTable('ticket_raw');
            $this->truncateTable('ticket_clean');

            ImportAudit::where('file_type', 'ticket')->delete();

            return [
                'status' => 'success',
                'message' => '✅ All ticket data reset successfully'
            ];

        } catch (Exception $e) {
            return [
                'status' => 'failed',
                'message' => "❌ Reset failed: " . $e->getMessage()
            ];
        }
    }
}