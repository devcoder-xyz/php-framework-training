<?php

declare(strict_types=1);

namespace App\FrameworkPasAPas\Session\Storage;

use function session_start;
use function session_status;
use const PHP_SESSION_NONE;

class NativeSessionStorage implements SessionStorageInterface
{
    private array $storage;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->storage = &$_SESSION;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->storage[$offset]);
    }

    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->storage[$offset];
        }
        return null;
    }

    public function offsetSet($offset, $value): self
    {
        $this->storage[$offset] = $value;
        return $this;
    }

    public function offsetUnset($offset): void
    {
        unset($this->storage[$offset]);
    }
}
