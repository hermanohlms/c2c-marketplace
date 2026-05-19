<?php $title = 'Cart'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>Your Cart</h1>

<?php
$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<?php if (empty($cart)): ?>

    <p>Your cart is empty.</p>

    <a href="/public/index.php?page=shop">Continue Shopping</a>

<?php else: ?>

    <form action="/public/index.php" method="POST">
        <input type="hidden" name="action" value="update-cart">

        <div class="cart-list">

            <?php foreach ($cart as $item): ?>

                <?php
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
                ?>

                <div class="cart-item">

                    <?php if (!empty($item['image'])): ?>
                        <img
                            src="/public/uploads/<?php echo htmlspecialchars($item['image']); ?>"
                            alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <?php endif; ?>

                    <div>
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>

                        <p>Price: R<?php echo htmlspecialchars($item['price']); ?></p>

                        <label>
                            Quantity:
                            <input
                                type="number"
                                name="quantities[<?php echo $item['id']; ?>]"
                                value="<?php echo htmlspecialchars($item['quantity']); ?>"
                                min="0">
                        </label>

                        <p>Subtotal: R<?php echo number_format($subtotal, 2); ?></p>


                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <button type="submit">Update Cart</button>
    </form>

    <h2>Total: R<?php echo number_format($total, 2); ?></h2>

    <form action="/public/index.php" method="POST">
        <input type="hidden" name="action" value="checkout">
        <button type="submit">Proceed to Checkout</button>
    </form>

    <a href="/public/index.php?page=shop">Continue Shopping</a>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>