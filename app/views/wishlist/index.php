<?php $title = 'My Wishlist'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>My Wishlist</h1>

<?php if (empty($wishlistItems)): ?>

    <div class="card empty-state">
        <h2>Your wishlist is empty</h2>

        <p>
            Save products you like so you can easily find them again later.
        </p>

        <a class="btn" href="/index.php?page=shop">
            Browse Products
        </a>
    </div>

<?php else: ?>

    <div class="product-grid">

        <?php foreach ($wishlistItems as $item): ?>

            <div class="product-card">

                <?php if (!empty($item['image'])): ?>
                    <img
                        src="<?php echo htmlspecialchars(productImageUrl($item['image'])); ?>"
                        alt="<?php echo htmlspecialchars($item['name']); ?>">
                <?php endif; ?>

                <p class="wishlist-category">
                    <?php echo htmlspecialchars($item['category_name'] ?? 'No category'); ?>
                </p>

                <h3 class="wishlist-title">
                    <?php echo htmlspecialchars($item['name']); ?>
                </h3>

                <p class="wishlist-price">
                    R<?php echo number_format($item['price'], 2); ?>
                </p>

                <div class="wishlist-actions">

                    <form action="/index.php" method="GET">
                        <input type="hidden" name="page" value="product">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['product_id']); ?>">

                        <button type="submit">
                            View Product
                        </button>
                    </form>

                    <form action="/index.php" method="POST">
                        <?php echo csrfField(); ?>

                        <input type="hidden" name="action" value="add-to-cart">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id']); ?>">
                        <input type="hidden" name="quantity" value="1">

                        <button type="submit">
                            Add to Cart
                        </button>
                    </form>

                    <form action="/index.php" method="POST">
                        <?php echo csrfField(); ?>

                        <input type="hidden" name="action" value="remove-from-wishlist">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id']); ?>">

                        <button type="submit" class="btn-secondary">
                            Remove
                        </button>
                    </form>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>