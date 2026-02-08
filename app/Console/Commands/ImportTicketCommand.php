<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TicketImportService;

class ImportTicketCommand extends Command
{
    protected $signature = 'import:ticket {step?}';
    protected $description = 'Import ticket data (step by step or full)';

    public function handle(TicketImportService $service)
    {
        $step = $this->argument('step');

        $this->info('🚀 Starting Ticket Import...');
        $this->newLine();

        if ($step === 'stg-raw') {
            // Step 1 only: STG → RAW
            $result = $service->processStgToRaw();
            $this->displayResult($result);

        } elseif ($step === 'raw-clean') {
            // Step 2 only: RAW → CLEAN
            $result = $service->processRawToClean();
            $this->displayResult($result);

        } elseif ($step === 'stats') {
            // Show statistics
            $stats = $service->getStats();
            $this->displayStats($stats);

        } elseif ($step === 'reset') {
            // Reset data (dengan konfirmasi)
            if ($this->confirm('⚠️  Reset all ticket data? This cannot be undone!')) {
                $result = $service->resetData(true);
                $this->displayResult($result);
            }

        } else {
            // Full process: STG → RAW → CLEAN
            $result = $service->processTicket();
            $this->displayResult($result);
        }

        return 0;
    }

    private function displayResult(array $result)
    {
        if ($result['status'] === 'success') {
            $this->info($result['message']);
            
            if (isset($result['details'])) {
                $this->newLine();
                $this->table(
                    ['Step', 'Status', 'Rows', 'Duration'],
                    [
                        [
                            'STG → RAW',
                            $result['details']['stg_to_raw']['status'],
                            $result['details']['stg_to_raw']['rows'],
                            $result['details']['stg_to_raw']['duration'] ?? '-'
                        ],
                        [
                            'RAW → CLEAN',
                            $result['details']['raw_to_clean']['status'],
                            $result['details']['raw_to_clean']['rows'],
                            $result['details']['raw_to_clean']['duration'] ?? '-'
                        ]
                    ]
                );
            }

            if (isset($result['total_duration'])) {
                $this->info("⏱️  Total Duration: {$result['total_duration']}");
            }

        } else {
            $this->error($result['message']);
        }
    }

    private function displayStats(array $stats)
    {
        $this->table(
            ['Table', 'Row Count'],
            [
                ['ticket_raw_stg', $stats['stg_count']],
                ['ticket_raw', $stats['raw_count']],
                ['ticket_clean', $stats['clean_count']]
            ]
        );

        if ($stats['last_import']) {
            $this->newLine();
            $this->info("📅 Last Import: {$stats['last_import']->uploaded_at}");
            $this->info("📊 Rows Imported: {$stats['last_import']->row_count}");
        }
    }
}