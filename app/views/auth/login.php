<?php $title = 'Login'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<section class="auth-page">

    <div class="auth-card card">

        <h1>Welcome back</h1>

        <p class="auth-subtitle">
            Log in to continue shopping, managing orders, or selling products.
        </p>

        <form action="/index.php" method="POST" class="auth-form">

            <?php echo csrfField(); ?>

            <input type="hidden" name="action" value="login">

            <label>
                Email Address
                <input type="email" name="email" placeholder="you@example.com" required>
            </label>

            <label>
                Password
                <input type="password" name="password" placeholder="Enter your password" required>
            </label>

            <button type="submit">Login</button>

        </form>

        <p class="auth-footer-text">
            Don't have an account?
            <a href="/index.php?page=register">Create one</a>
        </p>

    </div>

</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>