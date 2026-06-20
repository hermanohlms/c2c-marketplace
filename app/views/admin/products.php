<?php $title = 'Admin Products'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Product Moderation</h1>
        <p>Review listings and control product visibility.</p>
    </div>

    <a class="btn btn-secondary" href="/index.php?page=admin-dashboard">Back</a>
</div>

<form action="/index.php" method="GET" class="admin-search-form">
    <input type="hidden" name="page" value="admin-products">

    <input
        type="text"
        name="search"
        placeholder="Search by product, category, seller, or email..."
        value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">

    <button type="submit">Search</button>

    <?php if (!empty($_GET['search'])): ?>
        <a class="btn btn-secondary" href="/index.php?page=admin-products">
            Reset
        </a>
    <?php endif; ?>
</form>

<?php if (empty($products)): ?>

    <div class="card empty-state">
        <h2>No products found</h2>
        <p>Products will appear here once sellers add them.</p>
    </div>

<?php else: ?>

    <div class="management-grid">

        <?php foreach ($products as $product): ?>

            <div class="management-card card">

                <?php if (!empty($product['image'])): ?>
                    <img
                        src="<?php echo htmlspecialchars(productImageUrl($product['image'])); ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php endif; ?>

                <div>
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>

                    <p class="muted">
                        Seller:
                        <?php echo htmlspecialchars($product['seller_name'] ?? 'Unknown'); ?>
                        — <?php echo htmlspecialchars($product['seller_email'] ?? 'No email'); ?>
                    </p>

                    <p>Category: <?php echo htmlspecialchars($product['category_name'] ?? 'No category'); ?></p>
                    <p>Price: <strong>R<?php echo number_format($product['price'], 2); ?></strong></p>
                    <p>Stock: <?php echo htmlspecialchars($product['stock']); ?></p>

                    <span class="status-pill">
                        <?php echo htmlspecialchars($product['status']); ?>
                    </span>

                    <form action="/index.php" method="POST" class="inline-update-form">

                        <?php echo csrfField(); ?>

                        <input type="hidden" name="action" value="update-product-status">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">

                        <select name="status" required>
                            <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $product['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>

                        <button type="submit">Update</button>
                    </form>
                </div>

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
                    href="/index.php?page=admin-products&search=<?php echo urlencode($search); ?>&p=<?php echo $i; ?>
                </a>

            <?php endfor; ?>

        </div>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>