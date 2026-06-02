<?php

class SupportController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            abort403();
        }

        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($subject === '' || $message === '') {
            header('Location: /public/index.php?page=contact&error=empty');
            exit;
        }

        $stmt = $this->db->prepare("
            INSERT INTO support_tickets (user_id, subject, message, status)
            VALUES (:user_id, :subject, :message, :status)
        ");

        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':subject' => $subject,
            ':message' => $message,
            ':status' => 'open'
        ]);

        header('Location: /public/index.php?page=contact&success=ticket_submitted');
        exit;
    }

    public function updateStatus()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = "Admin access only.";
            header("Location: /public/index.php?page=home");
            exit;
        }

        $ticket_id = $_POST['ticket_id'] ?? null;
        $status = $_POST['status'] ?? null;

        $allowedStatuses = ['open', 'in_progress', 'closed'];

        if (!$ticket_id || !in_array($status, $allowedStatuses)) {
            $_SESSION['error'] = "Invalid ticket update.";
            header("Location: /public/index.php?page=admin-tickets");
            exit;
        }

        $stmt = $this->db->prepare("
        UPDATE support_tickets
        SET status = :status
        WHERE id = :id
    ");

        $updated = $stmt->execute([
            ':status' => $status,
            ':id' => $ticket_id
        ]);

        $_SESSION[$updated ? 'success' : 'error'] =
            $updated ? "Ticket updated successfully." : "Could not update ticket.";

        header("Location: /public/index.php?page=admin-tickets");
        exit;
    }
}
