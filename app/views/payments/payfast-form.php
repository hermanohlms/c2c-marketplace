<?php $title = 'Redirecting to PayFast'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>Redirecting to PayFast...</h1>

<form id="payfast-form" action="<?php echo htmlspecialchars($url); ?>" method="POST">
    <?php foreach ($data as $name => $value): ?>
        <input
            type="hidden"
            name="<?php echo htmlspecialchars($name); ?>"
            value="<?php echo htmlspecialchars($value); ?>">
    <?php endforeach; ?>

    <button type="submit">Pay Now</button>
</form>

<script>
    document.getElementById('payfast-form').submit();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>