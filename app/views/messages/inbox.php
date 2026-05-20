<?php $title = 'Messages'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Messages</h1>
        <p>View conversations between buyers and sellers.</p>
    </div>
</div>

<?php if (empty($conversations)): ?>

    <div class="card empty-state">
        <h2>No messages yet</h2>
        <p>Your conversations will appear here.</p>
    </div>

<?php else: ?>

    <div class="message-list">

        <?php foreach ($conversations as $conversation): ?>

            <?php
            $otherName = $_SESSION['user_role'] === 'buyer'
                ? $conversation['seller_name']
                : $conversation['buyer_name'];
            ?>

            <a
                class="message-card card"
                href="/public/index.php?page=messages-thread&id=<?php echo htmlspecialchars($conversation['id']); ?>">
                <div>
                    <h3><?php echo htmlspecialchars($otherName); ?></h3>

                    <?php if (!empty($conversation['product_name'])): ?>
                        <p class="muted">
                            Product: <?php echo htmlspecialchars($conversation['product_name']); ?>
                        </p>
                    <?php endif; ?>

                    <p>
                        <?php echo htmlspecialchars($conversation['last_message'] ?? 'No messages yet.'); ?>
                    </p>
                </div>

                <?php echo formatDateTime($conversation['last_message_at'] ?? $conversation['created_at']); ?>
            </a>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>