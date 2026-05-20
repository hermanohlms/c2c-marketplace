<?php $title = $product['name']; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<a class="back-link" href="/public/index.php?page=shop">← Back to Shop</a>

<section class="product-detail-page">

    <div class="product-detail-image-wrap">
        <?php if (!empty($product['image'])): ?>
            <img
                src="/public/uploads/<?php echo htmlspecialchars($product['image']); ?>"
                alt="<?php echo htmlspecialchars($product['name']); ?>"
                class="product-detail-image">
        <?php else: ?>
            <div class="product-detail-placeholder">No Image</div>
        <?php endif; ?>
    </div>

    <div class="product-detail-info card">

        <p class="product-category">
            <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
        </p>

        <h1><?php echo htmlspecialchars($product['name']); ?></h1>

        <p class="product-seller">
            Sold by
            <a href="/public/index.php?page=seller&id=<?php echo htmlspecialchars($product['seller_id']); ?>">
                <?php echo htmlspecialchars($product['seller_name'] ?? 'Unknown'); ?>
            </a>
        </p>

        <div class="rating-summary product-detail-rating">
            <span class="rating-stars">
                <?php
                $avg = round($product['average_rating']);
                echo str_repeat('★', $avg);
                echo str_repeat('☆', 5 - $avg);
                ?>
            </span>

            <span class="rating-count">
                <?php echo number_format($product['average_rating'], 1); ?>/5
                (<?php echo htmlspecialchars($product['review_count']); ?> reviews)
            </span>
        </div>

        <h2 class="product-detail-price">
            R<?php echo number_format($product['price'], 2); ?>
        </h2>

        <p class="stock-badge">
            Stock: <?php echo htmlspecialchars($product['stock']); ?>
        </p>

        <p class="product-description">
            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
        </p>

        <div class="product-actions">

            <form action="/public/index.php" method="POST" class="ajax-add-to-cart">
                <input type="hidden" name="action" value="add-to-cart">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">

                <button type="submit">Add to Cart</button>
            </form>

            <?php if (
                isset($_SESSION['user_id']) &&
                $_SESSION['user_role'] === 'buyer'
            ): ?>

                <form action="/public/index.php" method="POST">
                    <input type="hidden" name="action" value="start-conversation">
                    <input type="hidden" name="seller_id" value="<?php echo htmlspecialchars($product['seller_id']); ?>">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">

                    <button type="submit" class="btn-secondary">Contact Seller</button>
                </form>

            <?php endif; ?>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'buyer'): ?>

                <form action="/public/index.php" method="POST">
                    <input type="hidden" name="action" value="add-to-wishlist">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">

                    <button class="btn-secondary" type="submit">Add to Wishlist</button>
                </form>

            <?php endif; ?>

        </div>

    </div>

</section>

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

            <label class="rating-label">Rating</label>

            <div class="star-rating">
                <input type="radio" id="star5" name="rating" value="5" required>
                <label for="star5">★</label>

                <input type="radio" id="star4" name="rating" value="4">
                <label for="star4">★</label>

                <input type="radio" id="star3" name="rating" value="3">
                <label for="star3">★</label>

                <input type="radio" id="star2" name="rating" value="2">
                <label for="star2">★</label>

                <input type="radio" id="star1" name="rating" value="1">
                <label for="star1">★</label>
            </div>

            <label>
                Comment
                <textarea name="comment" placeholder="Write your review..."></textarea>
            </label>

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
                    <span class="review-stars">
                        <?php echo str_repeat('★', (int)$review['rating']); ?>
                        <?php echo str_repeat('☆', 5 - (int)$review['rating']); ?>
                    </span>
                </p>

                <p>
                    <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                </p>

                <small>
                    <?php echo formatDateTime($review['created_at']); ?>
                </small>
            </div>

        <?php endforeach; ?>

    </div>

</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>