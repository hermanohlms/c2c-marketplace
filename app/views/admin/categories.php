<?php $title = 'Manage Categories'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Manage Categories</h1>
        <p>Create, search, update, and delete product categories.</p>
    </div>

    <a class="btn btn-secondary" href="/public/index.php?page=admin-dashboard">Back</a>
</div>

<section class="card category-create-block">
    <h2>Add Category</h2>

    <form action="/public/index.php" method="POST" class="stack-form">
        <?php echo csrfField(); ?>

        <input type="hidden" name="action" value="create-category">

        <label>
            Category Name
            <br>
            <input type="text" name="name" placeholder="Electronics" required>
        </label>

        <label>
            Description
            <br>
            <textarea name="description" placeholder="Category description"></textarea>
        </label>

        <button type="submit">Add Category</button>
    </form>
</section>

<section class="category-admin-section">

    <div class="section-heading">
        <h2>Existing Categories</h2>
        <p>Search and manage all available product categories.</p>
    </div>

    <form action="/public/index.php" method="GET" class="admin-search-form">
        <input type="hidden" name="page" value="admin-categories">

        <input
            type="text"
            name="search"
            placeholder="Search categories by name or description..."
            value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">

        <button type="submit">Search</button>

        <?php if (!empty($_GET['search'])): ?>
            <a class="btn btn-secondary" href="/public/index.php?page=admin-categories">
                Reset
            </a>
        <?php endif; ?>
    </form>

    <?php if (empty($categories)): ?>

        <div class="card empty-state">
            <h2>No categories found</h2>
            <p>No categories match your current search.</p>
        </div>

    <?php else: ?>

        <div class="category-list">

            <?php foreach ($categories as $category): ?>

                <div class="category-card">

                    <form action="/public/index.php" method="POST" class="stack-form">
                        <?php echo csrfField(); ?>

                        <input type="hidden" name="action" value="update-category">
                        <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category['id']); ?>">

                        <label>
                            Name
                            <br>
                            <input
                                type="text"
                                name="name"
                                value="<?php echo htmlspecialchars($category['name']); ?>"
                                required>
                        </label>

                        <label>
                            Description
                            <br>
                            <textarea name="description"><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
                        </label>

                        <div class="category-meta">
                            <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                            <span><?php echo formatDateTime($category['created_at']); ?></span>
                        </div>

                        <?php if (!empty($category['description'])): ?>
                            <p class="muted">
                                <?php echo nl2br(htmlspecialchars($category['description'])); ?>
                            </p>
                        <?php endif; ?>

                        <button type="submit">Update Category</button>
                    </form>
                    <br>
                    <form
                        action="/public/index.php"
                        method="POST"
                        onsubmit="return confirm('Delete this category? Products will become uncategorized.');">
                        <?php echo csrfField(); ?>

                        <input type="hidden" name="action" value="delete-category">
                        <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category['id']); ?>">

                        <button type="submit" class="btn-secondary delete-category-btn">
                            Delete Category
                        </button>
                    </form>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <?php if ($totalPages > 1): ?>

        <div class="pagination-wrapper">
            <div class="pagination">

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                    <a
                        class="<?php echo $i === $currentPage ? 'active' : ''; ?>"
                        href="/public/index.php?page=admin-categories&search=<?php echo urlencode($search); ?>&p=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>

                <?php endfor; ?>

            </div>
        </div>

    <?php endif; ?>

</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>