<?php

use Dotenv\Dotenv;

try {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->safeLoad();
} catch (\Exception $e) {
    error_log("Warning: " . $e->getMessage());
}

// Define database constants with fallback values
define('DB_DRIVE', $_ENV['DB_DRIVE'] ?? 'pgsql');
define('DB_HOST', $_ENV['DB_HOST'] ?? 'caboose.proxy.rlwy.net');
define('DB_PORT', $_ENV['DB_PORT'] ?? '23700');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'railway');
define('DB_USER', $_ENV['DB_USER'] ?? 'postgres');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? 'qNJEApvSDDxUlgZqYemJruixCTTpWjkm');

define('METHODE_INSTANCE_NAME', $_ENV['METHODE_INSTANCE_NAME'] ?? 'getInstance');
define('SERVICES_PATH', $_ENV['SERVICES_PATH'] ?? '../app/config/services.yml');
