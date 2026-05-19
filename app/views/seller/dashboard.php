<?php $title = 'Seller Dashboard'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Seller Dashboard</h1>
        <p>Track sales, manage products, and monitor inventory.</p>
    </div>

    <a class="btn" href="/public/index.php?page=add-product">Add Product</a>
</div>

<div class="analytics-grid">

    <div class="analytics-card">
        <span>Total Revenue</span>
        <strong>R<?php echo number_format($analytics['total_revenue'], 2); ?></strong>
    </div>

    <div class="analytics-card">
        <span>Total Orders</span>
        <strong><?php echo htmlspecialchars($analytics['total_orders']); ?></strong>
    </div>

    <div class="analytics-card">
        <span>Items Sold</span>
        <strong><?php echo htmlspecialchars($analytics['total_items_sold']); ?></strong>
    </div>

    <div class="analytics-card">
        <span>Active Products</span>
        <strong><?php echo htmlspecialchars($activeProductsCount); ?></strong>
    </div>

</div>

<section class="dashboard-grid">

    <div class="card">
        <h2>Seller Actions</h2>

        <div class="action-list">
            <a class="btn btn-secondary" href="/public/index.php?page=my-products">My Products</a>
            <a class="btn btn-secondary" href="/public/index.php?page=seller-orders">Incoming Orders</a>
            <a class="btn btn-secondary" href="/public/index.php?page=add-product">Add Product</a>
        </div>
    </div>

    <div class="card">
        <h2>Low Stock Alerts</h2>

        <?php if (empty($lowStockProducts)): ?>
            <p class="muted">No low stock products.</p>
        <?php else: ?>
            <div class="mini-list">
                <?php foreach ($lowStockProducts as $product): ?>
                    <div class="mini-list-item">
                        <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                        <span><?php echo htmlspecialchars($product['stock']); ?> left</span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</section>

<section class="card">
    <h2>Top Selling Products</h2>

    <?php if (empty($topProducts)): ?>

        <p class="muted">No sales yet.</p>

    <?php else: ?>

        <div class="top-products-list">

            <?php foreach ($topProducts as $product): ?>

                <div class="top-product-card">

                    <?php if (!empty($product['image'])): ?>
                        <img
                            src="/public/uploads/<?php echo htmlspecialchars($product['image']); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php endif; ?>

                    <div>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p>Sold: <?php echo htmlspecialchars($product['total_sold']); ?></p>
                        <p>Revenue: R<?php echo number_format($product['revenue'], 2); ?></p>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>