<?php

require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Notification.php';

class AdminController
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    private function requireAdmin()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please login first.";
            header("Location: /public/index.php?page=login");
            exit;
        }

        if ($_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = "Admin access only.";
            header("Location: /public/index.php?page=shop");
            exit;
        }
    }

    public function dashboard()
    {
        $this->requireAdmin();

        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    public function categories()
    {
        $this->requireAdmin();

        $search = trim($_GET['search'] ?? '');

        $currentPage = max(1, (int)($_GET['p'] ?? 1));
        $perPage = 6;
        $offset = ($currentPage - 1) * $perPage;

        $whereSql = "";
        $params = [];

        if ($search !== '') {
            $whereSql = "
            WHERE LOWER(name) LIKE LOWER(:search)
            OR LOWER(description) LIKE LOWER(:search)
        ";

            $params[':search'] = '%' . $search . '%';
        }

        $countStmt = $this->db->prepare("
        SELECT COUNT(*) AS total
        FROM categories
        $whereSql
    ");

        $countStmt->execute($params);

        $totalCategories = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totalCategories / $perPage);

        $stmt = $this->db->prepare("
        SELECT *
        FROM categories
        $whereSql
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ");

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/admin/categories.php';
    }

    public function createCategory()
    {
        $this->requireAdmin();

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '') {
            $_SESSION['error'] = "Category name is required.";
            header("Location: /public/index.php?page=admin-categories");
            exit;
        }

        $categoryModel = new Category($this->db);

        try {
            $categoryModel->create($name, $description);
            $_SESSION['success'] = "Category created successfully.";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Category could not be created. It may already exist.";
        }

        header("Location: /public/index.php?page=admin-categories");
        exit;
    }

    public function products()
    {
        $this->requireAdmin();

        $search = trim($_GET['search'] ?? '');

        $currentPage = max(1, (int)($_GET['p'] ?? 1));
        $perPage = 6;
        $offset = ($currentPage - 1) * $perPage;

        $whereSql = "";
        $params = [];

        if ($search !== '') {
            $whereSql = "
            WHERE LOWER(products.name) LIKE LOWER(:search)
            OR LOWER(categories.name) LIKE LOWER(:search)
            OR LOWER(users.name) LIKE LOWER(:search)
            OR LOWER(users.email) LIKE LOWER(:search)
        ";

            $params[':search'] = '%' . $search . '%';
        }

        $countStmt = $this->db->prepare("
        SELECT COUNT(*) AS total
        FROM products
        INNER JOIN users ON products.seller_id = users.id
        LEFT JOIN categories ON products.category_id = categories.id
        $whereSql
    ");

        $countStmt->execute($params);

        $totalProducts = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totalProducts / $perPage);

        $stmt = $this->db->prepare("
        SELECT 
            products.*,
            users.name AS seller_name,
            users.email AS seller_email,
            categories.name AS category_name
        FROM products
        INNER JOIN users ON products.seller_id = users.id
        LEFT JOIN categories ON products.category_id = categories.id
        $whereSql
        ORDER BY products.created_at DESC
        LIMIT :limit OFFSET :offset
    ");

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/admin/products.php';
    }

    public function updateProductStatus()
    {
        $this->requireAdmin();

        $product_id = $_POST['product_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$product_id || !$status) {
            $_SESSION['error'] = "Invalid product update.";
            header("Location: /public/index.php?page=admin-products");
            exit;
        }

        $productModel = new Product($this->db);

        $productBeforeUpdate = $productModel->findBasicById($product_id);

        $updated = $productModel->updateStatus($product_id, $status);

        if ($updated) {

            $notificationModel = new Notification($this->db);

            if ($productBeforeUpdate && $productBeforeUpdate['seller_id']) {
                $notificationModel->create(
                    $productBeforeUpdate['seller_id'],
                    'Product status changed',
                    'Your product "' . $productBeforeUpdate['name'] . '" was marked as ' . $status . ' by admin.',
                    'admin'
                );
            }

            $_SESSION['success'] = "Product status updated.";
        } else {

            $_SESSION['error'] = "Could not update product status.";
        }

        header("Location: /public/index.php?page=admin-products");
        exit;
    }

    public function users()
    {
        $this->requireAdmin();

        $search = trim($_GET['search'] ?? '');

        $currentPage = max(1, (int)($_GET['p'] ?? 1));
        $perPage = 6;
        $offset = ($currentPage - 1) * $perPage;

        $whereSql = "";
        $params = [];

        if ($search !== '') {
            $whereSql = "
            WHERE LOWER(name) LIKE LOWER(:search)
            OR LOWER(email) LIKE LOWER(:search)
        ";

            $params[':search'] = '%' . $search . '%';
        }

        $countStmt = $this->db->prepare("
        SELECT COUNT(*) AS total
        FROM users
        $whereSql
    ");

        $countStmt->execute($params);

        $totalUsers = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totalUsers / $perPage);

        $stmt = $this->db->prepare("
        SELECT *
        FROM users
        $whereSql
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ");

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/admin/users.php';
    }

    public function updateUser()
    {
        $this->requireAdmin();

        $user_id = $_POST['user_id'] ?? null;
        $role = $_POST['role'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$user_id || !$role || !$status) {
            $_SESSION['error'] = "Invalid user update.";
            header("Location: /public/index.php?page=admin-users");
            exit;
        }

        if ($user_id == $_SESSION['user_id'] && $status === 'inactive') {
            $_SESSION['error'] = "You cannot deactivate your own admin account.";
            header("Location: /public/index.php?page=admin-users");
            exit;
        }

        $userModel = new User($this->db);

        $userModel->updateRole($user_id, $role);
        $userModel->updateStatus($user_id, $status);

        $_SESSION['success'] = "User updated successfully.";

        $notificationModel = new Notification($this->db);

        $notificationModel->create(
            $_SESSION['user_id'],
            'User updated',
            'You updated a user account successfully.',
            'admin'
        );

        header("Location: /public/index.php?page=admin-users");
        exit;
    }

    public function updateCategory()
    {
        $this->requireAdmin();

        $category_id = $_POST['category_id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!$category_id || $name === '') {
            $_SESSION['error'] = "Category name is required.";
            header("Location: /public/index.php?page=admin-categories");
            exit;
        }

        $stmt = $this->db->prepare("
        UPDATE categories
        SET name = :name,
            description = :description
        WHERE id = :id
    ");

        $updated = $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':id' => $category_id
        ]);

        $_SESSION[$updated ? 'success' : 'error'] =
            $updated ? "Category updated." : "Could not update category.";

        header("Location: /public/index.php?page=admin-categories");
        exit;
    }

    public function deleteCategory()
    {
        $this->requireAdmin();

        $category_id = $_POST['category_id'] ?? null;

        if (!$category_id) {
            $_SESSION['error'] = "Invalid category.";
            header("Location: /public/index.php?page=admin-categories");
            exit;
        }

        $stmt = $this->db->prepare("
        DELETE FROM categories
        WHERE id = :id
    ");

        $deleted = $stmt->execute([
            ':id' => $category_id
        ]);

        $_SESSION[$deleted ? 'success' : 'error'] =
            $deleted ? "Category deleted." : "Could not delete category.";

        header("Location: /public/index.php?page=admin-categories");
        exit;
    }

    public function tickets()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            abort403();
        }

        $stmt = $this->db->query("
        SELECT support_tickets.*, users.name AS user_name, users.email
        FROM support_tickets
        JOIN users ON support_tickets.user_id = users.id
        ORDER BY support_tickets.created_at DESC
    ");

        $tickets = $stmt->fetchAll();

        require_once __DIR__ . '/../views/admin/tickets.php';
    }
}
