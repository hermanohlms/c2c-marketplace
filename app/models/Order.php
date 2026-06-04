<?php

class Order
{

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create(
        $buyer_id,
        $total_amount,
        $delivery_name,
        $delivery_phone,
        $address_line1,
        $address_line2,
        $city,
        $province,
        $postal_code,
        $shipping_notes
    ) {
        $sql = "
        INSERT INTO orders (
            buyer_id,
            total_amount,
            status,
            delivery_name,
            delivery_phone,
            address_line1,
            address_line2,
            city,
            province,
            postal_code,
            shipping_notes
        )
        VALUES (
            :buyer_id,
            :total_amount,
            'pending',
            :delivery_name,
            :delivery_phone,
            :address_line1,
            :address_line2,
            :city,
            :province,
            :postal_code,
            :shipping_notes
        )
        RETURNING id
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':buyer_id' => $buyer_id,
            ':total_amount' => $total_amount,
            ':delivery_name' => $delivery_name,
            ':delivery_phone' => $delivery_phone,
            ':address_line1' => $address_line1,
            ':address_line2' => $address_line2,
            ':city' => $city,
            ':province' => $province,
            ':postal_code' => $postal_code,
            ':shipping_notes' => $shipping_notes
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    }

    public function addItem($order_id, $product_id, $quantity, $price)
    {
        $sql = "
        INSERT INTO order_items
        (
            order_id,
            product_id,
            quantity,
            price
        )
        VALUES
        (
            :order_id,
            :product_id,
            :quantity,
            :price
        )
        RETURNING id
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':order_id' => $order_id,
            ':product_id' => $product_id,
            ':quantity' => $quantity,
            ':price' => $price
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
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

    public function updateStatusForSeller($order_id, $seller_id, $status, $tracking_number = null)
    {
        $allowedStatuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($status, $allowedStatuses)) {
            return false;
        }

        $tracking_number = trim($tracking_number ?? '');

        if ($tracking_number !== '') {
            $sql = "
            UPDATE orders
            SET status = :status,
                tracking_number = :tracking_number,
                shipped_at = CASE
                    WHEN :status_for_shipped = 'shipped'
                    THEN CURRENT_TIMESTAMP
                    ELSE shipped_at
                END
            WHERE id = :order_id
            AND EXISTS (
                SELECT 1
                FROM order_items
                INNER JOIN products
                    ON order_items.product_id = products.id
                WHERE order_items.order_id = orders.id
                AND products.seller_id = :seller_id
            )
        ";

            $params = [
                ':status' => $status,
                ':status_for_shipped' => $status,
                ':tracking_number' => $tracking_number,
                ':order_id' => $order_id,
                ':seller_id' => $seller_id
            ];
        } else {
            $sql = "
            UPDATE orders
            SET status = :status,
                shipped_at = CASE
                    WHEN :status_for_shipped = 'shipped'
                    THEN CURRENT_TIMESTAMP
                    ELSE shipped_at
                END
            WHERE id = :order_id
            AND EXISTS (
                SELECT 1
                FROM order_items
                INNER JOIN products
                    ON order_items.product_id = products.id
                WHERE order_items.order_id = orders.id
                AND products.seller_id = :seller_id
            )
        ";

            $params = [
                ':status' => $status,
                ':status_for_shipped' => $status,
                ':order_id' => $order_id,
                ':seller_id' => $seller_id
            ];
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount() > 0;
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

    public function markDeliveredByBuyer($order_id, $buyer_id)
    {
        $sql = "
        UPDATE orders
        SET status = 'delivered'
        WHERE id = :order_id
        AND buyer_id = :buyer_id
        AND status = 'shipped'
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':order_id' => $order_id,
            ':buyer_id' => $buyer_id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function countByBuyer($buyer_id)
    {
        $sql = "
        SELECT COUNT(*) AS total
        FROM orders
        WHERE buyer_id = :buyer_id
        AND status != 'pending'
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':buyer_id' => $buyer_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function countBySeller($seller_id)
    {
        $sql = "
        SELECT COUNT(DISTINCT orders.id) AS total
        FROM orders
        INNER JOIN order_items
            ON orders.id = order_items.order_id
        INNER JOIN products
            ON order_items.product_id = products.id
        WHERE products.seller_id = :seller_id
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':seller_id' => $seller_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getByBuyerPaginated($buyer_id, $limit, $offset)
    {
        $sql = "
        SELECT *
        FROM orders
        WHERE buyer_id = :buyer_id
        AND status != 'pending'
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':buyer_id', $buyer_id);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as &$order) {
            $itemsSql = "
            SELECT 
                order_items.*,
                products.name AS product_name,
                products.image AS product_image
            FROM order_items
            INNER JOIN products
                ON order_items.product_id = products.id
            WHERE order_items.order_id = :order_id
        ";

            $itemsStmt = $this->conn->prepare($itemsSql);

            $itemsStmt->execute([
                ':order_id' => $order['id']
            ]);

            $order['items'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $orders;
    }

    public function getBySellerPaginated($seller_id, $limit, $offset)
    {
        $sql = "
        SELECT 
            orders.id AS order_id,
            orders.status,
            orders.created_at,
            orders.delivery_name,
            orders.delivery_phone,
            orders.address_line1,
            orders.address_line2,
            orders.city,
            orders.province,
            orders.postal_code,
            orders.shipping_notes,
            orders.tracking_number,

            users.name AS buyer_name,
            users.email AS buyer_email,

            products.name AS product_name,
            products.image AS product_image,
            order_items.quantity,
            order_items.price

        FROM orders
        INNER JOIN users 
            ON orders.buyer_id = users.id
        INNER JOIN order_items 
            ON orders.id = order_items.order_id
        INNER JOIN products 
            ON order_items.product_id = products.id

        WHERE products.seller_id = :seller_id

        ORDER BY orders.created_at DESC
        LIMIT :limit OFFSET :offset
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':seller_id', $seller_id);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatusAndTracking($order_id, $status, $tracking_number = null)
    {
        $sql = "
        UPDATE orders
        SET status = :status,
            tracking_number = CASE
                WHEN :tracking_number IS NOT NULL AND :tracking_number != ''
                THEN :tracking_number
                ELSE tracking_number
            END,
            shipped_at = CASE
                WHEN :status = 'shipped' THEN CURRENT_TIMESTAMP
                ELSE shipped_at
            END
        WHERE id = :id
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':status' => $status,
            ':tracking_number' => $tracking_number,
            ':id' => $order_id
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
        INNER JOIN orders
            ON order_items.order_id = orders.id
        INNER JOIN products
            ON order_items.product_id = products.id
        WHERE products.seller_id = :seller_id
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':seller_id' => $seller_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSellerSalesCount($seller_id)
    {
        $sql = "
        SELECT COALESCE(SUM(order_items.quantity), 0) AS total_sales
        FROM order_items
        INNER JOIN products
            ON order_items.product_id = products.id
        INNER JOIN orders
            ON order_items.order_id = orders.id
        WHERE products.seller_id = :seller_id
        AND orders.status IN ('paid', 'shipped', 'delivered')
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':seller_id' => $seller_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'];
    }
}
