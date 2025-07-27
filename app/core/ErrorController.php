<?php

namespace DevNoKage;

use DevNoKage\Abstract\AbstractController;
use DevNoKage\Enums\ClassName;

class ErrorController extends AbstractController
{
    public function _404(): void {
        echo '404';
    }
}
