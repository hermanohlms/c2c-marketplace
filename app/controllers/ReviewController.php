<?php

require_once __DIR__ . '/../models/Review.php';

class ReviewController
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please login to leave a review.";
            header("Location: /public/index.php?page=login");
            exit;
        }

        if ($_SESSION['user_role'] !== 'buyer') {
            $_SESSION['error'] = "Only buyers can leave reviews.";
            header("Location: /public/index.php?page=shop");
            exit;
        }

        $product_id = $_POST['product_id'] ?? null;
        $rating = $_POST['rating'] ?? null;
        $comment = $_POST['comment'] ?? '';

        if (!$product_id || !$rating) {
            $_SESSION['error'] = "Invalid review.";
            header("Location: /public/index.php?page=shop");
            exit;
        }

        $rating = (int) $rating;

        if ($rating < 1 || $rating > 5) {
            $_SESSION['error'] = "Rating must be between 1 and 5.";
            header("Location: /public/index.php?page=product&id=" . $product_id);
            exit;
        }

        $reviewModel = new Review($this->db);

        if (!$reviewModel->hasPurchasedProduct($_SESSION['user_id'], $product_id)) {
            $_SESSION['error'] = "You can only review products you have purchased.";
            header("Location: /public/index.php?page=product&id=" . $product_id);
            exit;
        }

        if ($reviewModel->hasReviewed($_SESSION['user_id'], $product_id)) {
            $_SESSION['error'] = "You have already reviewed this product.";
            header("Location: /public/index.php?page=product&id=" . $product_id);
            exit;
        }

        $created = $reviewModel->create(
            $product_id,
            $_SESSION['user_id'],
            $rating,
            $comment
        );

        if ($created) {
            $_SESSION['success'] = "Review submitted successfully.";
        } else {
            $_SESSION['error'] = "Could not submit review.";
        }

        header("Location: /public/index.php?page=product&id=" . $product_id);
        exit;
    }
}
