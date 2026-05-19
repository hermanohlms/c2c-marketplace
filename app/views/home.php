<?php $title = 'Home'; ?>
<?php require_once __DIR__ . '/layouts/header.php'; ?>

<section class="hero">
    <div>
        <h1>Buy and sell products with ease</h1>

        <p>
            A simple marketplace for buyers, sellers, and admins,
            built with secure accounts, product listings, carts, orders,
            reviews, and wishlists.
        </p>

        <div class="hero-actions">
            <a class="btn" href="/public/index.php?page=shop">Browse Products</a>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <a class="btn btn-secondary" href="/public/index.php">Create Account</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="hero-card card">
        <h3>Marketplace Features</h3>

        <ul>
            <li>Buyer and seller accounts</li>
            <li>Secure checkout flow</li>
            <li>Reviews and wishlists</li>
            <li>Seller analytics</li>
        </ul>
    </div>
</section>

<section>
    <h2>Shop by Category</h2>

    <div class="category-preview-grid">
        <div class="card">Electronics</div>
        <div class="card">Clothing</div>
        <div class="card">Home</div>
    </div>
</section>

<section>
    <h2>Start exploring</h2>

    <p>
        Browse the latest active products from sellers on the marketplace.
    </p>

    <a class="btn" href="/public/index.php?page=shop">Go to Shop</a>
</section>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>