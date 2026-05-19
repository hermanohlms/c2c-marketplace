<?php $title = 'Add Product'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>Add Product</h1>

<form
    action="/public/index.php"
    method="POST"
    enctype="multipart/form-data"
>

    <input type="hidden" name="action" value="create-product">

    <select name="category_id" required>
        <option value="">Select Category</option>

    <?php foreach ($categories as $category): ?>
        <option value="<?php echo $category['id']; ?>">
            <?php echo htmlspecialchars($category['name']); ?>
        </option>
    <?php endforeach; ?>
    </select>

    <br><br>

    <input
        type="text"
        name="name"
        placeholder="Product Name"
        required
    >

    <br><br>

    <textarea
        name="description"
        placeholder="Description"
    ></textarea>

    <br><br>

    <input
        type="number"
        step="0.01"
        name="price"
        placeholder="Price"
        required
    >

    <br><br>

    <input
        type="number"
        name="stock"
        placeholder="Stock"
        required
    >

    <br><br>

    <input
        type="file"
        name="image"
        required
    >

    <br><br>

    <button type="submit">
        Add Product
    </button>

</form>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>