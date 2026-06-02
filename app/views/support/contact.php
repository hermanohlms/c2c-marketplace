<?php $title = 'Contact Support'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>Contact Support</h1>

<section class="form-page card">
    <form action="/public/index.php" method="POST" class="stack-form">
        <?php echo csrfField(); ?>

        <input type="hidden" name="action" value="create-ticket">

        <label>
            Subject
            <br>
            <input type="text" name="subject"
                placeholder="Subject of issue" required>
        </label>

        <label>
            Message
            <br>
            <textarea name="message" placeholder="Description of issue" required></textarea>
        </label>

        <button type="submit">Submit</button>
    </form>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>