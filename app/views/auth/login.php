<?php $title = 'Login'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>Login</h2>

<form action="/public/index.php" method="POST">

    <input type="hidden" name="action" value="login">

    <input type="email" name="email" placeholder="Email" required>
    <br><br>

    <input type="password" name="password" placeholder="Password" required>
    <br><br>

    <button type="submit">Login</button>

</form>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>