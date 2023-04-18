<?php

declare(strict_types=1);

namespace App\FrameworkPasAPas\DB;

final class Manager
{
    protected \PDO $pdo;

    public function __construct(\PDO $pdo, array $attributes = [])
    {
        foreach ($attributes as $attribute => $value ) {
            $pdo->setAttribute($attribute, $value);
        }
        $this->pdo = $pdo;
    }

    /**
     * @param string $query
     * @param array<int|string, mixed> $params
     * @return \PDOStatement
     */
    public function executeQuery(string $query, array $params = []): \PDOStatement
    {
        $db = $this->pdo->prepare($query);
        foreach ($params as $key => $value) {
            if (is_int($key)) {
                $db->bindValue($key + 1, $value);
            } else {
                $db->bindValue(':' . $key, $value);
            }
        }
        $db->execute();
        return $db;
    }

    public function fetch(string $query, array $params = []): ?array
    {
        $db = $this->executeQuery($query, $params);
        $data = $db->fetch(\PDO::FETCH_ASSOC);
        return $data === false ? null : $data;
    }

    public function fetchAll(string $query, array $params = []): ?array
    {
        $db = $this->executeQuery($query, $params);
        return $db->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @return \PDO
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }
}