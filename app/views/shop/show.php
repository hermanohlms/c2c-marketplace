<?php $title = $product['name']; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="product-detail">

    <?php if (!empty($product['image'])): ?>
        <img
            src="/public/uploads/<?php echo htmlspecialchars($product['image']); ?>"
            alt="<?php echo htmlspecialchars($product['name']); ?>">
    <?php endif; ?>

    <div>
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>

        <p>
            Category:
            <?php echo htmlspecialchars($product['category_name'] ?? 'No category'); ?>
        </p>

        <p>
            Seller:
            <?php echo htmlspecialchars($product['seller_name'] ?? 'Unknown'); ?>
        </p>

        <p>
            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
        </p>

        <h2>R<?php echo htmlspecialchars($product['price']); ?></h2>

        <p>
            Stock:
            <?php echo htmlspecialchars($product['stock']); ?>
        </p>

        <form action="/public/index.php" method="POST" class="ajax-add-to-cart">
            <input type="hidden" name="action" value="add-to-cart">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

            <button type="submit">Add to Cart</button>
        </form>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'buyer'): ?>

            <form action="/public/index.php" method="POST">
                <input type="hidden" name="action" value="add-to-wishlist">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">

                <button type="submit">Add to Wishlist</button>
            </form>

        <?php endif; ?>

        <a href="/public/index.php?page=shop">Back to Shop</a>
    </div>

</div>

<hr>

<section class="reviews-section">

    <h2>Reviews</h2>

    <?php if (!empty($ratingSummary['total_reviews'])): ?>
        <p>
            Average rating:
            <?php echo number_format($ratingSummary['average_rating'], 1); ?>/5
            (<?php echo htmlspecialchars($ratingSummary['total_reviews']); ?> reviews)
        </p>
    <?php else: ?>
        <p>No reviews yet.</p>
    <?php endif; ?>

    <?php if (
        isset($_SESSION['user_id']) &&
        $_SESSION['user_role'] === 'buyer'
    ): ?>

        <h3>Leave a Review</h3>

        <form action="/public/index.php" method="POST" class="review-form">
            <input type="hidden" name="action" value="create-review">
            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">

            <label>
                Rating
                <select name="rating" required>
                    <option value="">Select rating</option>
                    <option value="5">5 - Excellent</option>
                    <option value="4">4 - Good</option>
                    <option value="3">3 - Average</option>
                    <option value="2">2 - Poor</option>
                    <option value="1">1 - Very Poor</option>
                </select>
            </label>

            <br><br>

            <label>
                Comment
                <textarea name="comment" placeholder="Write your review..."></textarea>
            </label>

            <br><br>

            <button type="submit">Submit Review</button>
        </form>

    <?php endif; ?>

    <div class="review-list">

        <?php foreach ($reviews as $review): ?>

            <div class="review-card">
                <strong>
                    <?php echo htmlspecialchars($review['reviewer_name']); ?>
                </strong>

                <p>
                    Rating:
                    <?php echo htmlspecialchars($review['rating']); ?>/5
                </p>

                <p>
                    <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                </p>

                <small>
                    <?php echo htmlspecialchars($review['created_at']); ?>
                </small>
            </div>

        <?php endforeach; ?>

    </div>

</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>