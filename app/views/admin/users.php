<?php $title = 'Manage Users'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>Manage Users</h1>

<a href="/public/index.php?page=admin-dashboard">Back to Admin Dashboard</a>

<br><br>

<div class="admin-user-list">

    <?php foreach ($users as $user): ?>

        <div class="admin-user-card">

            <h3><?php echo htmlspecialchars($user['name']); ?></h3>

            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Created: <?php echo htmlspecialchars($user['created_at']); ?></p>

            <form action="/public/index.php" method="POST">
                <input type="hidden" name="action" value="update-user">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">

                <label>
                    Role:
                    <select name="role" required>
                        <option value="buyer" <?php echo $user['role'] === 'buyer' ? 'selected' : ''; ?>>
                            Buyer
                        </option>

                        <option value="seller" <?php echo $user['role'] === 'seller' ? 'selected' : ''; ?>>
                            Seller
                        </option>

                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>
                            Admin
                        </option>
                    </select>
                </label>

                <br><br>

                <label>
                    Status:
                    <select name="status" required>
                        <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>
                            Active
                        </option>

                        <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>
                            Inactive
                        </option>
                    </select>
                </label>

                <br><br>

                <button type="submit">Update User</button>
            </form>

        </div>

    <?php endforeach; ?>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>