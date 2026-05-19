<?php $title = 'Checkout Success'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>Order Successful</h1>

<p>Your order has been placed successfully.</p>

<?php if (isset($_GET['order_id'])): ?>
    <p>Order number: #<?php echo htmlspecialchars($_GET['order_id']); ?></p>
<?php endif; ?>

<a href="/public/index.php?page=shop">Continue Shopping</a>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>