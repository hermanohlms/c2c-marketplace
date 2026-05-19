<?php $title = 'Register'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>Register</h2>

<form action="/public/index.php" method="POST">

    <input type="hidden" name="action" value="register">

    <input type="text" name="name" placeholder="Name" required>
    <br><br>

    <input type="email" name="email" placeholder="Email" required>
    <br><br>

    <input type="password" name="password" placeholder="Password" required>
    <br><br>

    <select name="role">
        <option value="buyer">Buyer</option>
        <option value="seller">Seller</option>
    </select>

    <br><br>

    <button type="submit">Register</button>

</form>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>