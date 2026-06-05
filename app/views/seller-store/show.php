<?php $title = $seller['name'] . ' Store'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<section class="seller-store-header card">

    <div class="seller-store-profile">

        <?php if (!empty($seller['profile_image'])): ?>
            <img
                src="/uploads/<?php echo htmlspecialchars($seller['profile_image']); ?>"
                alt="<?php echo htmlspecialchars($seller['name']); ?>"
                class="seller-store-image">
        <?php else: ?>
            <div class="seller-store-placeholder">
                <?php echo strtoupper(substr($seller['name'], 0, 1)); ?>
            </div>
        <?php endif; ?>

        <div>
            <h1><?php echo htmlspecialchars($seller['name']); ?></h1>

            <div class="seller-store-stats">

                <span>
                    <?php echo count($products); ?> Products listed
                </span>
                <br>
                <span>
                    <?php echo htmlspecialchars($sales ?? 0); ?> Sales made
                </span>
                <br>
                <span>
                    <?php echo number_format($rating ?? 0, 1); ?>/5 star rating
                </span>

            </div>

            <?php if (!empty($seller['store_description'])): ?>
                <p class="seller-store-description">
                    <?php echo nl2br(htmlspecialchars($seller['store_description'])); ?>
                </p>
            <?php endif; ?>

            <?php if (
                isset($_SESSION['user_id']) &&
                $_SESSION['user_role'] === 'seller' &&
                $_SESSION['user_id'] == $seller['id']
            ): ?>

                <form action="/index.php" method="POST" class="stack-form store-description-form">
                    <?php echo csrfField(); ?>

                    <input type="hidden" name="action" value="update-store-description">

                    <label>
                        Store Description
                        <textarea
                            name="store_description"
                            rows="5"
                            placeholder="Tell buyers about your store..."><?php echo htmlspecialchars($seller['store_description'] ?? ''); ?></textarea>
                    </label>

                    <button type="submit">Update Store Description</button>
                </form>

            <?php endif; ?>

            <div class="seller-store-meta">
                <span>Seller since <?php echo htmlspecialchars(date('F Y', strtotime($seller['created_at']))); ?></span>

                <?php if (!empty($seller['phone'])): ?>
                    <span>Contact: <?php echo htmlspecialchars($seller['phone']); ?></span>
                <?php endif; ?>
            </div>
        </div>

    </div>

</section>

<div class="page-header seller-products-header">
    <div>
        <h2>
            Products by <?php echo htmlspecialchars($seller['name']); ?>
            (<?php echo count($products); ?>)
        </h2>
        <p>Browse this seller’s active listings.</p>
    </div>

    <a class="btn btn-secondary" href="/index.php?page=shop">Back to Shop</a>
</div>

<?php if (empty($products)): ?>

    <div class="card empty-state">
        <h2>No active products</h2>
        <p>This seller has no active products right now.</p>
    </div>

<?php else: ?>

    <div class="product-grid">

        <?php foreach ($products as $product): ?>

            <div class="product-card">

                <a href="/index.php?page=product&id=<?php echo htmlspecialchars($product['id']); ?>" class="product-image-link">
                    <?php if (!empty($product['image'])): ?>
                        <img
                            src="/uploads/<?php echo htmlspecialchars($product['image']); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?>"
                            class="product-image">
                    <?php else: ?>
                        <div class="product-image-placeholder">No Image</div>
                    <?php endif; ?>
                </a>

                <div class="product-card-body">

                    <p class="product-category">
                        <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                    </p>

                    <h3 class="product-title">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h3>

                    <div class="rating-summary">
                        <span class="rating-stars">
                            <?php
                            $avg = round($product['average_rating']);
                            echo str_repeat('★', $avg);
                            echo str_repeat('☆', 5 - $avg);
                            ?>
                        </span>

                        <span class="rating-count">
                            <?php echo htmlspecialchars($product['review_count']); ?> reviews
                        </span>
                    </div>

                    <div class="product-card-footer">
                        <strong class="product-price">
                            R<?php echo number_format($product['price'], 2); ?>
                        </strong>

                        <a
                            class="btn product-btn"
                            href="/index.php?page=product&id=<?php echo htmlspecialchars($product['id']); ?>">
                            View
                        </a>
                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>