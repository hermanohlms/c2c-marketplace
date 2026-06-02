<?php

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Notification.php';

class CheckoutController
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function checkout()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please login before checkout.";
            header("Location: /public/index.php?page=login");
            exit;
        }

        if ($_SESSION['user_role'] !== 'buyer') {
            $_SESSION['error'] = "Only buyers can checkout.";
            header("Location: /public/index.php?page=shop");
            exit;
        }

        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            $_SESSION['error'] = "Your cart is empty.";
            header("Location: /public/index.php?page=cart");
            exit;
        }

        $delivery_name = trim($_POST['delivery_name'] ?? '');
        $delivery_phone = trim($_POST['delivery_phone'] ?? '');
        $address_line1 = trim($_POST['address_line1'] ?? '');
        $address_line2 = trim($_POST['address_line2'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $province = trim($_POST['province'] ?? '');
        $postal_code = trim($_POST['postal_code'] ?? '');
        $shipping_notes = trim($_POST['shipping_notes'] ?? '');

        if (
            $delivery_name === '' ||
            $delivery_phone === '' ||
            $address_line1 === '' ||
            $city === '' ||
            $province === '' ||
            $postal_code === ''
        ) {
            $_SESSION['error'] = "Please complete all required delivery details.";
            header("Location: /public/index.php?page=checkout");
            exit;
        }

        $orderModel = new Order($this->db);
        $productModel = new Product($this->db);

        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        try {
            $this->db->beginTransaction();

            $order_id = $orderModel->create(
                $_SESSION['user_id'],
                $total,
                $delivery_name,
                $delivery_phone,
                $address_line1,
                $address_line2,
                $city,
                $province,
                $postal_code,
                $shipping_notes
            );

            foreach ($cart as $item) {
                $product = $productModel->findById($item['id']);

                if (!$product || $product['stock'] < $item['quantity']) {
                    throw new Exception("Not enough stock for " . $item['name']);
                }

                $orderModel->addItem(
                    $order_id,
                    $item['id'],
                    $item['quantity'],
                    $item['price']
                );
            }

            $this->db->commit();

            unset($_SESSION['cart']);

            $_SESSION['last_order_id'] = $order_id;

            $_SESSION['success'] = "Order placed successfully.";

            $notificationModel = new Notification($this->db);

            $notificationModel->create(
                $_SESSION['user_id'],
                'Order placed',
                'Your order has been placed successfully.',
                'order'
            );

            header("Location: /public/index.php?page=payfast-start");
            exit;
        } catch (Exception $e) {
            $this->db->rollBack();

            $_SESSION['error'] = $e->getMessage();

            header("Location: /public/index.php?page=checkout");
            exit;
        }
    }

    public function show()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please login first.";
            header("Location: /public/index.php?page=login");
            exit;
        }

        if (empty($_SESSION['cart'])) {
            $_SESSION['error'] = "Your cart is empty.";
            header("Location: /public/index.php?page=cart");
            exit;
        }

        require_once __DIR__ . '/../views/checkout/index.php';
    }
}
