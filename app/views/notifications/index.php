<?php $title = 'Notifications'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Notifications</h1>
        <p>View recent account, order, and marketplace updates.</p>
    </div>

    <form action="/public/index.php" method="POST">
        <input type="hidden" name="action" value="mark-notifications-read">
        <button type="submit" class="btn btn-secondary">Mark All Read</button>
    </form>
</div>

<?php if (empty($notifications)): ?>

    <div class="card empty-state">
        <h2>No notifications yet</h2>
        <p>Updates will appear here when something important happens.</p>
    </div>

<?php else: ?>

    <div class="notification-list">

        <?php foreach ($notifications as $notification): ?>

            <div class="notification-card card <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>">

                <div>
                    <h3><?php echo htmlspecialchars($notification['title']); ?></h3>
                    <p><?php echo htmlspecialchars($notification['message']); ?></p>
                    <small><?php echo formatDateTime($notification['created_at']); ?></small>
                </div>

                <?php if (!$notification['is_read']): ?>
                    <span class="unread-dot"></span>
                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>