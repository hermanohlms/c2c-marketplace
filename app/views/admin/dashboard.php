<?php $title = 'Admin Dashboard'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>Admin Dashboard</h1>

<p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>.</p>

<a href="/public/index.php?page=admin-categories">Manage Categories</a>

<br><br>

<a href="/public/index.php?page=admin-products">Moderate Products</a>

<br><br>

<a href="/public/index.php?page=admin-users">Manage Users</a>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>