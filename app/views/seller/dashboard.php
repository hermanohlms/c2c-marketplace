<?php $title = 'Seller Dashboard'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>Seller Dashboard</h1>

<p>
    Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
</p>

<div class="analytics-grid">

    <div class="analytics-card">
        <h3>Total Revenue</h3>
        <p>R<?php echo number_format($analytics['total_revenue'], 2); ?></p>
    </div>

    <div class="analytics-card">
        <h3>Total Orders</h3>
        <p><?php echo htmlspecialchars($analytics['total_orders']); ?></p>
    </div>

    <div class="analytics-card">
        <h3>Items Sold</h3>
        <p><?php echo htmlspecialchars($analytics['total_items_sold']); ?></p>
    </div>

    <div class="analytics-card">
        <h3>Active Products</h3>
        <p><?php echo htmlspecialchars($activeProductsCount); ?></p>
    </div>

</div>

<hr>

<h2>Seller Actions</h2>

<a href="/public/index.php?page=add-product">Add Product</a>
<br><br>

<a href="/public/index.php?page=my-products">My Products</a>
<br><br>

<a href="/public/index.php?page=seller-orders">Incoming Orders</a>

<hr>

<h2>Top Selling Products</h2>

<?php if (empty($topProducts)): ?>

    <p>No sales yet.</p>

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

<hr>

<h2>Low Stock Alerts</h2>

<?php if (empty($lowStockProducts)): ?>

    <p>No low stock products.</p>

<?php else: ?>

    <div class="low-stock-list">

        <?php foreach ($lowStockProducts as $product): ?>

            <div class="low-stock-card">
                <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                <p>Stock remaining: <?php echo htmlspecialchars($product['stock']); ?></p>
            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>