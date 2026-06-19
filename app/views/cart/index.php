<?php $title = 'Cart'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>Your Cart</h1>

<?php if (empty($cartItems)): ?>

    <div class="card empty-cart">
        <h2>Your cart is empty</h2>
        <p>Start browsing products and add something you like.</p>
        <a class="btn" href="/index.php?page=shop">Continue Shopping</a>
    </div>

<?php else: ?>

    <section class="cart-page">

        <div class="cart-items-list">

            <?php foreach ($cartItems as $item): ?>

                <?php
                $itemSubtotal = $item['price'] * $item['quantity'];
                ?>

                <div class="cart-item-card" data-product-id="<?php echo htmlspecialchars($item['product_id']); ?>">

                    <?php if (!empty($item['image'])): ?>
                        <img
                            src="/uploads/<?php echo htmlspecialchars($item['image']); ?>"
                            alt="<?php echo htmlspecialchars($item['name']); ?>"
                            class="cart-item-image">
                    <?php else: ?>
                        <div class="cart-item-image product-image-placeholder">
                            No Image
                        </div>
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
                                class="cart-quantity-input"
                                data-product-id="<?php echo htmlspecialchars($item['product_id']); ?>"
                                data-stock="<?php echo htmlspecialchars($item['stock']); ?>"
                                value="<?php echo htmlspecialchars($item['quantity']); ?>"
                                min="0"
                                max="<?php echo htmlspecialchars($item['stock']); ?>">
                        </label>

                        <p class="cart-item-subtotal">
                            Subtotal:
                            <strong>R<?php echo number_format($itemSubtotal, 2); ?></strong>
                        </p>

                        <form action="/index.php" method="POST">
                            <?php echo csrfField(); ?>

                            <input type="hidden" name="action" value="remove-from-cart">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id']); ?>">

                            <button type="submit" class="btn btn-secondary">
                                Remove
                            </button>
                        </form>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <aside class="cart-summary card">
            <h2>Order Summary</h2>

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

            <a href="/index.php?page=checkout" class="btn checkout-btn">
                Proceed to Checkout
            </a>

            <a class="continue-link" href="/index.php?page=shop">
                Continue Shopping
            </a>
        </aside>

    </section>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>