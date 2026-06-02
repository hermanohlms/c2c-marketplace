<?php $title = 'Support Tickets'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>Support Tickets</h1>

<?php foreach ($tickets as $ticket): ?>

    <div class="card">
        <h3><?php echo htmlspecialchars($ticket['subject']); ?></h3>

        <p>
            <?php echo nl2br(htmlspecialchars($ticket['message'])); ?>
        </p>

        <small>
            User: <?php echo htmlspecialchars($ticket['user_name']); ?>
            |
            Status: <?php echo htmlspecialchars($ticket['status']); ?>
            |
            <small><?php echo formatTime($ticket['created_at']); ?></small>
        </small>

        <form action="/public/index.php" method="POST" class="ticket-status-form">
            <?php echo csrfField(); ?>

            <input type="hidden" name="action" value="update-support-ticket">
            <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($ticket['id']); ?>">

            <select name="status" required>
                <option value="open" <?php echo $ticket['status'] === 'open' ? 'selected' : ''; ?>>Open</option>
                <option value="in_progress" <?php echo $ticket['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                <option value="closed" <?php echo $ticket['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
            </select>

            <button type="submit">Update</button>
        </form>
    </div>
    <br>
<?php endforeach; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>