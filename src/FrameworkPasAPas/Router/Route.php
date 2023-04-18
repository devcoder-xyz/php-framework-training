<?php

declare(strict_types=1);

namespace App\FrameworkPasAPas\Router;

use InvalidArgumentException;

final class Route
{
    private string $name;
    private string $path;

    /**
     * @var mixed
     */
    private $handler;

    /**
     * @var array<string>
     */
    private array $methods = [];

    public function __construct(string $name, string $path, $handler, array $methods = ['GET'])
    {
        if ($methods === []) {
            throw new InvalidArgumentException('HTTP methods argument was empty; must contain at least one method');
        }
        $this->name = $name;
        $this->path = self::trimPath($path);
        $this->handler = $handler;
        $this->methods = $methods;
    }

    public function match(string $path, string $method): bool
    {
        if (!in_array($method, $this->getMethods())) {
            return false;
        }

        return self::trimPath($path) == $this->getPath();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    private static function trimPath(string $path): string
    {
        return '/' . rtrim(ltrim(trim($path), '/'), '/');
    }
}