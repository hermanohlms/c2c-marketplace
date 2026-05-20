<?php

class Product
{

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create(
        $seller_id,
        $category_id,
        $name,
        $description,
        $price,
        $stock,
        $image
    ) {

        $sql = "
            INSERT INTO products
            (
                seller_id,
                category_id,
                name,
                description,
                price,
                stock,
                image
            )
            VALUES
            (
                :seller_id,
                :category_id,
                :name,
                :description,
                :price,
                :stock,
                :image
            )
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':seller_id' => $seller_id,
            ':category_id' => $category_id,
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':stock' => $stock,
            ':image' => $image
        ]);
    }

    public function getCategories()
    {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBySeller($seller_id)
    {
        $sql = "
        SELECT products.*, categories.name AS category_name
        FROM products
        LEFT JOIN categories ON products.category_id = categories.id
        WHERE products.seller_id = :seller_id
        ORDER BY products.created_at DESC
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':seller_id' => $seller_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllActive()
    {
        $sql = "
        SELECT products.*, categories.name AS category_name, users.name AS seller_name
        FROM products
        LEFT JOIN categories ON products.category_id = categories.id
        LEFT JOIN users ON products.seller_id = users.id
        WHERE products.status = 'active'
        ORDER BY products.created_at DESC
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $sql = "
                SELECT 
                products.*, 
                categories.name AS category_name, 
                users.name AS seller_name,
                COALESCE(AVG(reviews.rating), 0) AS average_rating,
                COUNT(reviews.id) AS review_count
            FROM products
            LEFT JOIN categories ON products.category_id = categories.id
            LEFT JOIN users ON products.seller_id = users.id
            LEFT JOIN reviews ON products.id = reviews.product_id
            WHERE products.id = :id
            GROUP BY products.id, categories.name, users.name
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function reduceStock($product_id, $quantity)
    {
        $sql = "
        UPDATE products
        SET stock = stock - :quantity
        WHERE id = :product_id
        AND stock >= :quantity
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':product_id' => $product_id,
            ':quantity' => $quantity
        ]);

        return $stmt->rowCount() > 0;
    }

    public function searchAndFilter($search, $category_id, $sort)
    {
        $sql = "
                SELECT 
                products.*, 
                categories.name AS category_name, 
                users.name AS seller_name,
                COALESCE(AVG(reviews.rating), 0) AS average_rating,
                COUNT(reviews.id) AS review_count
            FROM products
            LEFT JOIN categories ON products.category_id = categories.id
            LEFT JOIN users ON products.seller_id = users.id
            LEFT JOIN reviews ON products.id = reviews.product_id
            WHERE products.status = 'active'
        ";

        $params = [];

        if (!empty($search)) {
            $sql .= " AND (
            LOWER(products.name) LIKE LOWER(:search)
            OR LOWER(products.description) LIKE LOWER(:search)
        )";

            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($category_id)) {
            $sql .= " AND products.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }

        $sql .= "
            GROUP BY products.id, categories.name, users.name
        ";

        if ($sort === 'price_low') {
            $sql .= " ORDER BY products.price ASC";
        } elseif ($sort === 'price_high') {
            $sql .= " ORDER BY products.price DESC";
        } elseif ($sort === 'newest') {
            $sql .= " ORDER BY products.created_at DESC";
        } else {
            $sql .= " ORDER BY products.created_at DESC";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllForAdmin()
    {
        $sql = "
        SELECT 
            products.*,
            categories.name AS category_name,
            users.name AS seller_name,
            users.email AS seller_email
        FROM products
        LEFT JOIN categories ON products.category_id = categories.id
        LEFT JOIN users ON products.seller_id = users.id
        ORDER BY products.created_at DESC
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($product_id, $status)
    {
        $allowedStatuses = ['active', 'inactive'];

        if (!in_array($status, $allowedStatuses)) {
            return false;
        }

        $sql = "
        UPDATE products
        SET status = :status
        WHERE id = :product_id
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':status' => $status,
            ':product_id' => $product_id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function countActiveBySeller($seller_id)
    {
        $sql = "
        SELECT COUNT(*) AS total
        FROM products
        WHERE seller_id = :seller_id
        AND status = 'active'
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':seller_id' => $seller_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getLowStockBySeller($seller_id)
    {
        $sql = "
        SELECT *
        FROM products
        WHERE seller_id = :seller_id
        AND stock <= 5
        ORDER BY stock ASC
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':seller_id' => $seller_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveBySeller($seller_id)
    {
        $sql = "
        SELECT 
            products.*,
            categories.name AS category_name,
            COALESCE(AVG(reviews.rating), 0) AS average_rating,
            COUNT(reviews.id) AS review_count
        FROM products
        LEFT JOIN categories ON products.category_id = categories.id
        LEFT JOIN reviews ON products.id = reviews.product_id
        WHERE products.seller_id = :seller_id
        AND products.status = 'active'
        GROUP BY products.id, categories.name
        ORDER BY products.created_at DESC
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':seller_id' => $seller_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
