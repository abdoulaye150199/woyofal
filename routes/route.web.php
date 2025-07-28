<?php

use DevNoKage\Enums\KeyRoute;
use DevNoKage\ErrorController;
use App\Controller\WoyofalController;

return [
    '/' => [
        KeyRoute::CONTROLLER->value => WoyofalController::class,
        KeyRoute::METHOD->value => 'index',
        KeyRoute::MIDDLEWARE->value => [],
        KeyRoute::HTTP_METHOD->value => 'GET'
    ],
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
    ],
    '/404' => [
        KeyRoute::CONTROLLER->value => ErrorController::class,
        KeyRoute::METHOD->value => '_404',
        KeyRoute::MIDDLEWARE->value => [],
        KeyRoute::HTTP_METHOD->value => 'GET'
    ]
];