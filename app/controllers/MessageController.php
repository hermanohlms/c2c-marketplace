<?php

require_once __DIR__ . '/../models/Message.php';

class MessageController
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
            header("Location: /index.php?page=login");
            exit;
        }
    }

    public function inbox()
    {
        $this->requireLogin();

        $messageModel = new Message($this->db);
        $conversations = $messageModel->getConversationsByUser($_SESSION['user_id']);

        require_once __DIR__ . '/../views/messages/inbox.php';
    }

    public function start()
    {
        $this->requireLogin();

        if ($_SESSION['user_role'] !== 'buyer') {
            $_SESSION['error'] = "Only buyers can contact sellers.";
            header("Location: /index.php?page=shop");
            exit;
        }

        $seller_id = $_POST['seller_id'] ?? null;
        $product_id = $_POST['product_id'] ?? null;

        if (!$seller_id) {
            $_SESSION['error'] = "Invalid seller.";
            header("Location: /index.php?page=shop");
            exit;
        }

        $messageModel = new Message($this->db);

        $conversation_id = $messageModel->findOrCreateConversation(
            $_SESSION['user_id'],
            $seller_id,
            $product_id
        );

        header("Location: /index.php?page=messages-thread&id=" . $conversation_id);
        exit;
    }

    public function thread()
    {
        $this->requireLogin();

        $conversation_id = $_GET['id'] ?? null;

        if (!$conversation_id) {
            $_SESSION['error'] = "Conversation not found.";
            header("Location: /index.php?page=messages");
            exit;
        }

        $messageModel = new Message($this->db);

        $conversation = $messageModel->getConversation(
            $conversation_id,
            $_SESSION['user_id']
        );

        if (!$conversation) {
            $_SESSION['error'] = "Conversation not found.";
            header("Location: /index.php?page=messages");
            exit;
        }

        $messageModel->markConversationRead(
            $conversation_id,
            $_SESSION['user_id']
        );

        $messages = $messageModel->getMessages($conversation_id);

        require_once __DIR__ . '/../views/messages/thread.php';
    }

    public function send()
    {
        $this->requireLogin();

        $conversation_id = $_POST['conversation_id'] ?? null;
        $message = trim($_POST['message'] ?? '');

        if (!$conversation_id || $message === '') {
            $_SESSION['error'] = "Message cannot be empty.";
            header("Location: /index.php?page=messages");
            exit;
        }

        $messageModel = new Message($this->db);

        $conversation = $messageModel->getConversation(
            $conversation_id,
            $_SESSION['user_id']
        );

        if (!$conversation) {
            $_SESSION['error'] = "Conversation not found.";
            header("Location: /index.php?page=messages");
            exit;
        }

        $messageModel->sendMessage(
            $conversation_id,
            $_SESSION['user_id'],
            $message
        );

        header("Location: /index.php?page=messages-thread&id=" . $conversation_id);
        exit;
    }
}
