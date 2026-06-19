<?php $title = 'Cart'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>Your Cart</h1>

<?php
$subtotal = 0;
$deliveryFee = 49.99;
?>

<?php if (empty($cartItems)): ?>

    <div class="card empty-cart">
        <h2>Your cart is empty</h2>
        <p>Start browsing products and add something you like.</p>
        <a class="btn" href="/index.php?page=shop">Continue Shopping</a>
    </div>

<?php else: ?>

    <section class="cart-page">

        <form action="/index.php" method="POST" class="cart-items-form">
            <input type="hidden" name="action" value="update-cart">

            <?php echo csrfField(); ?>

            <div class="cart-items-list">

                <?php foreach ($cartItems as $item): ?>

                    <?php
                    $itemSubtotal = $item['price'] * $item['quantity'];
                    $subtotal += $itemSubtotal;
                    ?>

                    <?php
                    $total = $itemSubtotal + $deliveryFee;
                    ?>


                    <div class="cart-item-card">

                        <?php if (!empty($item['image'])): ?>
                            <img
                                src="/uploads/<?php echo htmlspecialchars($item['image']); ?>"
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
                                    name="quantities[<?php echo htmlspecialchars($item['product_id']); ?>]"
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
            <div class="summary-row">
                <span>Subtotal</span>
                <strong>R<?php echo number_format($subtotal, 2); ?></strong>
            </div>

            <div class="summary-row">
                <span>Delivery</span>
                <strong>R<?php echo number_format($deliveryFee, 2); ?></strong>
            </div>

            <hr>

            <div class="summary-row total-row">
                <span>Total</span>
                <strong>R<?php echo number_format($total, 2); ?></strong>
            </div>
        </aside>

    </section>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>