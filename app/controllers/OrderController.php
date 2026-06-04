<?php

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/Escrow.php';

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

        $currentPage = max(1, (int)($_GET['p'] ?? 1));
        $perPage = 6;
        $offset = ($currentPage - 1) * $perPage;

        $orderModel = new Order($this->db);

        $totalOrders = $orderModel->countByBuyer($_SESSION['user_id']);
        $totalPages = ceil($totalOrders / $perPage);

        $orders = $orderModel->getByBuyerPaginated(
            $_SESSION['user_id'],
            $perPage,
            $offset
        );

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

        $currentPage = max(1, (int)($_GET['p'] ?? 1));
        $perPage = 6;
        $offset = ($currentPage - 1) * $perPage;

        $orderModel = new Order($this->db);

        $totalOrders = $orderModel->countBySeller($_SESSION['user_id']);
        $totalPages = ceil($totalOrders / $perPage);

        $orders = $orderModel->getBySellerPaginated(
            $_SESSION['user_id'],
            $perPage,
            $offset
        );

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
        $tracking_number = trim($_POST['tracking_number'] ?? '');

        if (!$order_id || !$status) {
            $_SESSION['error'] = "Invalid order update.";
            header("Location: /public/index.php?page=seller-orders");
            exit;
        }

        $orderModel = new Order($this->db);

        $updated = $orderModel->updateStatusForSeller(
            $order_id,
            $_SESSION['user_id'],
            $status,
            $tracking_number
        );

        if ($updated) {
            $_SESSION['success'] = "Order status updated.";

            if ($status === 'delivered') {
                $escrowModel = new Escrow($this->db);
                $escrowModel->releaseByOrder($order_id);
            }

            $notificationModel = new Notification($this->db);

            $buyer_id = $orderModel->getBuyerIdByOrder($order_id);

            if ($buyer_id) {
                $notificationModel->create(
                    $buyer_id,
                    'Order status updated',
                    'Your order #' . $order_id . ' is now ' . $status . '.',
                    'order'
                );
            }

            $_SESSION['success'] = "Order status updated.";

            if ($status === 'shipped') {
                $stmt = $this->db->prepare("
                    SELECT users.email, users.name
                    FROM orders
                    INNER JOIN users ON orders.buyer_id = users.id
                    WHERE orders.id = :order_id
                    LIMIT 1
                ");

                $stmt->execute([
                    ':order_id' => $order_id
                ]);

                $buyer = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($buyer) {
                    sendEmail(
                        $buyer['email'],
                        "Your Order Has Shipped - #{$order_id}",
                        "
                        <h2>Your order has shipped</h2>

                        <p>Hello {$buyer['name']},</p>

                        <p>
                            Your order <strong>#{$order_id}</strong>
                            has been marked as shipped.
                        </p>

                    " . (!empty($tracking_number)
                            ? "<p><strong>Tracking Number:</strong> {$tracking_number}</p>"
                            : "") . "

                        <p>
                            You can view the tracking number and
                            confirm delivery from your My Orders page.
                        </p>
                    "
                    );
                }
            }
        } else {
            $_SESSION['error'] = "Could not update order status.";
        }

        header("Location: /public/index.php?page=seller-orders");
        exit;
    }



    public function sellerEarnings()
    {

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
            $_SESSION['error'] = "Seller access only.";
            header("Location: /public/index.php?page=shop");
            exit;
        }

        $escrowModel = new Escrow($this->db);

        $summary = $escrowModel->getSellerSummary($_SESSION['user_id']);
        $transactions = $escrowModel->getSellerTransactions($_SESSION['user_id']);

        $monthlySummary = $escrowModel->getSellerMonthlySummary($_SESSION['user_id']);
        $monthlyBreakdown = $escrowModel->getSellerMonthlyBreakdown($_SESSION['user_id']);

        require_once __DIR__ . '/../views/seller/earnings.php';
    }

    public function confirmReceived()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'buyer') {
            $_SESSION['error'] = "Buyer access only.";
            header("Location: /public/index.php?page=my-orders");
            exit;
        }

        $order_id = $_POST['order_id'] ?? null;

        if (!$order_id) {
            $_SESSION['error'] = "Invalid order.";
            header("Location: /public/index.php?page=my-orders");
            exit;
        }

        $orderModel = new Order($this->db);

        $updated = $orderModel->markDeliveredByBuyer(
            $order_id,
            $_SESSION['user_id']
        );

        if ($updated) {
            $escrowModel = new Escrow($this->db);
            $escrowModel->releaseByOrder($order_id);

            $stmt = $this->db->prepare("
                SELECT DISTINCT users.email, users.name
                FROM order_items
                INNER JOIN products ON order_items.product_id = products.id
                INNER JOIN users ON products.seller_id = users.id
                WHERE order_items.order_id = :order_id
            ");

            $stmt->execute([
                ':order_id' => $order_id
            ]);

            $sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($sellers as $seller) {
                sendEmail(
                    $seller['email'],
                    "Order Delivered - Funds Released",
                    "
                        <h2>Funds Released</h2>
                        <p>Hello {$seller['name']},</p>
                        <p>Order <strong>#{$order_id}</strong> has been confirmed as delivered.</p>
                        <p>Your seller earnings for this order are now available.</p>
                    "
                );
            }

            $_SESSION['success'] = "Order confirmed as received. Seller funds have been released.";
        } else {
            $_SESSION['error'] = "Could not confirm this order. It may not be shipped yet.";
        }

        header("Location: /public/index.php?page=my-orders");
        exit;
    }

    public function requestPayout()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
            $_SESSION['error'] = "Seller access only.";
            header("Location: /public/index.php?page=seller-earnings");
            exit;
        }

        $amount = (float)($_POST['amount'] ?? 0);

        $escrowModel = new Escrow($this->db);

        $created = $escrowModel->createPayoutRequest(
            $_SESSION['user_id'],
            $amount
        );

        $_SESSION[$created ? 'success' : 'error'] =
            $created ? "Payout request submitted." : "Invalid payout amount.";

        header("Location: /public/index.php?page=seller-earnings");
        exit;
    }
}
