<?php

class Message
{

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findOrCreateConversation($buyer_id, $seller_id, $product_id = null)
    {
        $sql = "
            SELECT id
            FROM conversations
            WHERE buyer_id = :buyer_id
            AND seller_id = :seller_id
            AND product_id IS NOT DISTINCT FROM :product_id
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':buyer_id' => $buyer_id,
            ':seller_id' => $seller_id,
            ':product_id' => $product_id
        ]);

        $conversation = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($conversation) {
            return $conversation['id'];
        }

        $sql = "
            INSERT INTO conversations (buyer_id, seller_id, product_id)
            VALUES (:buyer_id, :seller_id, :product_id)
            RETURNING id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':buyer_id' => $buyer_id,
            ':seller_id' => $seller_id,
            ':product_id' => $product_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    }

    public function getConversationsByUser($user_id)
    {
        $sql = "
        SELECT 
            conversations.*,
            buyer.name AS buyer_name,
            seller.name AS seller_name,
            products.name AS product_name,

            (
                SELECT messages.message
                FROM messages
                WHERE messages.conversation_id = conversations.id
                ORDER BY messages.created_at DESC
                LIMIT 1
            ) AS last_message,

            (
                SELECT messages.created_at
                FROM messages
                WHERE messages.conversation_id = conversations.id
                ORDER BY messages.created_at DESC
                LIMIT 1
            ) AS last_message_at,

            (
                SELECT COUNT(*)
                FROM messages
                WHERE messages.conversation_id = conversations.id
                AND messages.sender_id != :user_id
                AND messages.is_read = FALSE
            ) AS unread_count

        FROM conversations
        INNER JOIN users AS buyer 
            ON conversations.buyer_id = buyer.id
        INNER JOIN users AS seller 
            ON conversations.seller_id = seller.id
        LEFT JOIN products 
            ON conversations.product_id = products.id

        WHERE conversations.buyer_id = :user_id
        OR conversations.seller_id = :user_id

        ORDER BY last_message_at DESC NULLS LAST, conversations.created_at DESC
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':user_id' => $user_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getConversation($conversation_id, $user_id)
    {
        $sql = "
            SELECT 
                conversations.*,
                buyer.name AS buyer_name,
                seller.name AS seller_name,
                products.name AS product_name
            FROM conversations
            INNER JOIN users AS buyer ON conversations.buyer_id = buyer.id
            INNER JOIN users AS seller ON conversations.seller_id = seller.id
            LEFT JOIN products ON conversations.product_id = products.id
            WHERE conversations.id = :conversation_id
            AND (
                conversations.buyer_id = :user_id
                OR conversations.seller_id = :user_id
            )
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':conversation_id' => $conversation_id,
            ':user_id' => $user_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getMessages($conversation_id)
    {
        $sql = "
            SELECT messages.*, users.name AS sender_name
            FROM messages
            INNER JOIN users ON messages.sender_id = users.id
            WHERE conversation_id = :conversation_id
            ORDER BY messages.created_at ASC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':conversation_id' => $conversation_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sendMessage($conversation_id, $sender_id, $message)
    {
        $sql = "
            INSERT INTO messages (conversation_id, sender_id, message)
            VALUES (:conversation_id, :sender_id, :message)
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':conversation_id' => $conversation_id,
            ':sender_id' => $sender_id,
            ':message' => $message
        ]);
    }

    public function unreadCount($user_id)
    {
        $sql = "
        SELECT COUNT(messages.id) AS total
        FROM messages
        INNER JOIN conversations 
            ON messages.conversation_id = conversations.id
        WHERE messages.sender_id != :user_id
        AND messages.is_read = FALSE
        AND (
            conversations.buyer_id = :user_id
            OR conversations.seller_id = :user_id
        )
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':user_id' => $user_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function unreadCountByConversation($conversation_id, $user_id)
    {
        $sql = "
        SELECT COUNT(*) AS total
        FROM messages
        WHERE conversation_id = :conversation_id
        AND sender_id != :user_id
        AND is_read = FALSE
    ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':conversation_id' => $conversation_id,
            ':user_id' => $user_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function markConversationRead($conversation_id, $user_id)
    {
        $sql = "
        UPDATE messages
        SET is_read = TRUE
        WHERE conversation_id = :conversation_id
        AND sender_id != :user_id
    ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':conversation_id' => $conversation_id,
            ':user_id' => $user_id
        ]);
    }
}
