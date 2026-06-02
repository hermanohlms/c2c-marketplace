<?php $title = 'Home'; ?>
<?php require_once __DIR__ . '/layouts/header.php'; ?>

<section class="home-hero">

    <div class="home-hero-content">

        <h1>Buy and sell locally.</h1>

        <p>
            Discover products from trusted sellers in your area, manage orders,
            chat directly, and shop securely through YourMarket.
        </p>

        <div class="hero-actions">
            <a class="btn" href="/public/index.php?page=shop">Start Shopping</a>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <a class="btn btn-secondary" href="/public/index.php?page=register">
                    Create Account
                </a>
            <?php endif; ?>
        </div>
    </div>

    <br>

    <div class="home-hero-panel card">
        <h2>Platform Features</h2>

        <div class="feature-list">
            <div>
                <strong>Secure Payments</strong>
                <span>PayFast checkout with payment validation.</span>
            </div>

            <div>
                <strong>Seller Storefronts</strong>
                <span>View sellers, products, and store activity.</span>
            </div>

            <div>
                <strong>Live Messaging</strong>
                <span>Chat with buyers and sellers before ordering.</span>
            </div>

            <div>
                <strong>Escrow Tracking</strong>
                <span>Seller earnings are held until delivery is confirmed.</span>
            </div>
        </div>
    </div>

</section>

<section class="home-section">

    <div class="section-heading">
        <h2>How it works</h2>
        <p>A simple marketplace flow for buyers and sellers.</p>
    </div>

    <div class="home-steps">
        <div class="card">
            <span class="step-number"></span>
            <h3>Browse products</h3>
            <p>Search listings, view listed products, and compare products.</p>
        </div>
        <br>
        <div class="card">
            <span class="step-number"></span>
            <h3>Checkout securely</h3>
            <p>Pay safely through PayFast and track your order status.</p>
        </div>
        <br>
        <div class="card">
            <span class="step-number"></span>
            <h3>Confirm delivery</h3>
            <p>Once received, confirm your order so seller funds can be released.</p>
        </div>
    </div>

</section>

<br>

<section class="home-section home-cta card">

    <div>
        <h2>Ready to explore the marketplace?</h2>
        <p>Find products, contact sellers, and shop with a secure order process.</p>
    </div>

    <a class="btn" href="/public/index.php?page=shop">Browse Shop</a>

</section>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>