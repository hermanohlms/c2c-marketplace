<?php $title = 'Manage Categories'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Manage Categories</h1>
        <p>Create and organize product categories.</p>
    </div>

    <a class="btn btn-secondary" href="/public/index.php?page=admin-dashboard">Back</a>
</div>

<section class="management-layout">

    <div class="card">
        <h2>Add Category</h2>

        <form action="/public/index.php" method="POST" class="stack-form">

            <?php echo csrfField(); ?>

            <input type="hidden" name="action" value="create-category">

            <label>
                Category Name
                <input type="text" name="name" placeholder="Electronics" required>
            </label>

            <label>
                Description
                <textarea name="description" placeholder="Category description"></textarea>
            </label>

            <button type="submit">Add Category</button>
        </form>
    </div>

    <div class="card">
        <h2>Existing Categories</h2>

        <?php if (empty($categories)): ?>
            <p class="muted">No categories yet.</p>
        <?php else: ?>
            <div class="mini-list">
                <?php foreach ($categories as $category): ?>
                    <form action="/public/index.php" method="POST" class="stack-form">
                        <?php echo csrfField(); ?>

                        <input type="hidden" name="action" value="update-category">
                        <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category['id']); ?>">

                        <label>
                            Name
                            <input
                                type="text"
                                name="name"
                                value="<?php echo htmlspecialchars($category['name']); ?>"
                                required>
                        </label>

                        <label>
                            Description
                            <textarea name="description"><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
                        </label>

                        <button type="submit">Update Category</button>
                    </form>

                    <form action="/public/index.php" method="POST" onsubmit="return confirm('Delete this category? Products will become uncategorized.');">
                        <?php echo csrfField(); ?>

                        <input type="hidden" name="action" value="delete-category">
                        <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category['id']); ?>">

                        <button type="submit" class="btn-secondary">Delete Category</button>
                    </form>
                    <div class="mini-list-item vertical">
                        <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                        <span>
                            <?php echo formatDateTime($category['created_at']); ?></span>
                        <p>
                            <?php echo nl2br(htmlspecialchars($category['description'] ?? '')); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>