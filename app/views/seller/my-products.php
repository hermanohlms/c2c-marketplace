<?php $title = 'My Products'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h1>My Products</h1>

<a href="/public/index.php?page=dashboard">Back to Dashboard</a>
<br><br>

<?php if (empty($products)): ?>

    <p>You have not added any products yet.</p>

<?php else: ?>

    <?php foreach ($products as $product): ?>

        <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">

            <?php if (!empty($product['image'])): ?>
                <img 
                    src="/public/uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                    width="120"
                >
            <?php endif; ?>

            <h3><?php echo htmlspecialchars($product['name']); ?></h3>

            <p>
                Category:
                <?php echo htmlspecialchars($product['category_name'] ?? 'No category'); ?>
            </p>

            <p>
                Price: R<?php echo htmlspecialchars($product['price']); ?>
            </p>

            <p>
                Stock: <?php echo htmlspecialchars($product['stock']); ?>
            </p>

            <p>
                Status: <?php echo htmlspecialchars($product['status']); ?>
            </p>

        </div>

    <?php endforeach; ?>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>