<?php

class Notification
{

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($user_id, $title, $message, $type = 'info')
    {
        $sql = "
            INSERT INTO notifications (user_id, title, message, type)
            VALUES (:user_id, :title, :message, :type)
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':message' => $message,
            ':type' => $type
        ]);
    }

    public function getByUser($user_id)
    {
        $sql = "
            SELECT *
            FROM notifications
            WHERE user_id = :user_id
            ORDER BY created_at DESC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function unreadCount($user_id)
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM notifications
            WHERE user_id = :user_id
            AND is_read = FALSE
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function markAllRead($user_id)
    {
        $sql = "
            UPDATE notifications
            SET is_read = TRUE
            WHERE user_id = :user_id
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([':user_id' => $user_id]);
    }
}
