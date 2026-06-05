<?php $title = 'Payout Requests'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Payout Requests</h1>
        <p>Approve or reject seller payout requests.</p>
    </div>

    <a class="btn btn-secondary" href="/index.php?page=admin-dashboard">Back</a>
</div>

<?php if (empty($payouts)): ?>

    <div class="card empty-state">
        <h2>No payout requests</h2>
        <p>Seller payout requests will appear here.</p>
    </div>

<?php else: ?>

    <div class="management-grid">

        <?php foreach ($payouts as $payout): ?>

            <div class="card management-card">

                <div>
                    <h3><?php echo htmlspecialchars($payout['seller_name']); ?></h3>

                    <p class="muted">
                        <?php echo htmlspecialchars($payout['seller_email']); ?>
                    </p>

                    <p>
                        <strong>Amount:</strong>
                        R<?php echo number_format($payout['amount'], 2); ?>
                    </p>

                    <p>
                        <strong>Status:</strong>
                        <span class="status-pill">
                            <?php echo htmlspecialchars($payout['status']); ?>
                        </span>
                    </p>

                    <p class="muted">
                        Requested: <?php echo formatDateTime($payout['created_at']); ?>
                    </p>

                    <?php if (!empty($payout['paid_at'])): ?>
                        <p class="muted">
                            Paid: <?php echo formatDateTime($payout['paid_at']); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($payout['status'] === 'pending'): ?>

                        <form action="/index.php" method="POST" class="payout-actions">
                            <?php echo csrfField(); ?>

                            <input type="hidden" name="action" value="update-payout">
                            <input type="hidden" name="payout_id" value="<?php echo htmlspecialchars($payout['id']); ?>">

                            <button type="submit" name="decision" value="approve">
                                Approve
                            </button>

                            <button type="submit" name="decision" value="reject" class="btn-secondary">
                                Reject
                            </button>
                        </form>

                    <?php endif; ?>
                </div>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>