<?php

class Cart
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function add($user_id, $product_id, $quantity = 1)
    {
        $sql = "
            INSERT INTO cart_items (user_id, product_id, quantity)
            VALUES (:user_id, :product_id, :quantity)
            ON CONFLICT (user_id, product_id)
            DO UPDATE SET quantity = cart_items.quantity + EXCLUDED.quantity
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':user_id' => $user_id,
            ':product_id' => $product_id,
            ':quantity' => $quantity
        ]);
    }

    public function getItems($user_id)
    {
        $sql = "
            SELECT
                cart_items.id AS cart_item_id,
                cart_items.quantity,
                products.id AS product_id,
                products.name,
                products.price,
                products.image,
                products.stock
            FROM cart_items
            INNER JOIN products
                ON cart_items.product_id = products.id
            WHERE cart_items.user_id = :user_id
            ORDER BY cart_items.created_at DESC
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':user_id' => $user_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateQuantity($user_id, $product_id, $quantity)
    {
        if ($quantity <= 0) {
            return $this->remove($user_id, $product_id);
        }

        $sql = "
            UPDATE cart_items
            SET quantity = :quantity
            WHERE user_id = :user_id
            AND product_id = :product_id
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':quantity' => $quantity,
            ':user_id' => $user_id,
            ':product_id' => $product_id
        ]);
    }

    public function remove($user_id, $product_id)
    {
        $sql = "
            DELETE FROM cart_items
            WHERE user_id = :user_id
            AND product_id = :product_id
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':user_id' => $user_id,
            ':product_id' => $product_id
        ]);
    }

    public function clear($user_id)
    {
        $sql = "
            DELETE FROM cart_items
            WHERE user_id = :user_id
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':user_id' => $user_id
        ]);
    }

    public function count($user_id)
    {
        $sql = "
            SELECT COALESCE(SUM(quantity), 0) AS total
            FROM cart_items
            WHERE user_id = :user_id
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':user_id' => $user_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
