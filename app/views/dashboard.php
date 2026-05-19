<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h1>Dashboard</h1>

<p>
Welcome,
<?php echo $_SESSION['user_name']; ?>
</p>

<p>
Role:
<?php echo $_SESSION['user_role']; ?>
</p>

<a href="/public/index.php?page=logout">
    Logout
</a>

</body>
</html>