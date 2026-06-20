<?php $title = 'Shop'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="shop-toolbar">

    <h1>Shop</h1>

    <form class="filter-form" action="/index.php" method="GET">

        <input type="hidden" name="page" value="shop">

        <input
            type="text"
            name="search"
            placeholder="Search products..."
            value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">

        <select name="category_id">
            <option value="">All Categories</option>

            <?php foreach ($categories as $category): ?>
                <option
                    value="<?php echo htmlspecialchars($category['id']); ?>"
                    <?php echo (($_GET['category_id'] ?? '') == $category['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="sort">
            <option value="newest" <?php echo (($_GET['sort'] ?? '') === 'newest') ? 'selected' : ''; ?>>
                Newest
            </option>

            <option value="price_low" <?php echo (($_GET['sort'] ?? '') === 'price_low') ? 'selected' : ''; ?>>
                Price: Low to High
            </option>

            <option value="price_high" <?php echo (($_GET['sort'] ?? '') === 'price_high') ? 'selected' : ''; ?>>
                Price: High to Low
            </option>
        </select>

        <button type="submit">Filter</button>

        <a class="filter-reset" href="/index.php?page=shop">Reset</a>

    </form>

</div>

<?php if (empty($products)): ?>

    <p>No products available yet.</p>

<?php else: ?>

    <div class="product-grid">

        <?php foreach ($products as $product): ?>

            <div class="product-card">

                <a href="/index.php?page=product&id=<?php echo $product['id']; ?>" class="product-image-link">
                    <?php if (!empty($product['image'])): ?>
                        <img
                            src="<?php echo htmlspecialchars(productImageUrl($product['image'])); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?>"
                            class="product-image">
                    <?php else: ?>
                        <div class="product-image-placeholder">No Image</div>
                    <?php endif; ?>
                </a>

                <div class="product-card-body">

                    <p class="product-category">
                        <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                    </p>

                    <h3 class="product-title">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h3>

                    <div class="rating-summary">
                        <span class="rating-stars">
                            <?php
                            $avg = round($product['average_rating']);
                            echo str_repeat('★', $avg);
                            echo str_repeat('☆', 5 - $avg);
                            ?>
                        </span>

                        <span class="rating-count">
                            <?php echo htmlspecialchars($product['review_count']); ?> reviews
                        </span>
                    </div>

                    <p class="product-seller">
                        Seller:
                        <a href="/index.php?page=seller&id=<?php echo htmlspecialchars($product['seller_id']); ?>">
                            <?php echo htmlspecialchars($product['seller_name'] ?? 'Unknown'); ?>
                        </a>
                    </p>

                    <div class="product-card-footer">
                        <strong class="product-price">
                            R<?php echo number_format($product['price'], 2); ?>
                        </strong>

                        <a
                            class="btn product-btn"
                            href="/index.php?page=product&id=<?php echo $product['id']; ?>">
                            View
                        </a>
                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

    <?php if ($totalPages > 1): ?>

        <div class="pagination">

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                <a
                    class="<?php echo $i === $currentPage ? 'active' : ''; ?>"
                    href="/index.php?page=shop&search=<?php echo urlencode($search); ?>&category_id=<?php echo urlencode($category_id); ?>&sort=<?php echo urlencode($sort); ?>&p=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>

            <?php endfor; ?>

        </div>

    <?php endif; ?>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>