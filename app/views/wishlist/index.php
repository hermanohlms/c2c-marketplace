<?php $title = 'My Wishlist'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>My Wishlist</h1>

<?php if (empty($wishlistItems)): ?>

    <p>Your wishlist is empty.</p>
    <a href="/public/index.php?page=shop">Browse Products</a>

<?php else: ?>

    <div class="product-grid">

        <?php foreach ($wishlistItems as $item): ?>

            <div class="product-card">

                <?php if (!empty($item['image'])): ?>
                    <img
                        src="/public/uploads/<?php echo htmlspecialchars($item['image']); ?>"
                        alt="<?php echo htmlspecialchars($item['name']); ?>">
                <?php endif; ?>

                <h3><?php echo htmlspecialchars($item['name']); ?></h3>

                <p><?php echo htmlspecialchars($item['category_name'] ?? 'No category'); ?></p>

                <p><strong>R<?php echo number_format($item['price'], 2); ?></strong></p>

                <a href="/public/index.php?page=product&id=<?php echo htmlspecialchars($item['product_id']); ?>">
                    View Product
                </a>

                <br><br>

                <form action="/public/index.php" method="POST">
                    <input type="hidden" name="action" value="remove-from-wishlist">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id']); ?>">

                    <button type="submit">Remove</button>
                </form>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>