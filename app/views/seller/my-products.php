<?php $title = 'My Products'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>My Products</h1>
        <p>View and manage your product listings.</p>
    </div>

    <a class="btn" href="/public/index.php?page=add-product">Add Product</a>
</div>

<?php if (empty($products)): ?>

    <div class="card empty-state">
        <h2>No products yet</h2>
        <p>Add your first product to start selling.</p>
        <a class="btn" href="/public/index.php?page=add-product">Add Product</a>
    </div>

<?php else: ?>

    <div class="product-grid">

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

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>