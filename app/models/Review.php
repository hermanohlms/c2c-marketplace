<?php

class Review
{

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function hasPurchasedProduct($buyer_id, $product_id)
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM orders
            INNER JOIN order_items ON orders.id = order_items.order_id
            WHERE orders.buyer_id = :buyer_id
            AND order_items.product_id = :product_id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':buyer_id' => $buyer_id,
            ':product_id' => $product_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }

    public function hasReviewed($user_id, $product_id)
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM reviews
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

    public function create($product_id, $user_id, $rating, $comment)
    {
        $sql = "
            INSERT INTO reviews (product_id, user_id, rating, comment)
            VALUES (:product_id, :user_id, :rating, :comment)
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':product_id' => $product_id,
            ':user_id' => $user_id,
            ':rating' => $rating,
            ':comment' => $comment
        ]);
    }

    public function getByProduct($product_id)
    {
        $sql = "
            SELECT reviews.*, users.name AS reviewer_name
            FROM reviews
            INNER JOIN users ON reviews.user_id = users.id
            WHERE reviews.product_id = :product_id
            ORDER BY reviews.created_at DESC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':product_id' => $product_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAverageRating($product_id)
    {
        $sql = "
            SELECT AVG(rating) AS average_rating, COUNT(*) AS total_reviews
            FROM reviews
            WHERE product_id = :product_id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':product_id' => $product_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
