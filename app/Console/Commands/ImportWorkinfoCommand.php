<?php

namespace App\Console\Commands;

use App\Services\WorkinfoImportService;
use Illuminate\Console\Command;

class ImportWorkinfoCommand extends Command
{
    protected $signature = 'import:workinfo {action? : stats|stg-raw|raw-clean|reset}';
    protected $description = 'Import Workinfo data (STG → RAW → CLEAN)';

    public function handle(WorkinfoImportService $service)
    {
        $this->info('🚀 Starting Workinfo Import...');
        $this->newLine();

        $action = $this->argument('action');

        try {
            switch ($action) {
                case 'stats':
                    $this->showStats($service);
                    break;

                case 'stg-raw':
                    $this->stgToRaw($service);
                    break;

                case 'raw-clean':
                    $this->rawToClean($service);
                    break;

                case 'reset':
                    $this->reset($service);
                    break;

                default:
                    $this->fullImport($service);
                    break;
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function showStats(WorkinfoImportService $service)
    {
        $stats = $service->getStats();

        $this->table(
            ['Table', 'Row Count'],
            [
                ['workinfo_raw_stg', $stats['stg_count']],
                ['workinfo_raw', $stats['raw_count']],
                ['workinfo_clean', $stats['clean_count']],
            ]
        );

        if ($stats['last_import']) {
            $this->newLine();
            $this->info('📅 Last Import: ' . $stats['last_import']->uploaded_at);
            $this->info('📊 Rows Imported: ' . $stats['last_import']->row_count);
        }
    }

    private function stgToRaw(WorkinfoImportService $service)
    {
        $startTime = microtime(true);
        $rows = $service->stgToRaw();
        $duration = round(microtime(true) - $startTime, 2);

        $this->info("✅ Workinfo STG → RAW completed ({$rows} rows processed in {$duration}s)");
    }

    private function rawToClean(WorkinfoImportService $service)
    {
        $startTime = microtime(true);
        $rows = $service->rawToClean();
        $duration = round(microtime(true) - $startTime, 2);

        $this->info("✅ Workinfo RAW → CLEAN completed ({$rows} rows inserted in {$duration}s)");
    }

    private function reset(WorkinfoImportService $service)
    {
        if ($this->confirm('Are you sure you want to truncate all workinfo tables?', false)) {
            $service->reset();
            $this->info('✅ All workinfo tables have been truncated.');
        } else {
            $this->info('❌ Reset cancelled.');
        }
    }

    private function fullImport(WorkinfoImportService $service)
    {
        $overallStart = microtime(true);
        $results = [];

        // STG → RAW
        $startTime = microtime(true);
        try {
            $rows = $service->stgToRaw();
            $duration = round(microtime(true) - $startTime, 2);
            $results[] = ['STG → RAW', 'success', $rows, $duration . 's'];
        } catch (\Exception $e) {
            $duration = round(microtime(true) - $startTime, 2);
            $results[] = ['STG → RAW', 'failed', 0, $duration . 's'];
            throw $e;
        }

        // RAW → CLEAN
        $startTime = microtime(true);
        try {
            $rows = $service->rawToClean();
            $duration = round(microtime(true) - $startTime, 2);
            $results[] = ['RAW → CLEAN', 'success', $rows, $duration . 's'];
        } catch (\Exception $e) {
            $duration = round(microtime(true) - $startTime, 2);
            $results[] = ['RAW → CLEAN', 'failed', 0, $duration . 's'];
            throw $e;
        }

        $totalDuration = round(microtime(true) - $overallStart, 1);

        $this->newLine();
        $this->info('✅ Workinfo import completed successfully');
        $this->newLine();

        $this->table(
            ['Step', 'Status', 'Rows', 'Duration'],
            $results
        );

        $this->info("⏱️  Total Duration: {$totalDuration}s");
    }
}