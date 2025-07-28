<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../app/config/bootstrap.php';

use DevNoKage\Router;

// Health check
if ($_SERVER['REQUEST_URI'] === '/health') {
    header('Content-Type: text/plain');
    echo 'OK';
    exit;
}

// Make sure $routes is available from bootstrap.php
if (!isset($routes) || !is_array($routes)) {
    throw new RuntimeException('Routes configuration not found or invalid');
}

// Set routes
Router::setRoute($routes);
Router::resolve();

