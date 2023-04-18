<?php

namespace App\FrameworkPasAPas\DB\Query;

final class Update
{
    private string $table;

    /**
     * @var array<string>
     */
    private $conditions = [];

    /**
     * @var array<string>
     */
    private $values = [];

    public function __construct(string $table, ?string $alias = null)
    {
        $this->table = $alias === null ? $table : "${table} AS ${alias}";;
    }

    public function __toString(): string
    {
        return 'UPDATE ' . $this->table
            . ' SET ' . implode(', ', $this->values)
            . ($this->conditions === [] ? '' : ' WHERE ' . implode(' AND ', $this->conditions));
    }

    public function where(string ...$where): self
    {
        foreach ($where as $arg) {
            $this->conditions[] = $arg;
        }
        return $this;
    }

    public function set(string $column, string $value): self
    {
        $this->values[] = $column . ' = ' . $value;
        return $this;
    }
}