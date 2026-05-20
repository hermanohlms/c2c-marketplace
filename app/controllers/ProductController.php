<?php

require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';

class ProductController
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $seller_id = $_SESSION['user_id'];

            $category_id = $_POST['category_id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $stock = $_POST['stock'];

            $image = $_FILES['image']['name'];

            move_uploaded_file(
                $_FILES['image']['tmp_name'],
                __DIR__ . '/../../public/uploads/' . $image
            );

            $productModel = new Product($this->db);

            $created = $productModel->create(
                $seller_id,
                $category_id,
                $name,
                $description,
                $price,
                $stock,
                $image
            );

            if ($created) {

                echo "Product created successfully!";
            } else {

                echo "Failed to create product.";
            }
        }
    }

    public function showAddProductForm()
    {
        $productModel = new Product($this->db);
        $categories = $productModel->getCategories();

        require_once __DIR__ . '/../views/seller/add-product.php';
    }

    public function myProducts()
    {
        $seller_id = $_SESSION['user_id'];

        $productModel = new Product($this->db);
        $products = $productModel->getBySeller($seller_id);

        require_once __DIR__ . '/../views/seller/my-products.php';
    }

    public function shop()
    {
        $search = $_GET['search'] ?? '';
        $category_id = $_GET['category_id'] ?? '';
        $sort = $_GET['sort'] ?? 'newest';

        $productModel = new Product($this->db);

        $products = $productModel->searchAndFilter(
            $search,
            $category_id,
            $sort
        );

        $categories = $productModel->getCategories();

        require_once __DIR__ . '/../views/shop/index.php';
    }

    public function show()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            die("Product not found.");
        }

        $productModel = new Product($this->db);
        $product = $productModel->findById($id);

        if (!$product) {
            die("Product not found.");
        }

        $reviewModel = new Review($this->db);
        $reviews = $reviewModel->getByProduct($id);
        $ratingSummary = $reviewModel->getAverageRating($id);

        require_once __DIR__ . '/../views/shop/show.php';
    }

    public function updateStock()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
            $_SESSION['error'] = "Seller access only.";
            header("Location: /public/index.php?page=shop");
            exit;
        }

        $product_id = $_POST['product_id'] ?? null;
        $stock = $_POST['stock'] ?? null;

        if (!$product_id || $stock === null || $stock < 0) {
            $_SESSION['error'] = "Invalid stock amount.";
            header("Location: /public/index.php?page=my-products");
            exit;
        }

        $productModel = new Product($this->db);

        $updated = $productModel->updateStockForSeller(
            $product_id,
            $_SESSION['user_id'],
            (int)$stock
        );

        if ($updated) {
            $_SESSION['success'] = "Stock updated successfully.";
        } else {
            $_SESSION['error'] = "Could not update stock.";
        }

        header("Location: /public/index.php?page=my-products");
        exit;
    }

    public function updateSellerProductStatus()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
            $_SESSION['error'] = "Seller access only.";
            header("Location: /public/index.php?page=shop");
            exit;
        }

        $product_id = $_POST['product_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$product_id || !$status) {
            $_SESSION['error'] = "Invalid product status.";
            header("Location: /public/index.php?page=my-products");
            exit;
        }

        $productModel = new Product($this->db);

        $updated = $productModel->updateStatusForSeller(
            $product_id,
            $_SESSION['user_id'],
            $status
        );

        if ($updated) {
            $_SESSION['success'] = "Product status updated.";
        } else {
            $_SESSION['error'] = "Could not update product status.";
        }

        header("Location: /public/index.php?page=my-products");
        exit;
    }
}
