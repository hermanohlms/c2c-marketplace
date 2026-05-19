<?php $title = 'Manage Categories'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>Manage Categories</h1>

<a href="/public/index.php?page=admin-dashboard">Back to Admin Dashboard</a>

<br><br>

<form action="/public/index.php" method="POST" class="category-form">
    <input type="hidden" name="action" value="create-category">

    <input type="text" name="name" placeholder="Category name" required>

    <br><br>

    <textarea name="description" placeholder="Category description"></textarea>

    <br><br>

    <button type="submit">Add Category</button>
</form>

<hr>

<h2>Existing Categories</h2>

<?php if (empty($categories)): ?>

    <p>No categories yet.</p>

<?php else: ?>

    <div class="category-list">

        <?php foreach ($categories as $category): ?>

            <div class="category-card">
                <h3><?php echo htmlspecialchars($category['name']); ?></h3>

                <p>
                    <?php echo nl2br(htmlspecialchars($category['description'] ?? '')); ?>
                </p>

                <small>
                    Created: <?php echo htmlspecialchars($category['created_at']); ?>
                </small>
            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>