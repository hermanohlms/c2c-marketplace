<?php $title = 'Edit Product'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Edit Product</h1>
        <p>Update your product details, image, stock, and status.</p>
    </div>

    <a class="btn btn-secondary" href="/index.php?page=my-products">Back to My Products</a>
</div>

<section class="form-page card">

    <?php if (!empty($product['image'])): ?>
        <img
            src="/uploads/<?php echo htmlspecialchars($product['image']); ?>"
            alt="<?php echo htmlspecialchars($product['name']); ?>"
            style="max-width: 240px; border-radius: 14px; margin-bottom: 20px;">
    <?php endif; ?>

    <form action="/index.php" method="POST" enctype="multipart/form-data" class="stack-form">

        <?php echo csrfField(); ?>

        <input type="hidden" name="action" value="update-product">
        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">

        <label>
            Category
            <select name="category_id" required>
                <option value="">Select Category</option>

                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?php echo htmlspecialchars($category['id']); ?>"
                        <?php echo $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Product Name
            <input
                type="text"
                name="name"
                value="<?php echo htmlspecialchars($product['name']); ?>"
                required>
        </label>

        <label>
            Description
            <textarea name="description"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </label>

        <label>
            Price
            <input
                type="number"
                step="0.01"
                min="0"
                name="price"
                value="<?php echo htmlspecialchars($product['price']); ?>"
                required>
        </label>

        <label>
            Stock
            <input
                type="number"
                min="0"
                name="stock"
                value="<?php echo htmlspecialchars($product['stock']); ?>"
                required>
        </label>

        <label>
            Status
            <select name="status" required>
                <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>
                    Active
                </option>

                <option value="inactive" <?php echo $product['status'] === 'inactive' ? 'selected' : ''; ?>>
                    Inactive
                </option>
            </select>
        </label>

        <label>
            Replace Image
            <input type="file" name="image" accept="image/*">
        </label>

        <button type="submit">Save Changes</button>

    </form>

</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>