<?php $title = 'My Orders'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>My Orders</h1>

<?php if (empty($orders)): ?>

    <p>You have not placed any orders yet.</p>

    <a href="/public/index.php?page=shop">Start Shopping</a>

<?php else: ?>

    <div class="order-list">

        <?php foreach ($orders as $order): ?>

            <div class="order-card">

                <h3>Order #<?php echo htmlspecialchars($order['id']); ?></h3>

                <p>Status: <?php echo htmlspecialchars($order['status']); ?></p>

                <p>Total: R<?php echo number_format($order['total_amount'], 2); ?></p>

                <p>Date: <?php echo htmlspecialchars($order['created_at']); ?></p>

                <h4>Items</h4>

                <?php foreach ($order['items'] as $item): ?>

                    <div class="order-item">

                        <?php if (!empty($item['product_image'])): ?>
                            <img
                                src="/public/uploads/<?php echo htmlspecialchars($item['product_image']); ?>"
                                alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                        <?php endif; ?>

                        <div>
                            <p><?php echo htmlspecialchars($item['product_name']); ?></p>
                            <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                            <p>Price: R<?php echo number_format($item['price'], 2); ?></p>
                            <a href="/public/index.php?page=product&id=<?php echo htmlspecialchars($item['product_id']); ?>">
                                Review Product
                            </a>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>