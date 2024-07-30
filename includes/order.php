<?php

require_once "init.php";
require_once "db.php";
require_once "product.php";

class Order_Details
{
    public int $order_id;
    public Product $product;
    public int $quantity;
    public int $price;

    public function __construct(int $order_id, Product $product, int $quantity, int $price)
    {
        $this->order_id = $order_id;
        $this->product = $product;
        $this->quantity = $quantity;
        $this->price = $price;
    }
}

class Order
{
    private Database $db;
    public int $id;
    public int $user_id;
    public string $status;
    public DateTime $created_at;

    public array $order_details;

    public function __construct(Database $db, int $id, int $user_id, string $status, string $created_at)
    {
        $this->db = $db;
        $this->id = $id;
        $this->user_id = $user_id;
        $this->status = $status;
        $this->created_at = DateTime::createFromFormat("Y-m-d H:i:s", $created_at);

        $res = $this->db->select("order_details", ["order_id" => $this->id]);
        foreach ($res as $row) {
            $this->order_details[] = new Order_Details($row["order_id"], Product::getById($this->db, $row["product_id"]), $row["quantity"], $row["price"]);
        }
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->order_details as $order_detail) {
            $total += $order_detail->price;
        }

        return $total;
    }

    public function update(int $user_id, string $status): array
    {
        $this->user_id = $user_id;
        $this->status = $status;

        $this->db->update(
            "orders",
            [
                "user_id" => $this->user_id,
                "status" => $this->status
            ],
            ["id" => $this->id]
        );

        return [
            "success" => true,
            "message" => "Order updated."
        ];
    }
    public function addOrderDetail(Product $product, int $quantity): array
    {
        $order_products = [];
        if (!empty($this->order_details)) {
            foreach ($this->order_details as $order_detail) {
                $order_products[] = $order_detail->product;
            }
        }

        if ((array_search($product, $order_products)) !== false) {
            return ["success" => false, "message" => "Order already has that product."];
        }

        $this->db->insert(
            "order_details",
            [
                "order_id" => $this->id,
                "product_id" => $product->id,
                "quantity" => $quantity,
                "price" => $product->price
            ]
        );

        $order_detail = new Order_Details($this->id, $product, $quantity, $product->price);
        $this->order_details[] = $order_detail;

        return ["success" => $order_detail, "message" => "Order detail successfully added."];
    }
    public function removeOrderDetail(Product $product): void
    {
        $this->db->delete("order_details", ["order_id" => $this->id, "product_id" => $product->id]);

        $order_products = [];
        foreach ($this->order_details as $order_detail) {
            $order_products[] = $order_detail->product;
        }

        if (($key = array_search($product, $order_products)) !== false) {
            unset($this->order_details[$key]);
        }
    }
    public function updateOrderDetail(int $oldProductId, int $newProductId, int $quantity, int $price): void
    {
        $this->db->update("order_details", ["product_id" => $newProductId, "quantity" => $quantity, "price" => $price], ["order_id" => $this->id, "product_id" => $oldProductId]);
    }

    public static function create(Database $db, int $user_id, string $status): array
    {
        $db->insert(
            "orders",
            [
                "user_id" => $user_id,
                "status" => $status
            ]
        );

        return [
            "success" => true,
            "message" => "Order created."
        ];
    }
    public static function delete(Database $db, int $id): array
    {
        $db->delete("orders", ["id" => $id]);

        return [
            "success" => true,
            "message" => "Order deleted."
        ];
    }

    public static function getById(Database $db, int $id): Order
    {
        $row = $db->select("orders", ["id" => $id])[0];
        return new Order($db, $row["id"], $row["user_id"], $row["status"], $row["created_at"]);
    }
    public static function getAll(Database $db): array
    {
        $orders = [];
        foreach ($db->select("orders") as $row) {
            $orders[] = new Order($db, $row["id"], $row["user_id"], $row["status"], $row["created_at"]);
        }
        return $orders;
    }
    public static function getAllByUser(Database $db, $user_id): array
    {
        $orders = [];
        foreach ($db->select("orders", ["user_id" => $user_id]) as $row) {
            $orders[] = new Order($db, $row["id"], $row["user_id"], $row["status"], $row["created_at"]);
        }

        return $orders;
    }
}
