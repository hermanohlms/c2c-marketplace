<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';

class SellerStoreController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function show()
    {
        $seller_id = $_GET['id'] ?? null;

        if (!$seller_id) {
            abort404();
        }

        $userModel = new User($this->db);
        $productModel = new Product($this->db);
        $orderModel = new Order($this->db);

        $seller = $userModel->findSellerById($seller_id);

        if (!$seller) {
            abort404();
        }

        $products = $productModel->getActiveBySeller($seller_id);
        $rating = $productModel->getSellerRating($seller_id);
        $sales = $orderModel->getSellerSalesCount($seller_id);

        require_once __DIR__ . '/../views/seller-store/show.php';
    }

    public function updateDescription()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
            $_SESSION['error'] = "Seller access only.";
            header("Location: /public/index.php?page=shop");
            exit;
        }

        $description = trim($_POST['store_description'] ?? '');

        $userModel = new User($this->db);

        $updated = $userModel->updateStoreDescription(
            $_SESSION['user_id'],
            $description
        );

        $_SESSION[$updated ? 'success' : 'error'] =
            $updated ? "Store description updated." : "Could not update store description.";

        header("Location: /public/index.php?page=seller&id=" . $_SESSION['user_id']);
        exit;
    }
}
