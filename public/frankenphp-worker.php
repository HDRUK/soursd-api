<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set a default for the application base path and public path if they are missing...
$_SERVER['APP_BASE_PATH'] = $_ENV['APP_BASE_PATH'] ?? $_SERVER['APP_BASE_PATH'] ?? __DIR__.'/..';
$_SERVER['APP_PUBLIC_PATH'] = $_ENV['APP_PUBLIC_PATH'] ?? $_SERVER['APP_BASE_PATH'] ?? __DIR__;

echo "Reached worker\n";

require __DIR__.'/../vendor/laravel/octane/bin/frankenphp-worker.php';

use Illuminate\Support\Facades\Log;

Log::info('FrankenPHP worker has started and Laravel has booted.');