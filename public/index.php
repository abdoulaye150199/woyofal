<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../app/config/bootstrap.php';

use DevNoKage\Router;
use App\Controller\WoyofalController;
use DevNoKage\Enums\KeyRoute;

// Définir les routes
$routes = [
    '/api/woyofal/compteur/{numero}' => [
        KeyRoute::CONTROLLER->value => WoyofalController::class,
        KeyRoute::METHOD->value => 'verifierCompteur',
        KeyRoute::MIDDLEWARE->value => [],
        KeyRoute::HTTP_METHOD->value => 'GET'
    ],
    '/api/woyofal/acheter' => [
        KeyRoute::CONTROLLER->value => WoyofalController::class,
        KeyRoute::METHOD->value => 'acheter',
        KeyRoute::MIDDLEWARE->value => [],
        KeyRoute::HTTP_METHOD->value => 'POST'
    ]
];

// Health check
if ($_SERVER['REQUEST_URI'] === '/health') {
    header('Content-Type: text/plain');
    echo 'OK';
    exit;
}

// Debug des routes
error_log("🛣️ Routes définies: " . json_encode($routes));
error_log("🌐 URI demandée: " . $_SERVER['REQUEST_URI']);

Router::setRoute($routes);
Router::resolve();

