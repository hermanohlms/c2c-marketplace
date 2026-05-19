<?php

require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/User.php';

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

        $categoryModel = new Category($this->db);
        $categories = $categoryModel->getAll();

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

        $productModel = new Product($this->db);
        $products = $productModel->getAllForAdmin();

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

        $updated = $productModel->updateStatus($product_id, $status);

        if ($updated) {
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

        $userModel = new User($this->db);
        $users = $userModel->getAll();

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

        header("Location: /public/index.php?page=admin-users");
        exit;
    }
}
