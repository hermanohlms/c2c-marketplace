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
}
