<!DOCTYPE html>
<html>

<head>
    <title><?php echo $title ?? 'E-Commerce'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>


    <nav>

        <a href="/public/index.php?page=home">Home</a> |
        <a href="/public/index.php?page=shop">Shop</a> |

        <?php if (!isset($_SESSION['user_id'])): ?>

            <a href="/public/index.php">Register</a> |
            <a href="/public/index.php?page=login">Login</a> |

        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>

            <?php if ($_SESSION['user_role'] === 'seller'): ?>
                <a href="/public/index.php?page=dashboard">Seller Dashboard</a> |
                <a href="/public/index.php?page=seller-orders">Incoming Orders</a> |
            <?php endif; ?>

            <?php if ($_SESSION['user_role'] === 'buyer'): ?>
                <a href="/public/index.php?page=my-orders">My Orders</a> |
                <a href="/public/index.php?page=wishlist">Wishlist</a> |
            <?php endif; ?>

            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="/public/index.php?page=admin-dashboard">Admin Dashboard</a> |
            <?php endif; ?>

            <a href="/public/index.php?page=cart">
                Cart (<span id="cart-count">0</span>)
            </a> |
            <a href="/public/index.php?page=logout">Logout</a> |

            <span>
                Logged in as:
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                (<?php echo htmlspecialchars($_SESSION['user_role']); ?>)
            </span>

        <?php endif; ?>

    </nav>


    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert error">
            <?php echo htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert success">
            <?php echo htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <main class="container">



</body>