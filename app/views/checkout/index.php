<?php $title = 'Checkout'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Checkout</h1>
        <p>Enter your delivery details before continuing to payment.</p>
    </div>

    <a class="btn btn-secondary" href="/public/index.php?page=cart">Back to Cart</a>
</div>

<section class="checkout-layout">

    <form action="/public/index.php" method="POST" class="card stack-form">

        <?php echo csrfField(); ?>

        <input type="hidden" name="action" value="checkout">

        <h2>Delivery Details</h2>

        <label>
            Full Name
            <br>
            <input type="text" name="delivery_name" required>
        </label>

        <label>
            Phone Number
            <br>
            <input type="text" name="delivery_phone" required>
        </label>

        <label>
            Address Line 1
            <br>
            <input type="text" name="address_line1" required>
        </label>

        <label>
            Address Line 2
            <br>
            <input type="text" name="address_line2">
        </label>

        <label>
            City
            <br>
            <input type="text" name="city" required>
        </label>

        <label>
            Province
            <br>
            <input type="text" name="province" required>
        </label>

        <label>
            Postal Code
            <br>
            <input type="text" name="postal_code" required>
        </label>

        <label>
            Shipping Notes
            <br>
            <textarea name="shipping_notes" placeholder="Optional delivery notes"></textarea>
        </label>

        <button type="submit">Continue to Payment</button>

    </form>

</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>