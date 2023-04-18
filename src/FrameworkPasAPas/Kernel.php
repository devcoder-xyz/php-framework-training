<?php

declare(strict_types=1);

namespace App\FrameworkPasAPas;

use App\FrameworkPasAPas\DependencyInjection\Container;
use App\FrameworkPasAPas\Http\MiddlewareInterface;
use App\FrameworkPasAPas\Http\Request;
use App\FrameworkPasAPas\Http\Response;
use function dirname;

final class Kernel
{
    private string $environment;
    private Container $container;
    private array $middlewareCollection = [];

    public function __construct(string $environment)
    {
        $this->environment = $environment;
        \error_reporting(0);
        if ($environment === 'dev') {
            \error_reporting(E_ALL);
            \ini_set('display_errors', '1');
        }
        $this->boot();
    }

    private function boot(): void
    {
        date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'UTC');
        $this->initializeContainer();
        $this->initializeMiddlewares();
    }

    public function handle(Request $request): Response
    {
        $response = null;
        foreach ($this->middlewareCollection as $middleware) {

            if (is_string($middleware)) {
                $middleware = $this->container->get($middleware);
            }

            if (!$middleware instanceof MiddlewareInterface) {
                throw new \LogicException(
                    sprintf(
                        'The Middleware must be an instance of %s, "%s" given.',
                        MiddlewareInterface::class,
                        is_object($middleware) ? get_class($middleware) : gettype($middleware)
                    )
                );
            }

            $response = $middleware->process($request);
            if ($response instanceof Response) {
                break;
            }
        }
        return $response;
    }


    public function getParameters(): array
    {
        return [
            'kernel.project_dir' => $this->getProjectDir(),
            'kernel.environment' => $this->environment,
        ];
    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__, 2);
    }

    private function initializeContainer(): void
    {
        $parameters = $this->getParameters();
        $definitions = require $this->getProjectDir() . '/config/services.php';
        $this->container = new Container($parameters + $definitions);
    }

    private function initializeMiddlewares(): void
    {
        $this->middlewareCollection = require $this->getProjectDir() . '/config/middlewares.php';
    }
}