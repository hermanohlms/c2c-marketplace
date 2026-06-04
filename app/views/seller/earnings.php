<?php $title = 'Seller Earnings'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Seller Earnings</h1>
        <p>Track escrow, available balance, payouts, and commission.</p>
    </div>
</div>

<section class="analytics-grid">

    <div class="analytics-card">
        <span>Held Balance</span>
        <strong>R<?php echo number_format($summary['held_balance'], 2); ?></strong>
    </div>

    <div class="analytics-card">
        <span>Available Balance</span>
        <strong>R<?php echo number_format($summary['available_balance'], 2); ?></strong>
    </div>

    <div class="analytics-card">
        <span>Paid Out Total</span>
        <strong>R<?php echo number_format($summary['paid_out_total'], 2); ?></strong>
    </div>

    <div class="analytics-card">
        <span>Commission Paid</span>
        <strong>R<?php echo number_format($summary['total_commission'], 2); ?></strong>
    </div>

</section>

</section>

<?php if ($summary['available_balance'] > 0): ?>

    <section class="card payout-request-card">

        <h2>Request Payout</h2>

        <p>
            Available balance:
            <strong>
                R<?php echo number_format($summary['available_balance'], 2); ?>
            </strong>
        </p>

        <form action="/public/index.php" method="POST" class="stack-form">

            <?php echo csrfField(); ?>

            <input type="hidden" name="action" value="request-payout">

            <label>
                Amount
                <br>
                <input
                    type="number"
                    name="amount"
                    step="0.01"
                    min="1"
                    max="<?php echo htmlspecialchars($summary['available_balance']); ?>"
                    value="<?php echo htmlspecialchars($summary['available_balance']); ?>"
                    required>
            </label>

            <button type="submit">
                Request Payout
            </button>

        </form>

    </section>

<?php endif; ?>

<section class="analytics-grid">

    <div class="analytics-card">
        <span>This Month</span>
        <strong>R<?php echo number_format($monthlySummary['this_month'], 2); ?></strong>
    </div>

    <div class="analytics-card">
        <span>Last Month</span>
        <strong>R<?php echo number_format($monthlySummary['last_month'], 2); ?></strong>
    </div>

</section>

<?php if (!empty($monthlyBreakdown)): ?>

    <section class="card monthly-breakdown-card">
        <h2>Monthly Earnings</h2>

        <div class="monthly-breakdown-list">
            <?php foreach ($monthlyBreakdown as $month): ?>
                <div class="monthly-breakdown-row">
                    <span><?php echo htmlspecialchars($month['month']); ?></span>
                    <strong>R<?php echo number_format($month['total'], 2); ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

<?php endif; ?>

<div class="page-header">
    <div>
        <h2>Transaction History</h2>
        <p>Each paid order item creates an escrow transaction.</p>
    </div>
</div>

<?php if (empty($transactions)): ?>

    <div class="card empty-state">
        <h2>No earnings yet</h2>
        <p>Your earnings will appear here after paid orders are confirmed.</p>
    </div>

<?php else: ?>

    <div class="order-list">

        <?php foreach ($transactions as $transaction): ?>

            <div class="order-card">

                <div class="order-card-header">
                    <div>
                        <h3><?php echo htmlspecialchars($transaction['product_name']); ?></h3>
                        <p>
                            Order #<?php echo htmlspecialchars($transaction['order_id']); ?>
                            • Qty <?php echo htmlspecialchars($transaction['quantity']); ?>
                        </p>
                    </div>

                    <span class="status-pill">
                        <?php echo htmlspecialchars($transaction['status']); ?>
                    </span>
                </div>

                <p>Gross: <strong>R<?php echo number_format($transaction['gross_amount'], 2); ?></strong></p>
                <p>Commission: <strong>R<?php echo number_format($transaction['commission_amount'], 2); ?></strong></p>
                <p>Seller Amount: <strong>R<?php echo number_format($transaction['seller_amount'], 2); ?></strong></p>

                <p class="muted">
                    Created: <?php echo formatDateTime($transaction['created_at']); ?>
                </p>

                <?php if (!empty($transaction['released_at'])): ?>
                    <p class="muted">
                        Released: <?php echo formatDateTime($transaction['released_at']); ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($transaction['paid_out_at'])): ?>
                    <p class="muted">
                        Paid Out: <?php echo formatDateTime($transaction['paid_out_at']); ?>
                    </p>
                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>