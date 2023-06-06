<?php

declare(strict_types=1);

namespace App\FrameworkPasAPas\Http;

final class Request
{
    private array $query;
    private array $request;
    private array $cookies;
    private array $files;
    private array $server;
    private array $attributes;

    public function __construct(array $server, array $query = [], array $request = [], array $cookies = [], array $files = [])
    {
        $this->query = $query;
        $this->request = $request;
        $this->cookies = $cookies;
        $this->files = $files;
        $this->server = $server;
    }

    public static function fromGlobals(): self
    {
        return new self($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
    }

    public function getUri(): string
    {
        return $this->server['REQUEST_URI'];
    }

    public function getMethod(): string
    {
        return $this->server['REQUEST_METHOD'];
    }

    public function isMethodPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function getRequest(): array
    {
        return $this->request;
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function withAttribute($key, $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
