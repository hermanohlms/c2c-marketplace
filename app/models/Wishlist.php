<?php

class Wishlist
{

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function add($user_id, $product_id)
    {
        $sql = "
            INSERT INTO wishlists (user_id, product_id)
            VALUES (:user_id, :product_id)
            ON CONFLICT (user_id, product_id) DO NOTHING
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':user_id' => $user_id,
            ':product_id' => $product_id
        ]);
    }

    public function remove($user_id, $product_id)
    {
        $sql = "
            DELETE FROM wishlists
            WHERE user_id = :user_id
            AND product_id = :product_id
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':user_id' => $user_id,
            ':product_id' => $product_id
        ]);
    }

    public function getByUser($user_id)
    {
        $sql = "
            SELECT 
                wishlists.*,
                products.name,
                products.price,
                products.image,
                products.stock,
                products.status,
                categories.name AS category_name
            FROM wishlists
            INNER JOIN products ON wishlists.product_id = products.id
            LEFT JOIN categories ON products.category_id = categories.id
            WHERE wishlists.user_id = :user_id
            ORDER BY wishlists.created_at DESC
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':user_id' => $user_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function exists($user_id, $product_id)
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM wishlists
            WHERE user_id = :user_id
            AND product_id = :product_id
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':user_id' => $user_id,
            ':product_id' => $product_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }
}
