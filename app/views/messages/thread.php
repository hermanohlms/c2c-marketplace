<?php $title = 'Conversation'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Conversation</h1>

        <?php if (!empty($conversation['product_name'])): ?>
            <p>About: <?php echo htmlspecialchars($conversation['product_name']); ?></p>
        <?php endif; ?>
    </div>

    <a class="btn btn-secondary" href="/public/index.php?page=messages">
        Back to Messages
    </a>
</div>

<section class="chat-page card">

    <div class="chat-messages">

        <?php if (empty($messages)): ?>

            <p class="muted">No messages yet. Start the conversation below.</p>

        <?php else: ?>

            <?php foreach ($messages as $message): ?>

                <div class="chat-message <?php echo $message['sender_id'] == $_SESSION['user_id'] ? 'mine' : 'theirs'; ?>">

                    <?php if ($message['sender_id'] != $_SESSION['user_id']): ?>
                        <strong><?php echo htmlspecialchars($message['sender_name']); ?></strong>
                    <?php endif; ?>

                    <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>

                    <small><?php echo formatTime($message['created_at']); ?></small>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

    <form action="/public/index.php" method="POST" class="chat-form">

        <?php echo csrfField(); ?>

        <input type="hidden" name="action" value="send-message">
        <input type="hidden" name="conversation_id" value="<?php echo htmlspecialchars($conversation['id']); ?>">

        <textarea name="message" placeholder="Type your message..." required></textarea>

        <button type="submit">Send</button>
    </form>

</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>