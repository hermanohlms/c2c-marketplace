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

            $image = null;

            if (
                isset($_FILES['image']) &&
                $_FILES['image']['error'] === UPLOAD_ERR_OK
            ) {
                $allowedMimeTypes = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                $maxSize = 2 * 1024 * 1024; // 2MB

                if ($_FILES['image']['size'] > $maxSize) {
                    $_SESSION['error'] = "Image must be smaller than 2MB.";
                    header("Location: /public/index.php?page=add-product");
                    exit;
                }

                $mimeType = mime_content_type($_FILES['image']['tmp_name']);

                if (!array_key_exists($mimeType, $allowedMimeTypes)) {
                    $_SESSION['error'] = "Only JPG, PNG, and WEBP images are allowed.";
                    header("Location: /public/index.php?page=add-product");
                    exit;
                }

                $extension = $allowedMimeTypes[$mimeType];

                $image = 'product_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;

                move_uploaded_file(
                    $_FILES['image']['tmp_name'],
                    __DIR__ . '/../../public/uploads/' . $image
                );
            }

            if (!$image) {
                $_SESSION['error'] = "Product image is required.";
                header("Location: /public/index.php?page=add-product");
                exit;
            }

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

        $currentPage = max(1, (int)($_GET['p'] ?? 1));
        $perPage = 12;
        $offset = ($currentPage - 1) * $perPage;

        $totalProducts = $productModel->countSearchAndFilter(
            $search,
            $category_id
        );

        $totalPages = ceil($totalProducts / $perPage);

        $products = $productModel->searchAndFilter(
            $search,
            $category_id,
            $sort,
            $perPage,
            $offset
        );

        $categories = $productModel->getCategories();

        require_once __DIR__ . '/../views/shop/index.php';
    }

    public function show()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            abort404();
        }

        $productModel = new Product($this->db);
        $product = $productModel->findById($id);

        if (!$product) {
            abort404();
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

    public function edit()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
            $_SESSION['error'] = "Seller access only.";
            header("Location: /public/index.php?page=shop");
            exit;
        }

        $product_id = $_GET['id'] ?? null;

        if (!$product_id) {
            $_SESSION['error'] = "Product not found.";
            header("Location: /public/index.php?page=my-products");
            exit;
        }

        $productModel = new Product($this->db);

        $product = $productModel->findByIdForSeller(
            $product_id,
            $_SESSION['user_id']
        );

        if (!$product) {
            $_SESSION['error'] = "Product not found or access denied.";
            header("Location: /public/index.php?page=my-products");
            exit;
        }

        $categories = $productModel->getCategories();

        require_once __DIR__ . '/../views/seller/edit-product.php';
    }

    public function update()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
            $_SESSION['error'] = "Seller access only.";
            header("Location: /public/index.php?page=shop");
            exit;
        }

        $product_id = $_POST['product_id'] ?? null;
        $category_id = $_POST['category_id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = $_POST['price'] ?? null;
        $stock = $_POST['stock'] ?? null;
        $status = $_POST['status'] ?? 'active';

        if (
            !$product_id ||
            !$category_id ||
            $name === '' ||
            $price === null ||
            $stock === null ||
            $price < 0 ||
            $stock < 0
        ) {
            $_SESSION['error'] = "Please complete all required fields correctly.";
            header("Location: /public/index.php?page=edit-product&id=" . $product_id);
            exit;
        }

        $allowedStatuses = ['active', 'inactive'];

        if (!in_array($status, $allowedStatuses)) {
            $_SESSION['error'] = "Invalid product status.";
            header("Location: /public/index.php?page=edit-product&id=" . $product_id);
            exit;
        }

        $image = null;

        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] === UPLOAD_ERR_OK
        ) {
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

            $image = 'product_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;

            move_uploaded_file(
                $_FILES['image']['tmp_name'],
                __DIR__ . '/../../public/uploads/' . $image
            );
        }

        $productModel = new Product($this->db);

        $updated = $productModel->updateForSeller(
            $product_id,
            $_SESSION['user_id'],
            $category_id,
            $name,
            $description,
            $price,
            $stock,
            $status,
            $image
        );

        if ($updated) {
            $_SESSION['success'] = "Product updated successfully.";
        } else {
            $_SESSION['error'] = "No changes were made or update failed.";
        }

        header("Location: /public/index.php?page=my-products");
        exit;
    }
}
