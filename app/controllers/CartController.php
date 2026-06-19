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

        $productModel = new Product($this->db);
        $product = $productModel->findById($product_id);

        if (!$product) {
            $_SESSION['error'] = "Product not found.";
            header("Location: /index.php?page=shop");
            exit;
        }

        if ($quantity > (int)$product['stock']) {
            $_SESSION['error'] = "Not enough stock available.";
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/index.php?page=shop'));
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

        $subtotal = 0;
        $deliveryFee = 49.99;

        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $total = $subtotal + $deliveryFee;

        require_once __DIR__ . '/../views/cart/index.php';
    }

    public function update()
    {
        $this->requireBuyer();

        $quantities = $_POST['quantities'] ?? [];

        if (empty($quantities) || !is_array($quantities)) {
            $_SESSION['error'] = "Invalid cart update.";
            header("Location: /index.php?page=cart");
            exit;
        }

        $cartModel = new Cart($this->db);
        $productModel = new Product($this->db);

        foreach ($quantities as $product_id => $quantity) {
            $product_id = (int)$product_id;
            $quantity = (int)$quantity;

            if ($product_id <= 0) {
                continue;
            }

            if ($quantity <= 0) {
                $cartModel->remove($_SESSION['user_id'], $product_id);
                continue;
            }

            $product = $productModel->findById($product_id);

            if (!$product) {
                continue;
            }

            if ($quantity > (int)$product['stock']) {
                $_SESSION['error'] = "Not enough stock available for " . $product['name'] . ".";
                header("Location: /index.php?page=cart");
                exit;
            }

            $cartModel->updateQuantity(
                $_SESSION['user_id'],
                $product_id,
                $quantity
            );
        }

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
