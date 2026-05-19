<?php $title = 'Admin Products'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>Product Moderation</h1>

<a href="/public/index.php?page=admin-dashboard">Back to Admin Dashboard</a>

<br><br>

<?php if (empty($products)): ?>

    <p>No products found.</p>

<?php else: ?>

    <div class="admin-product-list">

        <?php foreach ($products as $product): ?>

            <div class="admin-product-card">

                <?php if (!empty($product['image'])): ?>
                    <img
                        src="/public/uploads/<?php echo htmlspecialchars($product['image']); ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php endif; ?>

                <div>
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>

                    <p>
                        Seller:
                        <?php echo htmlspecialchars($product['seller_name'] ?? 'Unknown'); ?>
                        (<?php echo htmlspecialchars($product['seller_email'] ?? 'No email'); ?>)
                    </p>

                    <p>
                        Category:
                        <?php echo htmlspecialchars($product['category_name'] ?? 'No category'); ?>
                    </p>

                    <p>
                        Price: R<?php echo number_format($product['price'], 2); ?>
                    </p>

                    <p>
                        Stock: <?php echo htmlspecialchars($product['stock']); ?>
                    </p>

                    <p>
                        Current Status:
                        <strong><?php echo htmlspecialchars($product['status']); ?></strong>
                    </p>

                    <form action="/public/index.php" method="POST">
                        <input type="hidden" name="action" value="update-product-status">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">

                        <select name="status" required>
                            <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>
                                Active
                            </option>

                            <option value="inactive" <?php echo $product['status'] === 'inactive' ? 'selected' : ''; ?>>
                                Inactive
                            </option>
                        </select>

                        <br><br>

                        <button type="submit">Update Status</button>
                    </form>
                </div>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>