<?php

namespace App\Console\Commands;

use App\Services\ManualUpdateImportService;
use Illuminate\Console\Command;

class ImportManualUpdate extends Command
{
    protected $signature = 'import:manual-update 
                            {file? : Path to CSV file}
                            {--reset : Reset manual update data}
                            {--stats : Show statistics}';

    protected $description = 'Import Manual Update CSV to tracker_manual_raw';

    private $service;

    public function __construct(ManualUpdateImportService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function handle()
    {
        $this->info('🚀 Starting Manual Update Import...');
        $this->newLine();

        // Handle --reset
        if ($this->option('reset')) {
            return $this->handleReset();
        }

        // Handle --stats
        if ($this->option('stats')) {
            return $this->handleStats();
        }

        // Handle file import
        $filePath = $this->argument('file');

        if (!$filePath) {
            $this->error('❌ Please provide CSV file path!');
            $this->info('Usage: php artisan import:manual-update /path/to/file.csv');
            return 1;
        }

        return $this->handleImport($filePath);
    }

    private function handleImport($filePath)
    {
        $startTime = microtime(true);

        try {
            // Import
            $inserted = $this->service->importFromCsv($filePath);

            $duration = round(microtime(true) - $startTime, 2);

            $this->newLine();
            $this->info("✅ Manual Update import completed successfully");
            $this->newLine();

            // Show table
            $this->table(
                ['Step', 'Status', 'Rows', 'Duration'],
                [
                    ['CSV → Manual RAW', 'success', $inserted, "{$duration}s"],
                ]
            );

            $this->info("⏱️  Total Duration: {$duration}s");

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }

    private function handleReset()
    {
        $this->warn('⚠️  This will delete ALL manual update data!');
        
        if (!$this->confirm('Are you sure?')) {
            $this->info('Reset cancelled.');
            return 0;
        }

        $this->service->reset();
        
        $this->info('✅ Manual update data has been reset.');
        
        return 0;
    }

    private function handleStats()
    {
        $stats = $this->service->getStats();

        $this->table(
            ['Table', 'Row Count'],
            [
                ['tracker_manual_raw', $stats['manual_count']],
            ]
        );

        if ($stats['last_import']) {
            $this->newLine();
            $this->info('📅 Last Import: ' . $stats['last_import']->uploaded_at);
            $this->info('📊 Rows Imported: ' . $stats['last_import']->row_count);
        }

        return 0;
    }
}