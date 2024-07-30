<?php

require_once "init.php";

class Database
{
    private PDO $pdo;
    private string $hostname;
    private string $username;
    private string $password;
    private string $database;

    public function __construct(string $hostname, string $username, string $password, string $database)
    {
        $this->hostname = $hostname;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;

        $this->connect();
    }

    public function insert(string $table, array $data): int
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_map(fn ($key) => ":$key", array_keys($data)));

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return (int)$this->pdo->lastInsertId();
    }
    public function select(string $table, array $criteria = []): array
    {
        $sql = "SELECT * FROM $table";

        if (!empty($criteria)) {
            $conditions = implode(" AND ", array_map(fn ($key) => "$key = :$key", array_keys($criteria)));
            $sql .= " WHERE $conditions";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($criteria);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function update(string $table, array $data, array $criteria = []): bool
    {
        $setClause = implode(", ", array_map(fn ($key) => "$key = :$key", array_keys($data)));
        $conditions = implode(" AND ", array_map(fn ($key) => "$key = :$key", array_keys($criteria)));

        $sql = "UPDATE $table SET $setClause WHERE $conditions";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_merge($data, $criteria));

        return $stmt->rowCount();
    }
    public function delete(string $table, array $criteria): int
    {
        $conditions = implode(" AND ", array_map(fn ($key) => "$key = :$key", array_keys($criteria)));

        $sql = "DELETE FROM $table WHERE $conditions";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($criteria);

        return $stmt->rowCount();
    }

    private function connect(): void {
        $this->pdo = new PDO("mysql:host=$this->hostname;dbname=$this->database", $this->username, $this->password);
    }
}

$db = new Database("localhost", "root", "", "ecommerce v2");
