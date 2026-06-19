<?php

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Cart.php';

class CartController
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    private function requireBuyer()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please login first.";
            header("Location: /index.php?page=login");
            exit;
        }

        if ($_SESSION['user_role'] !== 'buyer') {
            $_SESSION['error'] = "Buyer access only.";
            header("Location: /index.php?page=shop");
            exit;
        }
    }

    public function add()
    {
        $this->requireBuyer();

        $product_id = $_POST['product_id'] ?? null;
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));

        if (!$product_id) {
            $_SESSION['error'] = "Invalid product.";
            header("Location: /index.php?page=shop");
            exit;
        }

        $cartModel = new Cart($this->db);
        $cartModel->add($_SESSION['user_id'], $product_id, $quantity);

        $_SESSION['success'] = "Product added to cart.";
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/index.php?page=cart'));
        exit;
    }

    public function view()
    {
        $this->requireBuyer();

        $cartModel = new Cart($this->db);
        $cartItems = $cartModel->getItems($_SESSION['user_id']);

        $total = 0;

        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        require_once __DIR__ . '/../views/cart/index.php';
    }

    public function update()
    {
        $this->requireBuyer();

        $product_id = $_POST['product_id'] ?? null;
        $quantity = (int)($_POST['quantity'] ?? 1);

        if (!$product_id) {
            $_SESSION['error'] = "Invalid cart item.";
            header("Location: /index.php?page=cart");
            exit;
        }

        $cartModel = new Cart($this->db);
        $cartModel->updateQuantity($_SESSION['user_id'], $product_id, $quantity);

        $_SESSION['success'] = "Cart updated.";
        header("Location: /index.php?page=cart");
        exit;
    }

    public function remove()
    {
        $this->requireBuyer();

        $product_id = $_POST['product_id'] ?? null;

        if (!$product_id) {
            $_SESSION['error'] = "Invalid cart item.";
            header("Location: /index.php?page=cart");
            exit;
        }

        $cartModel = new Cart($this->db);
        $cartModel->remove($_SESSION['user_id'], $product_id);

        $_SESSION['success'] = "Product removed from cart.";
        header("Location: /index.php?page=cart");
        exit;
    }

    public function count()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'buyer') {
            echo json_encode(['count' => 0]);
            exit;
        }

        $cartModel = new Cart($this->db);

        echo json_encode([
            'count' => (int)$cartModel->count($_SESSION['user_id'])
        ]);

        exit;
    }
}
