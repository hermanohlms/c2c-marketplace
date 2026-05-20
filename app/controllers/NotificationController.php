<?php

require_once __DIR__ . '/../models/Notification.php';

class NotificationController
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    private function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please login first.";
            header("Location: /public/index.php?page=login");
            exit;
        }
    }

    public function index()
    {
        $this->requireLogin();

        $notificationModel = new Notification($this->db);
        $notifications = $notificationModel->getByUser($_SESSION['user_id']);

        require_once __DIR__ . '/../views/notifications/index.php';
    }

    public function markAllRead()
    {
        $this->requireLogin();

        $notificationModel = new Notification($this->db);
        $notificationModel->markAllRead($_SESSION['user_id']);

        $_SESSION['success'] = "Notifications marked as read.";

        header("Location: /public/index.php?page=notifications");
        exit;
    }
}
