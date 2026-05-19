<?php $title = 'My Orders'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>My Orders</h1>
        <p>Track your purchases and review products you have bought.</p>
    </div>

    <a class="btn btn-secondary" href="/public/index.php?page=shop">Continue Shopping</a>
</div>

<?php if (empty($orders)): ?>

    <div class="card empty-state">
        <h2>No orders yet</h2>
        <p>Once you checkout, your orders will appear here.</p>
        <a class="btn" href="/public/index.php?page=shop">Start Shopping</a>
    </div>

<?php else: ?>

    <div class="order-list">

        <?php foreach ($orders as $order): ?>

            <div class="order-card">

                <div class="order-card-header">
                    <div>
                        <h3>Order #<?php echo htmlspecialchars($order['id']); ?></h3>
                        <p><?php echo htmlspecialchars($order['created_at']); ?></p>
                    </div>

                    <span class="status-pill">
                        <?php echo htmlspecialchars($order['status']); ?>
                    </span>
                </div>

                <div class="summary-row">
                    <span>Total</span>
                    <strong>R<?php echo number_format($order['total_amount'], 2); ?></strong>
                </div>

                <h4>Items</h4>

                <div class="order-items-list">

                    <?php foreach ($order['items'] as $item): ?>

                        <div class="order-item">

                            <?php if (!empty($item['product_image'])): ?>
                                <img
                                    src="/public/uploads/<?php echo htmlspecialchars($item['product_image']); ?>"
                                    alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                            <?php endif; ?>

                            <div>
                                <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                                <p>Price: R<?php echo number_format($item['price'], 2); ?></p>

                                <a href="/public/index.php?page=product&id=<?php echo htmlspecialchars($item['product_id']); ?>">
                                    Review Product
                                </a>
                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>