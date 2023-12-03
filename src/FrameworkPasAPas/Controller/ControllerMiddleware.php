<?php

namespace App\FrameworkPasAPas\Controller;

use App\FrameworkPasAPas\DependencyInjection\Container;
use App\FrameworkPasAPas\Http\MiddlewareInterface;
use App\FrameworkPasAPas\Http\Request;
use App\FrameworkPasAPas\Http\Response;
use BadMethodCallException;
use LogicException;

final class ControllerMiddleware implements MiddlewareInterface
{
    public const CONTROLLER = '_controller';
    public const ACTION = '_action';
    public const NAME = '_name';

    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function process(Request $request): ?Response
    {
        $controller = $this->resolveController($request);
        if ($controller instanceof AbstractController) {
            $controller->setContainer($this->container);
        }

        return self::callController($request, $controller);
    }

    private function resolveController(Request $request): callable
    {
        $_controller = $request->getAttribute(self::CONTROLLER);
        if (is_callable($_controller)) {
            return $_controller;
        }

        $controller = $this->container->get($_controller);
        if (is_callable($controller)) {
            return $controller;
        }

        $action = $request->getAttribute(self::ACTION);
        if (method_exists($controller, $action) === false) {
            throw new BadMethodCallException(
                $action === null
                    ? sprintf('Please use a Method on class %s.', get_class($controller))
                    : sprintf('Method "%s" on class %s does not exist.', $action, get_class($controller))
            );
        }
        return [$controller, $action];
    }

    private static function callController(Request $request, $controller): Response
    {
        $attributes = $request->getAttributes();
        unset($attributes[self::CONTROLLER]);
        unset($attributes[self::ACTION]);
        unset($attributes[self::NAME]);

        $response = $controller(...array_values($attributes));
        if (!$response instanceof Response) {
            throw new LogicException(
                'The controller must return an instance of ' . Response::class
            );
        }
        return $response;
    }
}