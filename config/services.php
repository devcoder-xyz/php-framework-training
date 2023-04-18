<?php

use App\Controller\MainController;
use App\FrameworkPasAPas\Controller\ControllerMiddleware;
use App\FrameworkPasAPas\DependencyInjection\Container;
use App\FrameworkPasAPas\Renderer\PHPRenderer;
use App\FrameworkPasAPas\Router\RouterMiddleware;
use App\FrameworkPasAPas\Router\Router;
use App\FrameworkPasAPas\Session\Flash;
use App\FrameworkPasAPas\Session\Storage\NativeSessionStorage;
use App\FrameworkPasAPas\Session\Storage\SessionStorageInterface;

return [
    MainController::class => static function (Container $container) {
        return new MainController();
    },
    RouterMiddleware::class => static function (Container $container) {
        return new RouterMiddleware($container->get(Router::class));
    },
    ControllerMiddleware::class => static function (Container $container) {
        return new ControllerMiddleware($container);
    },
    SessionStorageInterface::class => static function (Container $container) {
        return new NativeSessionStorage();
    },
    Flash::class => static function (Container $container) {
        $session = $container->get(SessionStorageInterface::class);
        return new Flash($session);
    },
    Router::class => static function (Container $container) {
        return new Router(require 'routes.php');
    },
    PHPRenderer::class => static function (Container $container) {
        return new PHPRenderer($container->get('template.directory'), ['flash' => $container->get(Flash::class)]);
    },
];