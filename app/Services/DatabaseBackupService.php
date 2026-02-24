<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseBackupService
{
    private const BATCH_SIZE = 300;

    /**
     * Stream the SQL dump as a download response.
     */
    public function streamDownload(string $filename): StreamedResponse
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName(); // 'mysql', 'mariadb', 'sqlite'

        if (!in_array($driver, ['mysql', 'mariadb', 'sqlite'])) {
            throw new \RuntimeException("Unsupported database driver: {$driver}. Only MySQL/MariaDB and SQLite are supported.");
        }

        return new StreamedResponse(function () use ($connection, $driver) {
            $pdo = $connection->getPdo();

            // --- Preamble ---
            echo "-- ==============================================\n";
            echo "-- CepatDapat Database Backup\n";
            echo "-- Generated: " . now()->format('Y-m-d H:i:s') . "\n";
            echo "-- Driver: {$driver}\n";
            echo "-- ==============================================\n\n";

            if (in_array($driver, ['mysql', 'mariadb'])) {
                echo "SET FOREIGN_KEY_CHECKS = 0;\n";
                echo "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
                echo "SET NAMES utf8mb4;\n\n";
            } elseif ($driver === 'sqlite') {
                echo "PRAGMA foreign_keys = OFF;\n\n";
            }

            // --- Tables ---
            $tables = $this->getTableNames($connection, $driver);

            foreach ($tables as $table) {
                echo "-- ----------------------------------------------\n";
                echo "-- Table: {$table}\n";
                echo "-- ----------------------------------------------\n\n";

                // DROP + CREATE
                $quotedTable = $this->quoteIdentifier($table, $driver);
                echo "DROP TABLE IF EXISTS {$quotedTable};\n";

                $createSql = $this->getCreateTableSql($connection, $table, $driver);
                echo $createSql . ";\n\n";

                // DATA
                $this->writeTableData($connection, $table, $pdo, $driver);

                echo "\n";
                flush();
            }

            // --- Footer ---
            if (in_array($driver, ['mysql', 'mariadb'])) {
                echo "SET FOREIGN_KEY_CHECKS = 1;\n";
            } elseif ($driver === 'sqlite') {
                echo "PRAGMA foreign_keys = ON;\n";
            }

            echo "\n-- Backup complete.\n";
            flush();

        }, 200, [
            'Content-Type' => 'application/sql; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * Get all base table names.
     */
    private function getTableNames($connection, string $driver): array
    {
        if (in_array($driver, ['mysql', 'mariadb'])) {
            $dbName = $connection->getDatabaseName();
            $rows = $connection->select(
                "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = 'BASE TABLE'",
                [$dbName]
            );
            return array_map(fn($r) => $r->TABLE_NAME, $rows);
        }

        // SQLite
        $rows = $connection->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        return array_map(fn($r) => $r->name, $rows);
    }

    /**
     * Get CREATE TABLE SQL for a given table.
     */
    private function getCreateTableSql($connection, string $table, string $driver): string
    {
        if (in_array($driver, ['mysql', 'mariadb'])) {
            $result = $connection->select("SHOW CREATE TABLE `{$table}`");
            return $result[0]->{'Create Table'} ?? '';
        }

        // SQLite
        $result = $connection->select("SELECT sql FROM sqlite_master WHERE type='table' AND name=?", [$table]);
        return $result[0]->sql ?? '';
    }

    /**
     * Stream INSERT statements for a table in batches. Never loads entire table.
     */
    private function writeTableData($connection, string $table, \PDO $pdo, string $driver): void
    {
        $count = $connection->table($table)->count();
        if ($count === 0) {
            echo "-- (empty table)\n";
            return;
        }

        $quotedTable = $this->quoteIdentifier($table, $driver);
        $offset = 0;

        while ($offset < $count) {
            $rows = $connection->table($table)->offset($offset)->limit(self::BATCH_SIZE)->get();

            if ($rows->isEmpty()) {
                break;
            }

            foreach ($rows as $row) {
                $columns = [];
                $values = [];

                foreach ((array) $row as $col => $val) {
                    $columns[] = $this->quoteIdentifier($col, $driver);
                    $values[] = $this->toSqlLiteral($val, $pdo);
                }

                $colStr = implode(', ', $columns);
                $valStr = implode(', ', $values);
                echo "INSERT INTO {$quotedTable} ({$colStr}) VALUES ({$valStr});\n";
            }

            $offset += self::BATCH_SIZE;
            flush();
        }
    }

    /**
     * Safely convert a PHP value to SQL literal using PDO quoting.
     */
    private function toSqlLiteral(mixed $value, \PDO $pdo): string
    {
        if ($value === null) {
            return 'NULL';
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }
        // String (including datetime strings)
        return $pdo->quote((string) $value);
    }

    /**
     * Quote a table or column identifier.
     */
    private function quoteIdentifier(string $name, string $driver): string
    {
        if (in_array($driver, ['mysql', 'mariadb'])) {
            return '`' . str_replace('`', '``', $name) . '`';
        }
        // SQLite uses double quotes
        return '"' . str_replace('"', '""', $name) . '"';
    }
}
