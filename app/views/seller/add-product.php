<?php $title = 'Add Product'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>Add Product</h1>
        <p>Create a new listing for buyers to discover.</p>
    </div>

    <a class="btn btn-secondary" href="/index.php?page=dashboard">Back to dashboard</a>
</div>

<section class="form-page card">

    <form action="/index.php" method="POST" enctype="multipart/form-data" class="stack-form">

        <?php echo csrfField(); ?>

        <input type="hidden" name="action" value="create-product">

        <label>
            Category
            <br>
            <select name="category_id" required>
                <option value="">Select Category</option>

                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Product Name
            <br>
            <input type="text" name="name" placeholder="Product name" required>
        </label>

        <label>
            Description
            <br>
            <textarea name="description" placeholder="Describe your product"></textarea>
        </label>

        <label>
            Price
            <br>
            <input type="number" step="0.01" name="price" placeholder="0.00" required>
        </label>

        <label>
            Stock
            <br>
            <input type="number" name="stock" placeholder="Available quantity" required>
        </label>

        <label>
            Product Image
            <br>
            <input type="file" name="image" required>
        </label>

        <button type="submit">Add Product</button>

    </form>

</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>