<?php

/**
 * Token-Gated Deployment Extractor & Database Provisioner — Bright-Market Laravel POS.
 */
header('Content-Type: text/plain; charset=utf-8');

$tokenFile = __DIR__.'/.extract_token';
$provided = $_GET['token'] ?? '';

if (! is_file($tokenFile) || ! is_string($provided) || $provided === '') {
    http_response_code(403);
    echo "FORBIDDEN: Missing deployment token.\n";
    exit(1);
}

$expected = trim((string) file_get_contents($tokenFile));
if ($expected === '' || ! hash_equals($expected, $provided)) {
    http_response_code(403);
    echo "FORBIDDEN: Invalid deployment token.\n";
    exit(1);
}

$zipFile = __DIR__.'/it0049-final-pos-infinityfree.zip';

if (! file_exists($zipFile)) {
    http_response_code(404);
    echo 'ERROR: Deployment package not found at '.$zipFile."\n";
    exit(1);
}

if (! class_exists('ZipArchive')) {
    http_response_code(500);
    echo "ERROR: ZipArchive extension is not enabled in this PHP environment.\n";
    exit(1);
}

/**
 * Minimal .env reader for extracting MySQL credentials post-unpack.
 *
 * @return array<string, string>
 */
function loadEnvFile(string $path): array
{
    $vars = [];
    if (! file_exists($path)) {
        return $vars;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $vars[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
    }

    return $vars;
}

$zip = new ZipArchive;
$res = $zip->open($zipFile);

if ($res !== true) {
    http_response_code(500);
    echo 'ERROR: Could not open zip file. Error code: '.$res."\n";
    exit(1);
}

$extracted = $zip->extractTo(__DIR__);
$zip->close();

if (! $extracted) {
    http_response_code(500);
    echo 'ERROR: Failed during extraction to '.__DIR__."\n";
    exit(1);
}

@unlink($zipFile);
echo "SUCCESS: Extracted it0049-final-pos-infinityfree.zip successfully!\n";

if (file_exists(__DIR__.'/index2.html')) {
    @unlink(__DIR__.'/index2.html');
    echo "Removed default InfinityFree index2.html placeholder.\n";
}

$storageDirs = [
    __DIR__.'/storage/app/public/avatars',
    __DIR__.'/storage/app/public/products',
    __DIR__.'/storage/framework/cache/data',
    __DIR__.'/storage/framework/sessions',
    __DIR__.'/storage/framework/views',
    __DIR__.'/storage/logs',
    __DIR__.'/bootstrap/cache',
];
foreach ($storageDirs as $sDir) {
    if (! is_dir($sDir)) {
        @mkdir($sDir, 0775, true);
    }
    @chmod($sDir, 0775);
}

$linkTarget = __DIR__.'/storage/app/public';
$linkPath = __DIR__.'/public/storage';
if (! file_exists($linkPath) && function_exists('symlink')) {
    @symlink($linkTarget, $linkPath);
}

$envFile = __DIR__.'/.env';
$env = loadEnvFile($envFile);
$sqlFile = __DIR__.'/database/pos_final.sql';

$dbHost = $env['DB_HOST'] ?? '';
$dbName = $env['DB_DATABASE'] ?? '';
$dbUser = $env['DB_USERNAME'] ?? '';
$dbPass = $env['DB_PASSWORD'] ?? '';
$appUrl = $env['APP_URL'] ?? '';

$mysqlSuccess = false;
if ($dbHost !== '' && $dbName !== '' && $dbUser !== '' && file_exists($sqlFile)) {
    $candidateHosts = [$dbHost];
    if (preg_match('/^sql\d+/', $dbHost, $m)) {
        $prefix = $m[0];
        $candidateHosts[] = $prefix.'.infinityfree.com';
        for ($i = 0; $i <= 9; $i++) {
            $candidateHosts[] = $prefix.$i.'.infinityfree.com';
        }
    }
    $candidateHosts = array_values(array_unique($candidateHosts));

    $lastError = null;
    foreach ($candidateHosts as $tryHost) {
        try {
            $dsn = "mysql:host={$tryHost};dbname={$dbName};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 3,
            ]);

            if ($tryHost !== $dbHost && file_exists($envFile)) {
                $envContent = (string) file_get_contents($envFile);
                $envContent = preg_replace('/^DB_HOST=.*/m', 'DB_HOST='.$tryHost, $envContent);
                file_put_contents($envFile, $envContent);
            }

            $existingSales = $pdo->query("SHOW TABLES LIKE 'sales'")->fetchColumn();
            if ($existingSales === false) {
                $sqlContent = (string) file_get_contents($sqlFile);
                $statements = array_filter(array_map('trim', explode(';', $sqlContent)));
                foreach ($statements as $stmt) {
                    if ($stmt !== '' && ! preg_match('/^(--|\/\*)/', $stmt)) {
                        $pdo->exec($stmt);
                    }
                }
                echo "SUCCESS: Imported MySQL schema and initial seed data into {$dbName} via {$tryHost}.\n";
            } else {
                echo "NOTICE: Existing MySQL tables already present on {$tryHost}; preserved live data.\n";
            }
            $mysqlSuccess = true;
            break;
        } catch (Throwable $e) {
            $lastError = $e->getMessage();
        }
    }

    if (! $mysqlSuccess && $lastError !== null) {
        echo 'NOTICE: MySQL initialization failed ('.$lastError."). Falling back to SQLite.\n";
    }
}

if (! $mysqlSuccess && file_exists($envFile)) {
    $envContent = (string) file_get_contents($envFile);
    $envContent = preg_replace('/^DB_CONNECTION=.*/m', 'DB_CONNECTION=sqlite', $envContent);
    file_put_contents($envFile, $envContent);
}

@unlink($tokenFile);
@unlink(__FILE__);
echo "Removed extract.php and one-time token for security.\n";
echo "Deployment complete! Visit {$appUrl}/ to verify.\n";
