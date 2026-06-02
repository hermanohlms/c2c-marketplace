<?php $title = 'Seller Orders'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Incoming Orders</h1>
        <p>Manage orders that include your products.</p>
    </div>

    <a class="btn btn-secondary" href="/public/index.php?page=dashboard">Back to Dashboard</a>
</div>

<?php if (empty($orders)): ?>

    <div class="card empty-state">
        <h2>No incoming orders yet</h2>
        <p>When buyers purchase your products, orders will appear here.</p>
    </div>

<?php else: ?>

    <div class="order-list">

        <?php foreach ($orders as $order): ?>

            <div class="order-card">

                <div class="order-card-header">
                    <div>
                        <h3>Order #<?php echo htmlspecialchars($order['order_id']); ?></h3>
                        <p><?php echo formatDateTime($order['created_at']); ?></p>
                    </div>

                    <span class="status-pill">
                        <?php echo htmlspecialchars($order['status']); ?>
                    </span>
                </div>

                <div class="order-item">

                    <?php if (!empty($order['product_image'])): ?>
                        <img
                            src="/public/uploads/<?php echo htmlspecialchars($order['product_image']); ?>"
                            alt="<?php echo htmlspecialchars($order['product_name']); ?>">
                    <?php endif; ?>

                    <div>
                        <strong><?php echo htmlspecialchars($order['product_name']); ?></strong>
                        <p>
                            <strong>Quantity:</strong> <?php echo htmlspecialchars($order['quantity']); ?>
                        </p>
                        <p>
                            <strong>Price:</strong> R<?php echo number_format($order['price'], 2); ?>
                        </p>
                    </div>



                </div>

                <div class="delivery-address-card">
                    <h4>Delivery Address</h4>

                    <p>
                        <strong>Name:</strong>
                        <?php echo htmlspecialchars($order['delivery_name'] ?? ''); ?>
                    </p>

                    <p>
                        <strong>Phone:</strong>
                        <?php echo htmlspecialchars($order['delivery_phone'] ?? ''); ?>
                    </p>

                    <p>
                        <strong>Address:</strong>
                        <?php echo htmlspecialchars($order['address_line1'] ?? ''); ?>
                    </p>

                    <?php if (!empty($order['address_line2'])): ?>
                        <p>
                            <?php echo htmlspecialchars($order['address_line2']); ?>
                        </p>
                    <?php endif; ?>

                    <p>
                        <?php echo htmlspecialchars($order['city'] ?? ''); ?>,
                        <?php echo htmlspecialchars($order['province'] ?? ''); ?>,
                        <?php echo htmlspecialchars($order['postal_code'] ?? ''); ?>
                    </p>

                    <?php if (!empty($order['shipping_notes'])): ?>
                        <p>
                            <strong>Notes:</strong>
                            <?php echo htmlspecialchars($order['shipping_notes']); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <form action="/public/index.php" method="POST" class="status-form">

                    <?php echo csrfField(); ?>

                    <input type="hidden" name="action" value="update-order-status">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">

                    <label>
                        Update Status
                        <select name="status" required>
                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="paid" <?php echo $order['status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </label>

                    <button type="submit">Update</button>
                </form>

            </div>

        <?php endforeach; ?>

    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a
                    class="<?php echo $i === $currentPage ? 'active' : ''; ?>"
                    href="/public/index.php?page=seller-orders&p=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>