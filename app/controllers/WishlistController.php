<?php

require_once __DIR__ . '/../models/Wishlist.php';

class WishlistController
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    private function requireBuyer()
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
    }

    public function index()
    {
        $this->requireBuyer();

        $wishlistModel = new Wishlist($this->db);
        $wishlistItems = $wishlistModel->getByUser($_SESSION['user_id']);

        require_once __DIR__ . '/../views/wishlist/index.php';
    }

    public function add()
    {
        $this->requireBuyer();

        $product_id = $_POST['product_id'] ?? null;

        if (!$product_id) {
            if (isset($_POST['ajax'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid product.'
                ]);
                exit;
            }

            $_SESSION['error'] = "Invalid product.";
            header("Location: /public/index.php?page=shop");
            exit;
        }

        $wishlistModel = new Wishlist($this->db);
        $wishlistModel->add($_SESSION['user_id'], $product_id);

        if (isset($_POST['ajax'])) {
            echo json_encode([
                'success' => true,
                'message' => 'Product added to wishlist.'
            ]);
            exit;
        }

        $_SESSION['success'] = "Product added to wishlist.";

        header("Location: /public/index.php?page=product&id=" . $product_id);
        exit;
    }

    public function remove()
    {
        $this->requireBuyer();

        $product_id = $_POST['product_id'] ?? null;

        if ($product_id) {
            $wishlistModel = new Wishlist($this->db);
            $wishlistModel->remove($_SESSION['user_id'], $product_id);

            if (isset($_POST['ajax'])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Product removed from wishlist.'
                ]);
                exit;
            }

            $_SESSION['success'] = "Product removed from wishlist.";
        } else {
            if (isset($_POST['ajax'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid product.'
                ]);
                exit;
            }

            $_SESSION['error'] = "Invalid product.";
        }

        header("Location: /public/index.php?page=wishlist");
        exit;
    }

    public function count()
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['count' => 0]);
            exit;
        }

        $stmt = $this->db->prepare("
        SELECT COUNT(*) AS total
        FROM wishlists
        WHERE user_id = :user_id
    ");

        $stmt->execute([
            ':user_id' => $_SESSION['user_id']
        ]);

        $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        echo json_encode([
            'count' => $count
        ]);
        exit;
    }
}
