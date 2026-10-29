<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function index()
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $dbName = $connection->getDatabaseName();
        $isMysql = in_array($driver, ['mysql', 'mariadb'], true);

        return view('settings.backup', compact('driver', 'dbName', 'isMysql'));
    }

    /**
     * Generate a full SQL dump and stream it as a download.
     * Pure PHP (no mysqldump/exec dependency) — cPanel/shared hosting safe.
     */
    public function download(Request $request)
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');

        $path = null;

        try {
            $connection = DB::connection();
            $driver = $connection->getDriverName();

            if (!in_array($driver, ['mysql', 'mariadb'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'આ સુવિધા ફક્ત MySQL / MariaDB ડેટાબેઝ માટે છે.',
                ], 422);
            }

            $dir = storage_path('app/backups');
            File::ensureDirectoryExists($dir, 0755, true);

            $filename = 'nexschool-backup-' . now()->format('Y-m-d_His') . '.sql';
            $path = $dir . DIRECTORY_SEPARATOR . $filename;

            $this->writeDump($connection, $path);
            $this->prune($dir, 5);

            return response()
                ->download($path, $filename, ['Content-Type' => 'application/sql'])
                ->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            if ($path && file_exists($path)) {
                @unlink($path);
            }

            return response()->json([
                'success' => false,
                'message' => 'બેકઅપ બનાવી શકાયું નહીં: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function writeDump($connection, string $path): void
    {
        $pdo = $connection->getPdo();
        $dbName = $connection->getDatabaseName();

        $fh = fopen($path, 'wb');
        if (!$fh) {
            throw new \RuntimeException('બેકઅપ ફાઇલ બનાવી શકાઈ નહીં (storage/app/backups લખી શકાતું નથી).');
        }

        try {
            fwrite($fh, "-- NexSchool Database Backup\n");
            fwrite($fh, "-- Database: {$dbName}\n");
            fwrite($fh, "-- Generated: " . now()->toDateTimeString() . "\n");
            fwrite($fh, "-- App version: " . config('app.version', '') . "\n\n");
            fwrite($fh, "SET NAMES utf8mb4;\n");
            fwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\n");
            fwrite($fh, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n");

            // એક જ consistent snapshot — dump દરમિયાન ડેટા બદલાય તો પણ
            // બધા tables એક જ ક્ષણના ડેટા આપે (InnoDB).
            $pdo->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
            $pdo->exec('START TRANSACTION WITH CONSISTENT SNAPSHOT');

            $tables = $pdo->query('SHOW FULL TABLES')->fetchAll(\PDO::FETCH_NUM);
            $views = [];

            foreach ($tables as $row) {
                $name = $row[0];
                $type = strtoupper((string) ($row[1] ?? 'BASE TABLE'));

                if ($type === 'VIEW') {
                    $views[] = $name;
                    continue;
                }

                $this->dumpTable($pdo, $fh, $name);
            }

            foreach ($views as $view) {
                $this->dumpView($pdo, $fh, $view);
            }

            $pdo->exec('COMMIT');

            fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
        } catch (\Throwable $e) {
            try {
                $pdo->rollBack();
            } catch (\Throwable $ignored) {
                // transaction already closed / not started
            }

            throw $e;
        } finally {
            fclose($fh);
        }
    }

    private function dumpTable(\PDO $pdo, $fh, string $table): void
    {
        $quoted = $this->quoteIdentifier($table);
        $create = $pdo->query("SHOW CREATE TABLE {$quoted}")->fetch(\PDO::FETCH_NUM);

        if (!$create) {
            return;
        }

        fwrite($fh, "--\n-- Table `{$table}`\n--\n");
        fwrite($fh, "DROP TABLE IF EXISTS {$quoted};\n");
        fwrite($fh, $create[1] . ";\n\n");

        $stmt = $pdo->query("SELECT * FROM {$quoted}");
        $columns = null;
        $batch = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if ($columns === null) {
                $columns = array_keys($row);
            }

            $values = [];
            foreach ($row as $value) {
                $values[] = $value === null ? 'NULL' : $pdo->quote((string) $value);
            }

            $batch[] = '(' . implode(',', $values) . ')';

            if (count($batch) >= 100) {
                $this->writeInsert($fh, $table, $columns, $batch);
                $batch = [];
            }
        }

        if ($batch) {
            $this->writeInsert($fh, $table, $columns, $batch);
        }

        fwrite($fh, "\n");
    }

    private function writeInsert($fh, string $table, array $columns, array $values): void
    {
        $columnSql = implode(',', array_map(fn ($c) => $this->quoteIdentifier($c), $columns));

        fwrite(
            $fh,
            "INSERT INTO {$this->quoteIdentifier($table)} ({$columnSql}) VALUES\n"
            . implode(",\n", $values) . ";\n"
        );
    }

    private function dumpView(\PDO $pdo, $fh, string $view): void
    {
        $quoted = $this->quoteIdentifier($view);
        $create = $pdo->query("SHOW CREATE VIEW {$quoted}")->fetch(\PDO::FETCH_NUM);

        if (!$create) {
            return;
        }

        fwrite($fh, "--\n-- View `{$view}`\n--\n");
        fwrite($fh, "DROP VIEW IF EXISTS {$quoted};\n");
        fwrite($fh, $create[1] . ";\n\n");
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    private function prune(string $dir, int $keep): void
    {
        $files = glob($dir . DIRECTORY_SEPARATOR . 'nexschool-backup-*.sql') ?: [];
        usort($files, fn ($a, $b) => filemtime($b) <=> filemtime($a));

        foreach (array_slice($files, $keep) as $old) {
            @unlink($old);
        }
    }
}
