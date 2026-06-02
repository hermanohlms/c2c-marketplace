<?php $title = 'Cart'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>Your Cart</h1>

<?php
$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<?php if (empty($cart)): ?>

    <div class="card empty-cart">
        <h2>Your cart is empty</h2>
        <p>Start browsing products and add something you like.</p>
        <a class="btn" href="/public/index.php?page=shop">Continue Shopping</a>
    </div>

<?php else: ?>

    <section class="cart-page">

        <form action="/public/index.php" method="POST" class="cart-items-form">
            <input type="hidden" name="action" value="update-cart">

            <?php echo csrfField(); ?>

            <div class="cart-items-list">

                <?php foreach ($cart as $item): ?>

                    <?php
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                    ?>

                    <div class="cart-item-card">

                        <?php if (!empty($item['image'])): ?>
                            <img
                                src="/public/uploads/<?php echo htmlspecialchars($item['image']); ?>"
                                alt="<?php echo htmlspecialchars($item['name']); ?>"
                                class="cart-item-image">
                        <?php endif; ?>

                        <div class="cart-item-info">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>

                            <p class="cart-item-price">
                                R<?php echo number_format($item['price'], 2); ?>
                            </p>

                            <label class="quantity-label">
                                Quantity
                                <input
                                    type="number"
                                    name="quantities[<?php echo htmlspecialchars($item['id']); ?>]"
                                    value="<?php echo htmlspecialchars($item['quantity']); ?>"
                                    min="0">
                            </label>

                            <p class="cart-item-subtotal">
                                Subtotal:
                                <strong>R<?php echo number_format($subtotal, 2); ?></strong>
                            </p>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <button type="submit" class="btn btn-secondary">
                Update Cart
            </button>
        </form>

        <aside class="cart-summary card">
            <h2>Order Summary</h2>

            <div class="summary-row">
                <span>Subtotal</span>
                <strong>R<?php echo number_format($total, 2); ?></strong>
            </div>

            <div class="summary-row">
                <span>Delivery</span>
                <strong>Calculated later</strong>
            </div>

            <hr>

            <div class="summary-row total-row">
                <span>Total</span>
                <strong>R<?php echo number_format($total, 2); ?></strong>
            </div>

            <a href="/public/index.php?page=checkout" class="btn checkout-btn">
                Proceed to Checkout
            </a>

            <a class="continue-link" href="/public/index.php?page=shop">
                Continue Shopping
            </a>
        </aside>

    </section>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>