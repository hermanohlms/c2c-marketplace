<?php $title = 'My Profile'; ?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1>My Profile</h1>
        <p>Update your account details and profile picture.</p>
    </div>
</div>

<section class="profile-layout">

    <div class="profile-summary card">

        <?php if (!empty($user['profile_image'])): ?>
            <img
                src="/uploads/<?php echo htmlspecialchars($user['profile_image']); ?>"
                alt="<?php echo htmlspecialchars($user['name']); ?>"
                class="profile-large-image">
        <?php else: ?>
            <div class="profile-placeholder-large">
                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
            </div>
        <?php endif; ?>

        <h2><?php echo htmlspecialchars($user['name']); ?></h2>
        <p><?php echo htmlspecialchars($user['email']); ?></p>
        <span class="status-pill"><?php echo htmlspecialchars($user['role']); ?></span>

    </div>

    <div class="card">

        <h2>Edit Profile</h2>

        <form action="/index.php" method="POST" enctype="multipart/form-data" class="stack-form">

            <?php echo csrfField(); ?>

            <input type="hidden" name="action" value="update-profile">

            <label>
                Name
                <input
                    type="text"
                    name="name"
                    value="<?php echo htmlspecialchars($user['name']); ?>"
                    required>
            </label>

            <label>
                Phone
                <input
                    type="text"
                    name="phone"
                    value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                    placeholder="Your phone number">
            </label>

            <label>
                Profile Picture
                <input type="file" name="profile_image" accept="image/*">
            </label>

            <button type="submit">Update Profile</button>

        </form>

    </div>

</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>