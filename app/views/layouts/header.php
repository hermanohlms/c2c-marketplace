<!DOCTYPE html>
<html>

<head>
    <title><?php echo $title ?? 'E-Commerce'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/assets/css/style.css">

</head>

<body>


    <header class="site-header">

        <a class="site-logo" href="/index.php?page=home">
            One-Stop-Shop
        </a>

        <button class="nav-toggle" id="nav-toggle" aria-label="Toggle navigation">
            ☰
        </button>

        <nav class="site-nav" id="site-nav">

            <a href="/index.php?page=home">Home</a>
            <a href="/index.php?page=shop">Shop</a>


            <?php if (!isset($_SESSION['user_id'])): ?>

                <a href="/index.php?page=register">Register</a>
                <a href="/index.php?page=login">Login</a>

            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>

                <?php if ($_SESSION['user_role'] === 'seller'): ?>
                    <a href="/index.php?page=dashboard">Seller Dashboard</a>
                    <a href="/index.php?page=seller-earnings">Earnings</a>
                    <a href="/index.php?page=contact">Contact Support</a>
                <?php endif; ?>

                <?php if ($_SESSION['user_role'] === 'buyer'): ?>
                    <a href="/index.php?page=my-orders">My Orders</a>
                    <a href="/index.php?page=wishlist" class="notification-link">

                        Wishlist

                        <?php if (!empty($_SESSION['wishlist_count'])): ?>

                            <span class="notification-badge">

                                <?php echo htmlspecialchars($_SESSION['wishlist_count']); ?>

                            </span>

                        <?php endif; ?>

                    </a>

                <?php endif; ?>

                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <a href="/index.php?page=admin-dashboard">Admin Dashboard</a>
                    <a href="/index.php?page=admin-tickets" class="notification-link">
                        Support Tickets

                        <?php if (!empty($_SESSION['open_support_tickets'])): ?>
                            <span class="notification-badge">
                                <?php echo htmlspecialchars($_SESSION['open_support_tickets']); ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

                <?php if ($_SESSION['user_role'] === 'buyer'): ?>

                    <a href="/index.php?page=cart" class="notification-link">
                        Cart

                        <span id="cart-count" class="notification-badge">
                            0
                        </span>
                    </a>

                    <a href="/index.php?page=contact">Contact Support</a>

                <?php endif; ?>

                <?php
                $unreadNotifications = $_SESSION['unread_notifications'] ?? 0;
                ?>

                <a href="/index.php?page=notifications" class="notification-link">
                    Notifications
                    <?php if ($unreadNotifications > 0): ?>
                        <span class="notification-badge">
                            <?php echo htmlspecialchars($unreadNotifications); ?>
                        </span>
                    <?php endif; ?>
                </a>

                <a href="/index.php?page=messages" class="notification-link">
                    Messages
                    <?php if (!empty($_SESSION['unread_messages']) && $_SESSION['unread_messages'] > 0): ?>
                        <span class="notification-badge">
                            <?php echo htmlspecialchars($_SESSION['unread_messages']); ?>
                        </span>
                    <?php endif; ?>
                </a>

                <div class="profile-menu">

                    <button class="profile-menu-button" id="profile-menu-button" type="button">

                        <?php if (!empty($_SESSION['profile_image'])): ?>
                            <img
                                src="/uploads/<?php echo htmlspecialchars($_SESSION['profile_image']); ?>"
                                alt="<?php echo htmlspecialchars($_SESSION['user_name']); ?>"
                                class="profile-avatar-img">
                        <?php else: ?>
                            <span class="profile-avatar">
                                <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                            </span>
                        <?php endif; ?>

                    </button>

                    <div class="profile-dropdown" id="profile-dropdown">

                        <p class="profile-dropdown-name">
                            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </p>

                        <p class="profile-dropdown-role">
                            <?php echo htmlspecialchars($_SESSION['user_role']); ?>
                        </p>

                        <hr>

                        <a href="/index.php?page=profile">My Profile</a>
                        <a href="/index.php?page=logout">Logout</a>
                        <button type="button" id="theme-toggle" class="profile-theme-toggle">
                            🌙 Dark Mode
                        </button>

                    </div>

                </div>

            <?php endif; ?>

        </nav>

    </header>


    <?php if (isset($_SESSION['error'])): ?>
        <script>
            window.toastMessage = <?php echo json_encode($_SESSION['error']); ?>;
            window.toastType = "error";
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <script>
            window.toastMessage = <?php echo json_encode($_SESSION['success']); ?>;
            window.toastType = "success";
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <main class="container">