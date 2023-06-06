<?php

use App\FrameworkPasAPas\Router\Route;

return [
    new Route('app_index', '', [\App\Controller\MainController::class])
];
