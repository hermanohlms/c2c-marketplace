<?php $title = 'Shop'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>Shop</h1>

<form class="filter-form" action="/public/index.php" method="GET">

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
                value="<?php echo $category['id']; ?>"
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

    <a href="/public/index.php?page=shop">Reset</a>

</form>

<?php if (empty($products)): ?>

    <p>No products available yet.</p>

<?php else: ?>

    <div class="product-grid">

        <?php foreach ($products as $product): ?>

            <div class="product-card">

                <?php if (!empty($product['image'])): ?>
                    <img
                        src="/public/uploads/<?php echo htmlspecialchars($product['image']); ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php endif; ?>

                <h3><?php echo htmlspecialchars($product['name']); ?></h3>

                <p><?php echo htmlspecialchars($product['category_name'] ?? 'No category'); ?></p>

                <p><strong>R<?php echo htmlspecialchars($product['price']); ?></strong></p>

                <a href="/public/index.php?page=product&id=<?php echo $product['id']; ?>">
                    View Product
                </a>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>