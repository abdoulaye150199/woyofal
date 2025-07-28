<?php

require_once '../vendor/autoload.php';
require_once '../app/config/helpers.php';
require_once '../app/config/env.php';

// Load routes and assign to variable
$routes = require_once '../routes/route.web.php';