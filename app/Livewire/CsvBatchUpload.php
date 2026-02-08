<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CsvBatchUpload extends Component
{
    use WithFileUploads;

    public $ticketFile;
    public $assetFile;
    public $workinfoFile;
    public $manualFile;

    public $importProgress = [];
    public $isImporting = false;
    public $importComplete = false;
    public $chunkSize = 1000; // 1000 rows per chunk

    protected $messages = [
        'ticketFile.required' => 'Ticket CSV is required',
        'assetFile.required' => 'Asset CSV is required',
        'workinfoFile.required' => 'Workinfo CSV is required',
    ];

    public function updatedTicketFile()
    {
        $this->validateFile('ticketFile');
    }

    public function updatedAssetFile()
    {
        $this->validateFile('assetFile');
    }

    public function updatedWorkinfoFile()
    {
        $this->validateFile('workinfoFile');
    }

    public function updatedManualFile()
    {
        $this->validateFile('manualFile');
    }

    private function validateFile($property)
    {
        if (!$this->$property) return;

        $this->validate([
            $property => 'file|mimes:csv,txt|max:524288', // 512MB max
        ]);
    }

    public function import()
    {
        // Validate mandatory files
        $this->validate([
            'ticketFile' => 'required|file|mimes:csv,txt|max:524288',
            'assetFile' => 'required|file|mimes:csv,txt|max:524288',
            'workinfoFile' => 'required|file|mimes:csv,txt|max:524288',
            'manualFile' => 'nullable|file|mimes:csv,txt|max:524288',
        ]);

        $this->isImporting = true;
        $this->importProgress = [];
        $this->importComplete = false;

        try {
            // Windows path only!
            $baseDir = base_path();
            $tempDir = $baseDir . '\\storage\\app\\temp-csv';
            
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0777, true);
            }

            $this->updateProgress('upload', 'processing', '📤 Saving uploaded files to storage...');

            // Save files
            $ticketStoragePath = $this->ticketFile->store('temp-csv');
            $assetStoragePath = $this->assetFile->store('temp-csv');
            $workinfoStoragePath = $this->workinfoFile->store('temp-csv');
            $manualStoragePath = $this->manualFile ? $this->manualFile->store('temp-csv') : null;

            // Get FULL Windows paths (backslash only!)
            $ticketFullPath = $baseDir . '\\storage\\app\\' . str_replace('/', '\\', $ticketStoragePath);
            $assetFullPath = $baseDir . '\\storage\\app\\' . str_replace('/', '\\', $assetStoragePath);
            $workinfoFullPath = $baseDir . '\\storage\\app\\' . str_replace('/', '\\', $workinfoStoragePath);
            $manualFullPath = $manualStoragePath ? $baseDir . '\\storage\\app\\' . str_replace('/', '\\', $manualStoragePath) : null;

            // Verify files exist
            if (!file_exists($ticketFullPath)) {
                throw new \Exception("Ticket file not saved: {$ticketFullPath}");
            }
            if (!file_exists($assetFullPath)) {
                throw new \Exception("Asset file not saved: {$assetFullPath}");
            }
            if (!file_exists($workinfoFullPath)) {
                throw new \Exception("Workinfo file not saved: {$workinfoFullPath}");
            }

            $ticketSize = filesize($ticketFullPath);
            $assetSize = filesize($assetFullPath);
            $workinfoSize = filesize($workinfoFullPath);
            
            $this->updateProgress('upload', 'success', "✅ Files saved - Ticket: " . round($ticketSize/1024/1024, 2) . " MB | Asset: " . round($assetSize/1024/1024, 2) . " MB | Workinfo: " . round($workinfoSize/1024/1024, 2) . " MB");

            // === DEBUG INFO ===
            $this->updateProgress('debug', 'processing', "🔍 DEBUG: Ticket path: {$ticketFullPath}");
            $this->updateProgress('debug', 'processing', "🔍 DEBUG: File exists: " . (file_exists($ticketFullPath) ? 'YES' : 'NO'));
            $this->updateProgress('debug', 'processing', "🔍 DEBUG: File readable: " . (is_readable($ticketFullPath) ? 'YES' : 'NO'));
            
            // Try to read first line
            $testHandle = fopen($ticketFullPath, 'r');
            if ($testHandle) {
                $firstLine = fgets($testHandle);
                $this->updateProgress('debug', 'processing', "🔍 DEBUG: First line preview: " . substr($firstLine, 0, 100) . "...");
                fclose($testHandle);
            } else {
                $this->updateProgress('debug', 'error', "🔍 DEBUG: Cannot open file for testing!");
            }

            // === TICKET IMPORT ===
            $this->updateProgress('ticket', 'processing', '🔍 Analyzing ticket file...');
            $ticketTotal = $this->countCsvRows($ticketFullPath);
            
            $this->updateProgress('ticket', 'processing', "📊 Found {$ticketTotal} tickets. Starting chunked upload...");
            
            $ticketImported = $this->uploadToStgChunked(
                $ticketFullPath, 
                'ticket_raw_stg',
                'ticket',
                $ticketTotal
            );
            
            // Verify STG upload
            $ticketStgCount = DB::table('ticket_raw_stg')->count();
            $this->updateProgress('ticket', 'processing', "✅ STG upload verified: {$ticketStgCount} rows in ticket_raw_stg");
            
            if ($ticketStgCount === 0) {
                throw new \Exception("STG upload failed: 0 rows inserted to ticket_raw_stg. Check laravel.log for details.");
            }
            
            $this->updateProgress('ticket', 'processing', '⚙️ Processing ticket data (STG → RAW → CLEAN)...');
            Artisan::call('import:ticket');
            
            $ticketClean = DB::table('ticket_clean')->count();
            $this->updateProgress('ticket', 'success', "✅ Ticket complete: {$ticketClean} rows in CLEAN");

            // === ASSET IMPORT ===
            $this->updateProgress('asset', 'processing', '🔍 Analyzing asset file...');
            $assetTotal = $this->countCsvRows($assetFullPath);
            
            $this->updateProgress('asset', 'processing', "📊 Found {$assetTotal} assets. Starting chunked upload...");
            
            $assetImported = $this->uploadToStgChunked(
                $assetFullPath, 
                'asset_raw_stg',
                'asset',
                $assetTotal
            );
            
            // Verify STG upload
            $assetStgCount = DB::table('asset_raw_stg')->count();
            $this->updateProgress('asset', 'processing', "✅ STG upload verified: {$assetStgCount} rows in asset_raw_stg");
            
            if ($assetStgCount === 0) {
                throw new \Exception("STG upload failed: 0 rows inserted to asset_raw_stg. Check laravel.log for details.");
            }
            
            $this->updateProgress('asset', 'processing', '⚙️ Processing asset data (STG → RAW → CLEAN)...');
            Artisan::call('import:asset');
            
            $assetClean = DB::table('asset_clean')->count();
            $this->updateProgress('asset', 'success', "✅ Asset complete: {$assetClean} rows in CLEAN");

            // === WORKINFO IMPORT ===
            $this->updateProgress('workinfo', 'processing', '🔍 Analyzing workinfo file...');
            $workinfoTotal = $this->countCsvRows($workinfoFullPath);
            
            $this->updateProgress('workinfo', 'processing', "📊 Found {$workinfoTotal} workinfo. Starting chunked upload...");
            
            $workinfoImported = $this->uploadToStgChunked(
                $workinfoFullPath, 
                'workinfo_raw_stg',
                'workinfo',
                $workinfoTotal
            );
            
            // Verify STG upload
            $workinfoStgCount = DB::table('workinfo_raw_stg')->count();
            $this->updateProgress('workinfo', 'processing', "✅ STG upload verified: {$workinfoStgCount} rows in workinfo_raw_stg");
            
            if ($workinfoStgCount === 0) {
                throw new \Exception("STG upload failed: 0 rows inserted to workinfo_raw_stg. Check laravel.log for details.");
            }
            
            $this->updateProgress('workinfo', 'processing', '⚙️ Processing workinfo data (STG → RAW → CLEAN)...');
            Artisan::call('import:workinfo');
            
            $workinfoClean = DB::table('workinfo_clean')->count();
            $this->updateProgress('workinfo', 'success', "✅ Workinfo complete: {$workinfoClean} rows in CLEAN");

            // === MANUAL UPDATE (OPTIONAL) ===
            if ($manualFullPath) {
                $this->updateProgress('manual', 'processing', '🔍 Processing manual update...');
                
                Artisan::call('import:manual-update', [
                    'file' => $manualFullPath
                ]);
                
                $manualRows = DB::table('tracker_manual_raw')->count();
                $this->updateProgress('manual', 'success', "✅ Manual: {$manualRows} rows imported");
            }

            // Cleanup temporary files (Windows style)
            if (file_exists($ticketFullPath)) @unlink($ticketFullPath);
            if (file_exists($assetFullPath)) @unlink($assetFullPath);
            if (file_exists($workinfoFullPath)) @unlink($workinfoFullPath);
            if ($manualFullPath && file_exists($manualFullPath)) @unlink($manualFullPath);

            $this->importComplete = true;
            $this->updateProgress('complete', 'success', '🎉 All imports completed successfully!');

        } catch (\Exception $e) {
            $this->updateProgress('error', 'error', '❌ Error: ' . $e->getMessage());
            Log::error('Import Error: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
            Log::error($e->getTraceAsString());
        } finally {
            $this->isImporting = false;
        }
    }

    /**
     * Count total rows in CSV (excluding header)
     */
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
        
        return $count - 1; // Exclude header
    }

    /**
     * Upload CSV to STG table with CHUNKING by LINES (WITH DEBUG)
     */
    private function uploadToStgChunked($filePath, $table, $progressKey, $totalRows)
    {
        // DEBUG: Log file info
        Log::info("uploadToStgChunked START", [
            'filePath' => $filePath,
            'table' => $table,
            'totalRows' => $totalRows,
            'fileExists' => file_exists($filePath),
            'fileSize' => file_exists($filePath) ? filesize($filePath) : 'N/A',
            'isReadable' => is_readable($filePath),
        ]);

        $handle = fopen($filePath, 'r');
        
        if (!$handle) {
            Log::error("FAILED TO OPEN FILE: {$filePath}");
            throw new \Exception("Cannot open file: {$filePath}");
        }
        
        Log::info("File opened successfully");
        
        // Read header
        $header = fgetcsv($handle);
        
        if (!$header) {
            fclose($handle);
            Log::error("NO HEADER FOUND in file: {$filePath}");
            throw new \Exception("Invalid CSV: no header found in {$filePath}");
        }
        
        Log::info("Header read successfully", [
            'headerCount' => count($header),
            'headerColumns' => $header
        ]);
        
        // Clear STG table
        DB::table($table)->truncate();
        Log::info("STG table truncated: {$table}");
        
        $chunk = [];
        $rowCount = 0;
        $chunkCount = 0;
        $totalChunks = ceil($totalRows / $this->chunkSize);
        $insertedChunks = 0;
        
        while (($row = fgetcsv($handle)) !== false) {
            // Skip empty rows
            if (count(array_filter($row)) === 0) {
                Log::debug("Skipping empty row at line " . ($rowCount + 2));
                continue;
            }
            
            // Check if row column count matches header
            if (count($row) !== count($header)) {
                Log::warning("Column count mismatch at row " . ($rowCount + 2), [
                    'headerCount' => count($header),
                    'rowCount' => count($row),
                ]);
                continue;
            }
            
            // Combine header with row
            $rowData = array_combine($header, $row);
            
            if (!$rowData) {
                Log::warning("array_combine failed at row " . ($rowCount + 2));
                continue;
            }
            
            // Convert empty strings to null
            foreach ($rowData as $key => $value) {
                if ($value === '' || $value === 'NULL' || $value === 'null') {
                    $rowData[$key] = null;
                }
            }
            
            $chunk[] = $rowData;
            $rowCount++;
            
            // When chunk is full, insert to database
            if (count($chunk) >= $this->chunkSize) {
                try {
                    DB::table($table)->insert($chunk);
                    $insertedChunks++;
                    $chunkCount++;
                    
                    Log::info("Chunk {$chunkCount} inserted successfully", [
                        'chunkSize' => count($chunk),
                        'totalRowsInserted' => $rowCount,
                    ]);
                    
                    // Update progress
                    $percentage = round(($chunkCount / $totalChunks) * 100);
                    $this->updateProgress(
                        $progressKey, 
                        'processing', 
                        "⏳ Uploading to STG: {$rowCount}/{$totalRows} rows ({$percentage}%) - Chunk {$chunkCount}/{$totalChunks}"
                    );
                    
                    // Clear chunk
                    $chunk = [];
                    
                    // Small delay to prevent memory issues
                    usleep(10000); // 10ms
                    
                } catch (\Exception $e) {
                    Log::error("Failed to insert chunk {$chunkCount}", [
                        'error' => $e->getMessage(),
                        'chunkSize' => count($chunk),
                        'firstRow' => $chunk[0] ?? null,
                    ]);
                    throw $e;
                }
            }
        }
        
        // Insert remaining rows
        if (!empty($chunk)) {
            try {
                DB::table($table)->insert($chunk);
                $insertedChunks++;
                $chunkCount++;
                
                Log::info("Final chunk {$chunkCount} inserted", [
                    'chunkSize' => count($chunk),
                    'totalRowsInserted' => $rowCount,
                ]);
                
            } catch (\Exception $e) {
                Log::error("Failed to insert final chunk", [
                    'error' => $e->getMessage(),
                    'chunkSize' => count($chunk),
                ]);
                throw $e;
            }
        }
        
        fclose($handle);
        
        Log::info("uploadToStgChunked COMPLETE", [
            'table' => $table,
            'totalRowsProcessed' => $rowCount,
            'totalChunksInserted' => $insertedChunks,
        ]);
        
        $this->updateProgress(
            $progressKey, 
            'processing', 
            "✅ Upload complete: {$rowCount} rows in {$chunkCount} chunks"
        );
        
        return $rowCount;
    }

    private function updateProgress($type, $status, $message)
    {
        $this->importProgress[$type] = [
            'status' => $status,
            'message' => $message,
            'time' => now()->format('H:i:s'),
        ];
    }

    public function resetUpload()
    {
        $this->reset([
            'ticketFile', 
            'assetFile', 
            'workinfoFile', 
            'manualFile', 
            'importProgress', 
            'importComplete', 
            'isImporting'
        ]);
    }

    public function render()
    {
        return view('livewire.csv-batch-upload');
    }
}