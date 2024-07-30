<?php

require_once "init.php";
require_once "db.php";

class Product
{
    private Database $db;
    public int $id;
    public string $name;
    public string $description;
    public int $stock;
    public float $price;

    public function __construct(Database $db, int $id, string $name, string $description, int $stock, float $price)
    {
        $this->db = $db;
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->stock = $stock;
        $this->price = $price;
    }

    public function update(string $name, string $description, int $stock, float $price): array
    {
        $product = Product::getByName($this->db, $name);

        if ($product) {
            return [
                "success" => false,
                "message" => "Products with name duplicates are not allowed."
            ];
        }

        $this->name = $name;
        $this->description = $description;
        $this->stock = $stock;
        $this->price = $price;

        $this->db->update(
            "products", 
            [
                "name" => $this->name, 
                "description" => $this->description, 
                "stock" => $this->stock, 
                "price" => $this->price
            ],
            ["id" => $this->id]
        );

        return [
            "success" => true,
            "message" => "Product updated."
        ];
    }

    public static function create(Database $db, string $name, string $description, int $stock, float $price): array
    {
        $product = Product::getByName($db, $name);

        if ($product) {
            return [
                "success" => false,
                "message" => "Products with name duplicates are not allowed."
            ];
        }

        $db->insert(
            "products", 
            [
                "name" => $name, 
                "description" => $description, 
                "stock" => $stock, 
                "price" => $price
            ]
        );

        return [
            "success" => true,
            "message" => "Product created."
        ];
    }
    public static function delete(Database $db, int $id): array {
        $db->delete("products", ["id" => $id]);

        return [
            "success" => true,
            "message" => "Product deleted."
        ];
    }

    public static function getById(Database $db, int $id): ?Product
    {
        $rows = $db->select("products", ["id" => $id]);

        if (empty($rows))
            return null;

        $row = $rows[0];
        return new Product($db, $row["id"], $row["name"], $row["description"], $row["stock"], $row["price"]);
    }
    public static function getByName(Database $db, string $name): ?Product
    {
        $rows = $db->select("products", ["name" => $name]);

        if (empty($rows))
            return null;

        $row = $rows[0];
        return new Product($db, $row["id"], $row["name"], $row["description"], $row["stock"], $row["price"]);
    }

    public static function getAll(Database $db): array {
        $products = [];
        foreach ($db->select("products") as $row) {
            $products[] = new Product($db, $row["id"], $row["name"], $row["description"], $row["stock"], $row["price"]);
        }

        return $products;        
    }
}