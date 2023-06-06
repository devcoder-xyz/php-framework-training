<?php

declare(strict_types=1);

namespace App\FrameworkPasAPas\Router;

use App\FrameworkPasAPas\Http\Request;
use App\FrameworkPasAPas\Router\Exception\RouteNotFound;

final class Router
{
    /**
     * @var array<Route>
     */
    private array $routes = [];

    /**
     * @param  $routes array<Route>
     */
    public function __construct(array $routes)
    {
        foreach ($routes as $route) {
            $this->add($route);
        }
    }

    /**
     * @throws RouteNotFound
     */
    public function match(Request $request) : Route
    {
        $path = $request->getUri();
        foreach ($this->routes as $route) {
            if ($route->match($path, $request->getMethod()) === false) {
                continue;
            }
            return $route;
        }

        throw new RouteNotFound(
            'No route found for ' . $path,
        );
    }

    public function add(Route $route): self
    {
        $this->routes[] = $route;
        return $this;
    }
}