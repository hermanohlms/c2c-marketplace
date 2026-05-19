<?php

require_once __DIR__ . '/../models/Order.php';

class OrderController
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function myOrders()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please login first.";
            header("Location: /public/index.php?page=login");
            exit;
        }

        if ($_SESSION['user_role'] !== 'buyer') {
            $_SESSION['error'] = "Buyer access only.";
            header("Location: /public/index.php?page=shop");
            exit;
        }

        $orderModel = new Order($this->db);
        $orders = $orderModel->getByBuyer($_SESSION['user_id']);

        foreach ($orders as &$order) {
            $order['items'] = $orderModel->getItemsByOrder($order['id']);
        }

        require_once __DIR__ . '/../views/orders/my-orders.php';
    }

    public function sellerOrders()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please login first.";
            header("Location: /public/index.php?page=login");
            exit;
        }

        if ($_SESSION['user_role'] !== 'seller') {
            $_SESSION['error'] = "Seller access only.";
            header("Location: /public/index.php?page=shop");
            exit;
        }

        $orderModel = new Order($this->db);
        $orders = $orderModel->getBySeller($_SESSION['user_id']);

        require_once __DIR__ . '/../views/seller/orders.php';
    }

    public function updateStatus()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please login first.";
            header("Location: /public/index.php?page=login");
            exit;
        }

        if ($_SESSION['user_role'] !== 'seller') {
            $_SESSION['error'] = "Seller access only.";
            header("Location: /public/index.php?page=shop");
            exit;
        }

        $order_id = $_POST['order_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$order_id || !$status) {
            $_SESSION['error'] = "Invalid order update.";
            header("Location: /public/index.php?page=seller-orders");
            exit;
        }

        $orderModel = new Order($this->db);

        $updated = $orderModel->updateStatusForSeller(
            $order_id,
            $_SESSION['user_id'],
            $status
        );

        if ($updated) {
            $_SESSION['success'] = "Order status updated.";
        } else {
            $_SESSION['error'] = "Could not update order status.";
        }

        header("Location: /public/index.php?page=seller-orders");
        exit;
    }
}
