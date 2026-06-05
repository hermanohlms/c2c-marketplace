<?php $title = 'Manage Users'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Manage Users</h1>
        <p>Control account roles and active status.</p>
    </div>

    <a class="btn btn-secondary" href="/index.php?page=admin-dashboard">Back</a>
</div>

<form action="/index.php" method="GET" class="admin-search-form">
    <input type="hidden" name="page" value="admin-users">

    <input
        type="text"
        name="search"
        placeholder="Search users..."
        value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">

    <button type="submit">Search</button>

    <?php if (!empty($_GET['search'])): ?>
        <a class="btn btn-secondary" href="/index.php?page=admin-users">
            Reset
        </a>
    <?php endif; ?>
</form>

<div class="management-grid">

    <?php foreach ($users as $user): ?>

        <div class="management-card card">

            <div>
                <h3><?php echo htmlspecialchars($user['name']); ?>
                </h3>
                <p class="muted">
                    <?php echo htmlspecialchars($user['email']); ?>
                </p>
                <p>Joined: <?php echo formatDateTime($user['created_at']); ?>
                </p>

                <form action="/index.php" method="POST" class="stack-form">

                    <?php echo csrfField(); ?>

                    <input type="hidden" name="action" value="update-user">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">

                    <label>
                        Role
                        <select name="role" required>
                            <option value="buyer" <?php echo $user['role'] === 'buyer' ? 'selected' : ''; ?>>Buyer</option>
                            <option value="seller" <?php echo $user['role'] === 'seller' ? 'selected' : ''; ?>>Seller</option>
                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </label>

                    <label>
                        Status
                        <select name="status" required>
                            <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </label>

                    <button type="submit">Update User</button>
                </form>
            </div>

        </div>

    <?php endforeach; ?>

</div>

<?php if ($totalPages > 1): ?>

    <div class="pagination">

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>

            <a
                class="<?php echo $i === $currentPage ? 'active' : ''; ?>"
                href="/index.php?page=admin-users&search=<?php echo urlencode($search); ?>&p=<?php echo $i; ?>">
                <?php echo $i; ?>
            </a>

        <?php endfor; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>