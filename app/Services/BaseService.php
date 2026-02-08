<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * BaseService - Helper untuk call Stored Procedures
 * Dipakai oleh semua Import Services
 */
class BaseService
{
    /**
     * Call stored procedure dengan error handling
     * 
     * @param string $procedureName Nama SP (tanpa "CALL")
     * @param array $params Parameter untuk SP (optional)
     * @return array Result dari SP
     * @throws Exception
     */
    protected function callStoredProcedure(string $procedureName, array $params = []): array
    {
        try {
            // Build CALL statement
            $placeholders = implode(',', array_fill(0, count($params), '?'));
            $sql = "CALL {$procedureName}";
            
            if (!empty($params)) {
                $sql .= "({$placeholders})";
            } else {
                $sql .= "()";
            }

            Log::info("Calling SP: {$sql}", ['params' => $params]);

            // Execute
            $stmt = DB::getPdo()->prepare($sql);
            $stmt->execute($params);
            
            // Fetch result (if any)
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Close cursor (WAJIB untuk multiple SP calls)
            $stmt->closeCursor();

            Log::info("SP executed successfully: {$procedureName}");

            return $result;

        } catch (Exception $e) {
            Log::error("SP execution failed: {$procedureName}", [
                'error' => $e->getMessage(),
                'params' => $params
            ]);
            throw $e;
        }
    }

    /**
     * Call SP tanpa return value (untuk DDL/DML)
     * 
     * @param string $procedureName
     * @return void
     * @throws Exception
     */
    protected function executeStoredProcedure(string $procedureName): void
    {
        try {
            Log::info("Executing SP: {$procedureName}");
            
            DB::unprepared("CALL {$procedureName}()");
            
            Log::info("SP executed: {$procedureName}");

        } catch (Exception $e) {
            Log::error("SP execution failed: {$procedureName}", [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Truncate table dengan error handling
     * 
     * @param string $tableName
     * @return void
     */
    protected function truncateTable(string $tableName): void
    {
        try {
            Log::info("Truncating table: {$tableName}");
            DB::table($tableName)->truncate();
            Log::info("Table truncated: {$tableName}");
        } catch (Exception $e) {
            Log::error("Truncate failed: {$tableName}", [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get row count dari table
     * 
     * @param string $tableName
     * @return int
     */
    protected function getRowCount(string $tableName): int
    {
        return DB::table($tableName)->count();
    }

    /**
     * Check if table has data
     * 
     * @param string $tableName
     * @return bool
     */
    protected function hasData(string $tableName): bool
    {
        return $this->getRowCount($tableName) > 0;
    }

    /**
     * Log import step
     * 
     * @param string $step
     * @param string $status (START|SUCCESS|FAILED)
     * @param array $context
     * @return void
     */
    protected function logStep(string $step, string $status, array $context = []): void
    {
        $emoji = [
            'START' => '🚀',
            'SUCCESS' => '✅',
            'FAILED' => '❌'
        ];

        Log::info("{$emoji[$status]} {$step} - {$status}", $context);
    }

    /**
     * Execute dalam transaction dengan rollback otomatis
     * 
     * @param callable $callback
     * @return mixed
     * @throws Exception
     */
    protected function transaction(callable $callback)
    {
        try {
            DB::beginTransaction();
            
            $result = $callback();
            
            DB::commit();
            
            return $result;
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error("Transaction rolled back", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Format duration (seconds to human readable)
     * 
     * @param float $seconds
     * @return string
     */
    protected function formatDuration(float $seconds): string
    {
        if ($seconds < 60) {
            return round($seconds, 2) . 's';
        }
        
        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;
        
        return "{$minutes}m " . round($secs, 2) . 's';
    }
}