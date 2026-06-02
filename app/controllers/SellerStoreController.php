<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Product.php';

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

        $seller = $userModel->findSellerById($seller_id);

        if (!$seller) {
            abort404();
        }

        $products = $productModel->getActiveBySeller($seller_id);

        require_once __DIR__ . '/../views/seller-store/show.php';
    }
}
