<?php

require_once __DIR__ . '/../models/Product.php';

class CartController
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function add()
    {
        $product_id = $_POST['product_id'] ?? null;

        if (!$product_id) {
            die("Product not found.");
        }

        $productModel = new Product($this->db);
        $product = $productModel->findById($product_id);

        if (!$product) {
            die("Product not found.");
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$product_id])) {

            if ($_SESSION['cart'][$product_id]['quantity'] + 1 > $product['stock']) {

                $_SESSION['error'] = "Not enough stock available.";

                header("Location: /public/index.php?page=product&id=" . $product_id);
                exit;
            }

            $_SESSION['cart'][$product_id]['quantity']++;
        } else {

            if ($product['stock'] <= 0) {

                $_SESSION['error'] = "This product is out of stock.";

                header("Location: /public/index.php?page=product&id=" . $product_id);
                exit;
            }

            $_SESSION['cart'][$product_id] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => 1
            ];
        }

        if (
            isset($_POST['ajax']) &&
            $_POST['ajax'] === '1'
        ) {
            $count = 0;

            foreach ($_SESSION['cart'] as $item) {
                $count += $item['quantity'];
            }

            echo json_encode([
                'success' => true,
                'message' => 'Product added to cart.',
                'count' => $count
            ]);
            exit;
        }

        header("Location: /public/index.php?page=cart");
        exit;
    }

    public function remove()
    {
        $product_id = $_POST['product_id'] ?? null;

        if ($product_id && isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }

        header("Location: /public/index.php?page=cart");
        exit;
    }

    public function view()
    {
        require_once __DIR__ . '/../views/cart/index.php';
    }

    public function update()
    {
        $quantities = $_POST['quantities'] ?? [];

        $productModel = new Product($this->db);

        foreach ($quantities as $product_id => $quantity) {

            $quantity = (int) $quantity;

            if ($quantity <= 0) {
                unset($_SESSION['cart'][$product_id]);
                continue;
            }

            $product = $productModel->findById($product_id);

            if (!$product) {
                unset($_SESSION['cart'][$product_id]);
                continue;
            }

            if ($quantity > $product['stock']) {
                $_SESSION['error'] = "Only " . $product['stock'] . " item(s) available for " . $product['name'] . ".";
                $_SESSION['cart'][$product_id]['quantity'] = $product['stock'];
            } else {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            }
        }

        header("Location: /public/index.php?page=cart");
        exit;
    }

    public function count()
    {
        $count = 0;

        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $count += $item['quantity'];
            }
        }

        echo json_encode([
            'count' => $count
        ]);
    }
}
