<?php $title = 'Payment Successful'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<section class="payment-result-page">

    <div class="card payment-result-card payment-success-card">

        <div class="payment-result-icon">
            ✓
        </div>

        <h1>Payment Successful</h1>

        <p>
            Your payment has been completed successfully. Your order is now being processed.
        </p>

        <div class="payment-result-actions">
            <a class="btn" href="/public/index.php?page=my-orders">
                View My Orders
            </a>

            <a class="btn btn-secondary" href="/public/index.php?page=shop">
                Continue Shopping
            </a>
        </div>

    </div>

</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>