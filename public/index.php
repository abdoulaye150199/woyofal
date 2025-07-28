<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../app/config/bootstrap.php';

use DevNoKage\Router;

// Route de healthcheck
if ($_SERVER['REQUEST_URI'] === '/health') {
    header('Content-Type: text/plain');
    echo 'OK';
    exit;
}

// Routes API
$routes = [
    '/api/woyofal/compteur/{numero}' => [
        'controller' => 'App\Controller\WoyofalController',
        'method' => 'verifierCompteur'
    ],
    '/api/woyofal/acheter' => [
        'controller' => 'App\Controller\WoyofalController',
        'method' => 'acheter'
    ],
    // Route par défaut pour 404
    '/404' => [
        'controller' => 'DevNoKage\ErrorController',
        'method' => '_404'
    ]
];

Router::setRoute($routes);
Router::resolve();

