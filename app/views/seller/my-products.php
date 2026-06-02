<?php $title = 'My Products'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>My Products</h1>
        <p>View and manage your product listings.</p>
    </div>

    <div class="header-actions">
        <a
            href="/public/index.php?page=dashboard"
            class="btn btn-secondary">
            Back to Dashboard
        </a>
    </div>
</div>

<?php if (empty($products)): ?>

    <div class="card empty-state">
        <h2>No products yet</h2>
        <p>Add your first product to start selling.</p>
        <a class="btn" href="/public/index.php?page=add-product">Add Product</a>
    </div>

<?php else: ?>

    <div class="product-grid seller-products-grid">

        <?php foreach ($products as $product): ?>

            <div class="product-card">

                <?php if (!empty($product['image'])): ?>
                    <img
                        src="/public/uploads/<?php echo htmlspecialchars($product['image']); ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                        class="product-image">
                <?php endif; ?>

                <div class="product-card-body">
                    <p class="product-category">
                        <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                    </p>

                    <h3 class="product-title">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h3>

                    <p class="product-seller">
                        Stock: <?php echo htmlspecialchars($product['stock']); ?>
                    </p>

                    <div class="product-card-footer">
                        <strong class="product-price">
                            R<?php echo number_format($product['price'], 2); ?>
                        </strong>

                        <span class="status-pill">
                            <?php echo htmlspecialchars($product['status']); ?>
                        </span>
                    </div>
                </div>

                <a
                    class="btn btn-secondary"
                    href="/public/index.php?page=edit-product&id=<?php echo htmlspecialchars($product['id']); ?>">
                    Edit Product
                </a>


                <form action="/public/index.php" method="POST" class="inline-update-form">

                    <?php echo csrfField(); ?>

                    <input type="hidden" name="action" value="update-stock">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">

                    <label>
                        Stock
                        <input
                            type="number"
                            name="stock"
                            value="<?php echo htmlspecialchars($product['stock']); ?>"
                            min="0"
                            required>
                    </label>



                    <button type="submit">Update Stock</button>
                </form>



                <form action="/public/index.php" method="POST" class="inline-update-form">

                    <?php echo csrfField(); ?>

                    <input type="hidden" name="action" value="update-seller-product-status">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">

                    <select name="status" required>
                        <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>
                            Active
                        </option>

                        <option value="inactive" <?php echo $product['status'] === 'inactive' ? 'selected' : ''; ?>>
                            Inactive
                        </option>
                    </select>

                    <button type="submit">
                        <?php echo $product['status'] === 'active' ? 'Remove from Shop' : 'Reactivate'; ?>
                    </button>
                </form>


            </div>



        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>