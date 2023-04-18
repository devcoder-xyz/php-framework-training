<?php

declare(strict_types=1);

namespace App\FrameworkPasAPas\DependencyInjection;

use App\FrameworkPasAPas\DependencyInjection\Exception\NotFoundException;

final class Container
{
    /**
     * @var array
     */
    private array $definitions = [];

    /**
     * @var array
     */
    private array $resolvedEntries = [];

    public function __construct(array $definitions)
    {
        $this->definitions = array_merge(
            $definitions,
            [self::class => $this]
        );
    }

    /**
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return mixed Entry.
     *
     * @throws NotFoundException  No entry was found.
     */
    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException("No entry or class found for '$id'");
        }

        if (array_key_exists($id, $this->resolvedEntries)) {
            return $this->resolvedEntries[$id];
        }

        $value = $this->definitions[$id];
        if ($value instanceof \Closure) {
            $value = $value($this);
        }

        $this->resolvedEntries[$id] = $value;
        return $value;
    }

    /**
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions) || array_key_exists($id, $this->resolvedEntries);
    }
}