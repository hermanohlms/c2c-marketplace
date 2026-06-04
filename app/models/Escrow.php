<?php

class Escrow
{
    private $conn;

    private $commissionRate = 0.10; // 10%

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createForOrderItem($order_id, $order_item_id, $seller_id, $gross_amount)
    {
        $commission = round($gross_amount * $this->commissionRate, 2);
        $sellerAmount = round($gross_amount - $commission, 2);

        $sql = "
            INSERT INTO seller_escrow
            (
                order_id,
                order_item_id,
                seller_id,
                gross_amount,
                commission_amount,
                seller_amount,
                status
            )
            VALUES
            (
                :order_id,
                :order_item_id,
                :seller_id,
                :gross_amount,
                :commission_amount,
                :seller_amount,
                'held'
            )
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':order_id' => $order_id,
            ':order_item_id' => $order_item_id,
            ':seller_id' => $seller_id,
            ':gross_amount' => $gross_amount,
            ':commission_amount' => $commission,
            ':seller_amount' => $sellerAmount
        ]);
    }

    public function releaseByOrder($order_id)
    {
        $sql = "
            UPDATE seller_escrow
            SET status = 'released',
                released_at = CURRENT_TIMESTAMP
            WHERE order_id = :order_id
            AND status = 'held'
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':order_id' => $order_id
        ]);
    }

    public function getSellerSummary($seller_id)
    {
        $sql = "
            SELECT
                COALESCE(SUM(CASE WHEN status = 'held' THEN seller_amount ELSE 0 END), 0) AS held_balance,
                COALESCE(SUM(CASE WHEN status = 'released' THEN seller_amount ELSE 0 END), 0) AS available_balance,
                COALESCE(SUM(CASE WHEN status = 'paid_out' THEN seller_amount ELSE 0 END), 0) AS paid_out_total,
                COALESCE(SUM(commission_amount), 0) AS total_commission
            FROM seller_escrow
            WHERE seller_id = :seller_id
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':seller_id' => $seller_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSellerTransactions($seller_id)
    {
        $sql = "
            SELECT 
                seller_escrow.*,
                products.name AS product_name,
                order_items.quantity
            FROM seller_escrow
            INNER JOIN order_items
                ON seller_escrow.order_item_id = order_items.id
            INNER JOIN products
                ON order_items.product_id = products.id
            WHERE seller_escrow.seller_id = :seller_id
            ORDER BY seller_escrow.created_at DESC
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':seller_id' => $seller_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function releaseExpiredShippedOrders($days = 14)
    {
        $sql = "
        UPDATE seller_escrow
        SET status = 'released',
            released_at = CURRENT_TIMESTAMP
        WHERE status = 'held'
        AND order_id IN (
            SELECT id
            FROM orders
            WHERE status = 'shipped'
            AND shipped_at <= CURRENT_TIMESTAMP - (:days || ' days')::interval
        )
    ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':days' => $days
        ]);
    }

    public function createPayoutRequest($seller_id, $amount)
    {
        $summary = $this->getSellerSummary($seller_id);

        if ($amount <= 0 || $amount > $summary['available_balance']) {
            return false;
        }

        $sql = "
        INSERT INTO seller_payouts (seller_id, amount, status)
        VALUES (:seller_id, :amount, 'pending')
    ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':seller_id' => $seller_id,
            ':amount' => $amount
        ]);
    }

    public function getPayoutRequests()
    {
        $sql = "
        SELECT 
            seller_payouts.*,
            users.name AS seller_name,
            users.email AS seller_email
        FROM seller_payouts
        INNER JOIN users ON seller_payouts.seller_id = users.id
        ORDER BY seller_payouts.created_at DESC
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function approvePayout($payout_id)
    {
        $this->conn->beginTransaction();

        try {
            $stmt = $this->conn->prepare("
            SELECT *
            FROM seller_payouts
            WHERE id = :id
            AND status = 'pending'
            LIMIT 1
        ");

            $stmt->execute([':id' => $payout_id]);
            $payout = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$payout) {
                $this->conn->rollBack();
                return false;
            }

            $stmt = $this->conn->prepare("
            UPDATE seller_escrow
            SET status = 'paid_out',
                paid_out_at = CURRENT_TIMESTAMP
            WHERE seller_id = :seller_id
            AND status = 'released'
        ");

            $stmt->execute([
                ':seller_id' => $payout['seller_id']
            ]);

            $stmt = $this->conn->prepare("
            UPDATE seller_payouts
            SET status = 'paid',
                paid_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");

            $stmt->execute([
                ':id' => $payout_id
            ]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function rejectPayout($payout_id)
    {
        $stmt = $this->conn->prepare("
        UPDATE seller_payouts
        SET status = 'cancelled'
        WHERE id = :id
        AND status = 'pending'
    ");

        $stmt->execute([
            ':id' => $payout_id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function getSellerMonthlySummary($seller_id)
    {
        $sql = "
        SELECT
            COALESCE(SUM(CASE 
                WHEN status IN ('held', 'released', 'paid_out')
                AND created_at >= date_trunc('month', CURRENT_DATE)
                THEN seller_amount ELSE 0 END), 0) AS this_month,

            COALESCE(SUM(CASE 
                WHEN status IN ('held', 'released', 'paid_out')
                AND created_at >= date_trunc('month', CURRENT_DATE - INTERVAL '1 month')
                AND created_at < date_trunc('month', CURRENT_DATE)
                THEN seller_amount ELSE 0 END), 0) AS last_month
        FROM seller_escrow
        WHERE seller_id = :seller_id
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':seller_id' => $seller_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSellerMonthlyBreakdown($seller_id)
    {
        $sql = "
        SELECT 
            TO_CHAR(date_trunc('month', created_at), 'Mon YYYY') AS month,
            COALESCE(SUM(seller_amount), 0) AS total
        FROM seller_escrow
        WHERE seller_id = :seller_id
        AND status IN ('held', 'released', 'paid_out')
        GROUP BY date_trunc('month', created_at)
        ORDER BY date_trunc('month', created_at) DESC
        LIMIT 6
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':seller_id' => $seller_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
