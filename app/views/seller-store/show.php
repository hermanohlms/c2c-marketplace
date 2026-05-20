<?php $title = $seller['name'] . ' Store'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<section class="seller-store-header card">

    <div class="seller-store-profile">

        <?php if (!empty($seller['profile_image'])): ?>
            <img
                src="/public/uploads/<?php echo htmlspecialchars($seller['profile_image']); ?>"
                alt="<?php echo htmlspecialchars($seller['name']); ?>"
                class="seller-store-image">
        <?php else: ?>
            <div class="seller-store-placeholder">
                <?php echo strtoupper(substr($seller['name'], 0, 1)); ?>
            </div>
        <?php endif; ?>

        <div>
            <h1><?php echo htmlspecialchars($seller['name']); ?></h1>

            <p class="muted">
                Seller since <?php echo htmlspecialchars(date('F Y', strtotime($seller['created_at']))); ?>
            </p>

            <?php if (!empty($seller['phone'])): ?>
                <p class="muted">Contact: <?php echo htmlspecialchars($seller['phone']); ?></p>
            <?php endif; ?>
        </div>

    </div>

</section>

<div class="page-header">
    <div>
        <h2>Products by <?php echo htmlspecialchars($seller['name']); ?></h2>
        <p>Browse this seller’s active listings.</p>
    </div>

    <a class="btn btn-secondary" href="/public/index.php?page=shop">Back to Shop</a>
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

                <a href="/public/index.php?page=product&id=<?php echo htmlspecialchars($product['id']); ?>" class="product-image-link">
                    <?php if (!empty($product['image'])): ?>
                        <img
                            src="/public/uploads/<?php echo htmlspecialchars($product['image']); ?>"
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
                            href="/public/index.php?page=product&id=<?php echo htmlspecialchars($product['id']); ?>">
                            View
                        </a>
                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>