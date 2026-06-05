<?php $title = 'Register'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<section class="auth-page">

    <div class="auth-card card">

        <h1>Create your account</h1>

        <p class="auth-subtitle">
            Join the marketplace as a buyer or seller.
        </p>

        <form action="/index.php" method="POST" class="auth-form">

            <?php echo csrfField(); ?>

            <input type="hidden" name="action" value="register">

            <label>
                Full Name
                <input type="text" name="name" placeholder="Your full name" required>
            </label>

            <label>
                Email Address
                <input type="email" name="email" placeholder="you@example.com" required>
            </label>

            <label>
                Password
                <input type="password" name="password" placeholder="Create a password" required>
            </label>

            <label>
                Account Type
                <select name="role" required>
                    <option value="buyer">Buyer</option>
                    <option value="seller">Seller</option>
                </select>
            </label>

            <button type="submit">Create Account</button>

        </form>

        <p class="auth-footer-text">
            Already have an account?
            <a href="/index.php?page=login">Login</a>
        </p>

    </div>

</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>