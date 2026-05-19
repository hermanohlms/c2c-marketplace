<?php

require_once __DIR__ . '/../helpers/payfast_helper.php';

class PaymentController
{

    private $db;
    private $config;

    public function __construct($db)
    {
        $this->db = $db;
        $this->config = require __DIR__ . '/../../config/payfast.php';
    }

    public function startPayfast()
    {
        if (!isset($_SESSION['last_order_id'])) {
            $_SESSION['error'] = "No order found for payment.";
            header("Location: /public/index.php?page=cart");
            exit;
        }

        $order_id = $_SESSION['last_order_id'];

        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            $_SESSION['error'] = "Order not found.";
            header("Location: /public/index.php?page=cart");
            exit;
        }

        $data = [
            'merchant_id' => $this->config['merchant_id'],
            'merchant_key' => $this->config['merchant_key'],
            'return_url' => $this->config['return_url'],
            'cancel_url' => $this->config['cancel_url'],
            'notify_url' => $this->config['notify_url'],
            'm_payment_id' => $order['id'],
            'amount' => number_format($order['total_amount'], 2, '.', ''),
            'item_name' => 'Order #' . $order['id']
        ];

        $data['signature'] = generatePayfastSignature(
            $data,
            $this->config['passphrase']
        );

        $url = $this->config['sandbox']
            ? $this->config['sandbox_url']
            : $this->config['live_url'];

        require_once __DIR__ . '/../views/payments/payfast-form.php';
    }

    public function success()
    {
        $_SESSION['success'] = "Payment flow completed. Payment confirmation still depends on PayFast ITN.";
        require_once __DIR__ . '/../views/payments/success.php';
    }

    public function cancelled()
    {
        $_SESSION['error'] = "Payment was cancelled.";
        header("Location: /public/index.php?page=my-orders");
        exit;
    }
}
