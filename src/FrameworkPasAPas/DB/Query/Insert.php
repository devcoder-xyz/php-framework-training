<?php

namespace App\FrameworkPasAPas\DB\Query;

final class Insert
{
    private string $table;

    /**
     * @var array<string>
     */
    private $values = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function __toString(): string
    {
        return 'INSERT INTO ' . $this->table
            . ' (' . implode(', ',array_keys($this->values)) . ') VALUES (' . implode(', ',$this->values) . ')';
    }

    public function set(string $column, string $value): self
    {
        $this->values[$column] = $value;
        return $this;
    }
}