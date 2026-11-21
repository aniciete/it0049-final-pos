<?php

/**
 * Bright-Market Laravel POS — Root Entry Point for Shared Hosting (/htdocs).
 */

if (file_exists(__DIR__.'/public/index.php')) {
    require_once __DIR__.'/public/index.php';
} else {
    define('LARAVEL_START', microtime(true));

    if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
        require $maintenance;
    }

    require __DIR__.'/vendor/autoload.php';

    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__.'/bootstrap/app.php';

    $app->handleRequest(\Illuminate\Http\Request::capture());
}
