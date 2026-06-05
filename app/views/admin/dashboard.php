<?php $title = 'Admin Dashboard'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Admin Dashboard</h1>
        <p>Manage marketplace categories, products, and users.</p>
    </div>
</div>

<section class="admin-dashboard-grid">

    <a class="admin-dashboard-card card" href="/index.php?page=admin-categories">
        <h2>Categories</h2>
        <p>Create and manage product categories.</p>
        <span>Manage Categories →</span>
    </a>

    <a class="admin-dashboard-card card" href="/index.php?page=admin-products">
        <h2>Products</h2>
        <p>Moderate product listings and update visibility.</p>
        <span>Moderate Products →</span>
    </a>

    <a class="admin-dashboard-card card" href="/index.php?page=admin-users">
        <h2>Users</h2>
        <p>Manage buyers, sellers, admins, and account status.</p>
        <span>Manage Users →</span>
    </a>

    <a href="/index.php?page=admin-payouts" class="admin-dashboard-card card">
        <h3>Payout Requests</h3>
        <p>Review and approve seller payout requests.</p>
        <span>Manage payouts →</span>
    </a>

</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>