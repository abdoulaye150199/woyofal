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

// Les routes sont définies dans route.web.php
Router::setRoute($routes);

Router::resolve();

