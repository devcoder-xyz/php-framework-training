<?php

namespace App\FrameworkPasAPas\Router;

use App\FrameworkPasAPas\Controller\ControllerMiddleware;
use App\FrameworkPasAPas\Http\MiddlewareInterface;
use App\FrameworkPasAPas\Http\Request;
use App\FrameworkPasAPas\Http\Response;
use App\FrameworkPasAPas\Kernel;
use App\FrameworkPasAPas\Router\Exception\RouteNotFound;

final class RouterMiddleware implements MiddlewareInterface
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function process(Request $request): ?Response
    {
        try {
            $route = $this->router->match($request);
            $routeHandler = $route->getHandler();
            $attributes = [
                ControllerMiddleware::CONTROLLER => $routeHandler[0] ?? $routeHandler,
                ControllerMiddleware::ACTION => $routeHandler[1] ?? null,
                ControllerMiddleware::NAME => $route->getName(),
            ];

            foreach ($attributes as $key => $value) {
                $request = $request->withAttribute($key, $value);
            }
        } catch (RouteNotFound $exception) {
            return new Response(null,404);
        }

        return null;
    }
}
