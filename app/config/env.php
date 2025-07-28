<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// Add DATABASE_URL support
define('DATABASE_URL', $_ENV['DATABASE_URL'] ?? null);

// Use DATABASE_URL if available, otherwise use individual settings
if (DATABASE_URL) {
    $dbUrl = parse_url(DATABASE_URL);
    define('DB_DRIVE', $dbUrl['scheme'] ?? $_ENV['DB_DRIVE'] ?? 'pgsql');
    define('DB_HOST', $dbUrl['host'] ?? $_ENV['DB_HOST'] ?? 'caboose.proxy.rlwy.net');
    define('DB_PORT', $dbUrl['port'] ?? $_ENV['DB_PORT'] ?? '23700');
    define('DB_USER', $dbUrl['user'] ?? $_ENV['DB_USER'] ?? 'postgres');
    define('DB_PASSWORD', $dbUrl['pass'] ?? $_ENV['DB_PASSWORD'] ?? 'qNJEApvSDDxUlgZqYemJruixCTTpWjkm');
    define('DB_NAME', trim($dbUrl['path'] ?? '', '/') ?? $_ENV['DB_NAME'] ?? 'railway');
} else {
    define('DB_HOST', $_ENV['DB_HOST'] ?? '');
    define('DB_PORT', $_ENV['DB_PORT'] ?? '');
    define('DB_DRIVE', $_ENV['DB_DRIVE'] ?? '');
    define('DB_USER', $_ENV['DB_USER'] ?? '');
    define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '');
    define('DB_NAME', $_ENV['DB_NAME'] ?? '');
}

define('METHODE_INSTANCE_NAME', $_ENV['METHODE_INSTANCE_NAME'] ?? 'getInstance');
define('SERVICES_PATH', $_ENV['SERVICES_PATH'] ?? '../app/config/services.yml');
