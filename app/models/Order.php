<?php

class Order
{

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($buyer_id, $total_amount)
    {
        $sql = "
            INSERT INTO orders (buyer_id, total_amount, status)
            VALUES (:buyer_id, :total_amount, 'pending')
            RETURNING id
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':buyer_id' => $buyer_id,
            ':total_amount' => $total_amount
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    }

    public function addItem($order_id, $product_id, $quantity, $price)
    {
        $sql = "
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (:order_id, :product_id, :quantity, :price)
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':order_id' => $order_id,
            ':product_id' => $product_id,
            ':quantity' => $quantity,
            ':price' => $price
        ]);
    }

    public function getByBuyer($buyer_id)
    {
        $sql = "
        SELECT *
        FROM orders
        WHERE buyer_id = :buyer_id
        AND status != 'pending'
        ORDER BY created_at DESC
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':buyer_id' => $buyer_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getItemsByOrder($order_id)
    {
        $sql = "
        SELECT 
            order_items.*,
            products.name AS product_name,
            products.image AS product_image
        FROM order_items
        LEFT JOIN products ON order_items.product_id = products.id
        WHERE order_items.order_id = :order_id
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':order_id' => $order_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBySeller($seller_id)
    {
        $sql = "
        SELECT 
            orders.id AS order_id,
            orders.status,
            orders.created_at,
            users.name AS buyer_name,
            users.email AS buyer_email,
            order_items.quantity,
            order_items.price,
            products.name AS product_name,
            products.image AS product_image
        FROM order_items
        INNER JOIN orders ON order_items.order_id = orders.id
        INNER JOIN products ON order_items.product_id = products.id
        INNER JOIN users ON orders.buyer_id = users.id
        WHERE products.seller_id = :seller_id
        ORDER BY orders.created_at DESC
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':seller_id' => $seller_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatusForSeller($order_id, $seller_id, $status)
    {
        $allowedStatuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($status, $allowedStatuses)) {
            return false;
        }

        $sql = "
        UPDATE orders
        SET status = :status
        WHERE id = :order_id
        AND id IN (
            SELECT order_items.order_id
            FROM order_items
            INNER JOIN products ON order_items.product_id = products.id
            WHERE products.seller_id = :seller_id
        )
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':status' => $status,
            ':order_id' => $order_id,
            ':seller_id' => $seller_id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function getSellerAnalytics($seller_id)
    {
        $sql = "
        SELECT
            COALESCE(SUM(order_items.quantity * order_items.price), 0) AS total_revenue,
            COUNT(DISTINCT orders.id) AS total_orders,
            COALESCE(SUM(order_items.quantity), 0) AS total_items_sold
        FROM order_items
        INNER JOIN orders ON order_items.order_id = orders.id
        INNER JOIN products ON order_items.product_id = products.id
        WHERE products.seller_id = :seller_id
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':seller_id' => $seller_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTopSellingProducts($seller_id)
    {
        $sql = "
        SELECT
            products.name,
            products.image,
            SUM(order_items.quantity) AS total_sold,
            SUM(order_items.quantity * order_items.price) AS revenue
        FROM order_items
        INNER JOIN products ON order_items.product_id = products.id
        WHERE products.seller_id = :seller_id
        GROUP BY products.id, products.name, products.image
        ORDER BY total_sold DESC
        LIMIT 5
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':seller_id' => $seller_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBuyerIdByOrder($order_id)
    {
        $sql = "
        SELECT buyer_id
        FROM orders
        WHERE id = :order_id
        LIMIT 1
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':order_id' => $order_id
        ]);

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        return $order ? $order['buyer_id'] : null;
    }

    public function getItemsByOrderId($order_id)
    {
        $sql = "
        SELECT *
        FROM order_items
        WHERE order_id = :order_id
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':order_id' => $order_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
