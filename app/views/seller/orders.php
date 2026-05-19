<?php $title = 'Seller Orders'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>Incoming Orders</h1>

<a href="/public/index.php?page=dashboard">Back to Dashboard</a>

<br><br>

<?php if (empty($orders)): ?>

    <p>No orders for your products yet.</p>

<?php else: ?>

    <div class="order-list">

        <?php foreach ($orders as $order): ?>

            <div class="order-card">

                <h3>Order #<?php echo htmlspecialchars($order['order_id']); ?></h3>

                <?php if (!empty($order['product_image'])): ?>
                    <img
                        src="/public/uploads/<?php echo htmlspecialchars($order['product_image']); ?>"
                        alt="<?php echo htmlspecialchars($order['product_name']); ?>"
                        style="max-width: 120px;">
                <?php endif; ?>

                <p>
                    Product:
                    <?php echo htmlspecialchars($order['product_name']); ?>
                </p>

                <p>
                    Quantity:
                    <?php echo htmlspecialchars($order['quantity']); ?>
                </p>

                <p>
                    Price:
                    R<?php echo number_format($order['price'], 2); ?>
                </p>

                <p>
                    Buyer:
                    <?php echo htmlspecialchars($order['buyer_name']); ?>
                    (<?php echo htmlspecialchars($order['buyer_email']); ?>)
                </p>

                <p>
                    Status:
                    <?php echo htmlspecialchars($order['status']); ?>
                </p>

                <form action="/public/index.php" method="POST">
                    <input type="hidden" name="action" value="update-order-status">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">

                    <select name="status" required>
                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>
                            Pending
                        </option>

                        <option value="paid" <?php echo $order['status'] === 'paid' ? 'selected' : ''; ?>>
                            Paid
                        </option>

                        <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>
                            Shipped
                        </option>

                        <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>
                            Delivered
                        </option>

                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>
                            Cancelled
                        </option>
                    </select>

                    <br><br>

                    <button type="submit">Update Status</button>
                </form>

                <p>
                    Date:
                    <?php echo htmlspecialchars($order['created_at']); ?>
                </p>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>