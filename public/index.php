<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../app/config/bootstrap.php';

use DevNoKage\Router;

Router::setRoute($routes);

Router::resolve();

