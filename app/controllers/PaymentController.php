<?php

require_once __DIR__ . '/../helpers/payfast_helper.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Escrow.php';

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
            header("Location: /index.php?page=cart");
            exit;
        }

        $order_id = $_SESSION['last_order_id'];

        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            $_SESSION['error'] = "Order not found.";
            header("Location: /index.php?page=cart");
            exit;
        }

        $data = [
            'merchant_id' => $this->config['merchant_id'],
            'merchant_key' => $this->config['merchant_key'],
            'return_url' => $this->config['return_url'],
            'cancel_url' => $this->config['cancel_url'],
            'notify_url' => $this->config['notify_url'],
            'm_payment_id' => (string) $order['id'],
            'amount' => number_format((float) $order['total_amount'], 2, '.', ''),
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
        unset($_SESSION['last_order_id']);

        $_SESSION['success'] = "Payment submitted. We are waiting for PayFast confirmation.";
        require_once __DIR__ . '/../views/payments/success.php';
    }

    public function cancelled()
    {
        if (!empty($_SESSION['last_order_id'])) {
            $stmt = $this->db->prepare("
            UPDATE orders
            SET status = 'cancelled'
            WHERE id = :id
            AND status = 'pending'
        ");

            $stmt->execute([
                ':id' => $_SESSION['last_order_id']
            ]);

            unset($_SESSION['last_order_id']);
        }

        $_SESSION['error'] = "Payment was cancelled. Your order was not completed.";

        header("Location: /index.php?page=cart");
        exit;
    }

    public function itn()
    {
        $data = $_POST;

        if (getenv('PAYFAST_VALIDATE_IP') === 'true') {
            $clientIp = $this->getClientIp();

            if (!$this->isAllowedPayfastIp($clientIp)) {
                http_response_code(403);
                exit('Invalid PayFast source IP');
            }
        }

        if (empty($data)) {
            http_response_code(400);
            exit('No ITN data received');
        }

        $receivedSignature = $data['signature'] ?? '';

        $generatedSignature = generatePayfastSignature(
            $data,
            $this->config['passphrase']
        );

        if ($receivedSignature !== $generatedSignature) {
            http_response_code(400);
            exit('Invalid signature');
        }

        $order_id = $data['m_payment_id'] ?? null;
        $payment_status = $data['payment_status'] ?? '';
        $amount_gross = $data['amount_gross'] ?? 0;
        $pf_payment_id = $data['pf_payment_id'] ?? null;

        if (!$order_id) {
            http_response_code(400);
            exit('Missing order ID');
        }

        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            http_response_code(404);
            exit('Order not found');
        }

        if ($order['status'] === 'paid') {
            http_response_code(200);
            exit('Order already paid');
        }

        if (
            number_format((float)$order['total_amount'], 2, '.', '') !==
            number_format((float)$amount_gross, 2, '.', '')
        ) {
            http_response_code(400);
            exit('Amount mismatch');
        }

        if ($payment_status === 'COMPLETE') {

            $this->db->beginTransaction();

            try {
                $orderModel = new Order($this->db);
                $productModel = new Product($this->db);

                $orderItems = $orderModel->getItemsByOrderId($order_id);

                $escrowModel = new Escrow($this->db);

                foreach ($orderItems as $item) {
                    $reduced = $productModel->reduceStock(
                        $item['product_id'],
                        $item['quantity']
                    );

                    if (!$reduced) {
                        throw new Exception(
                            "Could not reduce stock for product ID " . $item['product_id']
                        );
                    }

                    $product = $productModel->findById($item['product_id']);

                    if (!$product || empty($product['seller_id'])) {
                        throw new Exception("Seller not found for product ID " . $item['product_id']);
                    }


                    $grossAmount = $item['price'] * $item['quantity'];

                    $escrowModel->createForOrderItem(
                        $order_id,
                        $item['id'],
                        $product['seller_id'],
                        $grossAmount
                    );
                }

                $stmt = $this->db->prepare("
                UPDATE orders
                SET status = 'paid'
                WHERE id = :id
            ");

                $stmt->execute([
                    ':id' => $order_id
                ]);

                $stmt = $this->db->prepare("
                INSERT INTO payments 
                (
                    order_id, 
                    payment_method, 
                    payment_status, 
                    transaction_id, 
                    amount, 
                    payfast_payment_id, 
                    raw_response
                )
                VALUES
                (
                    :order_id, 
                    'PayFast', 
                    'paid', 
                    :transaction_id, 
                    :amount, 
                    :payfast_payment_id, 
                    :raw_response
                )
            ");

                $stmt->execute([
                    ':order_id' => $order_id,
                    ':transaction_id' => $pf_payment_id,
                    ':amount' => $amount_gross,
                    ':payfast_payment_id' => $pf_payment_id,
                    ':raw_response' => json_encode($data)
                ]);

                $this->db->commit();

                $userStmt = $this->db->prepare("
                    SELECT users.email, users.name
                    FROM orders
                    INNER JOIN users ON orders.buyer_id = users.id
                    WHERE orders.id = :order_id
                    LIMIT 1
                ");

                $userStmt->execute([
                    ':order_id' => $order_id
                ]);

                $buyer = $userStmt->fetch(PDO::FETCH_ASSOC);

                if ($buyer) {

                    $subject = "Payment Confirmation - Order #{$order_id}";

                    $body = "
                        <h2>Payment Successful</h2>

                        <p>Hello {$buyer['name']},</p>

                        <p>
                        Your payment for order <strong>#{$order_id}</strong>
                        was successfully received.
                        </p>

                        <p>
                        Amount Paid: <strong>R{$amount_gross}</strong>
                        </p>

                        <p>
                        Thank you for shopping with us.
                        </p>
                    ";

                    sendEmail(
                        $buyer['email'],
                        $subject,
                        $body
                    );
                }

                http_response_code(200);
                exit('Payment verified');
            } catch (Exception $e) {
                $this->db->rollBack();

                http_response_code(500);
                exit('Payment processing failed');
            }
        }

        http_response_code(200);
        exit('Payment not complete');
    }

    private function getClientIp()
    {
        return $_SERVER['HTTP_CF_CONNECTING_IP']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? '';
    }

    private function isAllowedPayfastIp($ip)
    {
        $allowedIps = [
            '144.126.193.139'
        ];

        return in_array($ip, $allowedIps);
    }
}
