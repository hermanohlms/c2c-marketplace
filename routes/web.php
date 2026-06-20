<?php

if ($action === 'register') {

    $controller->register();
} elseif ($action === 'login') {

    $controller->login();
} elseif ($page === 'dashboard') {

    if (!isset($_SESSION['user_id'])) {
        abort403();
    }

    if ($_SESSION['user_role'] !== 'seller') {
        abort403();
    }

    $analytics = $orderModel->getSellerAnalytics($_SESSION['user_id']);
    $topProducts = $orderModel->getTopSellingProducts($_SESSION['user_id']);
    $activeProductsCount = $productModel->countActiveBySeller($_SESSION['user_id']);
    $lowStockProducts = $productModel->getLowStockBySeller($_SESSION['user_id']);

    require_once __DIR__ . '/../app/views/seller/dashboard.php';
} elseif ($page === 'my-products') {

    if (!isset($_SESSION['user_id'])) {
        abort403();
    }

    if ($_SESSION['user_role'] !== 'seller') {
        abort403();
    }

    $productController->myProducts();
} elseif ($page === 'logout') {

    session_destroy();

    header("Location: /index.php?page=login");
    exit;
} elseif ($page === 'add-product') {

    if (!isset($_SESSION['user_id'])) {
        abort403();
    }

    if ($_SESSION['user_role'] !== 'seller') {
        abort403();
    }

    $productController->showAddProductForm();
} elseif ($action === 'create-product') {

    $productController->create();
} elseif ($action === 'update-stock') {

    $productController->updateStock();
} elseif ($action === 'update-seller-product-status') {

    $productController->updateSellerProductStatus();
} elseif ($page === 'edit-product') {

    $productController->edit();
} elseif ($action === 'update-product') {

    $productController->update();
} elseif ($action === 'update-store-description') {

    $sellerStoreController->updateDescription();
} elseif ($page === 'messages') {

    $messageController->inbox();
} elseif ($page === 'messages-thread') {

    $messageController->thread();
} elseif ($action === 'start-conversation') {

    $messageController->start();
} elseif ($action === 'send-message') {

    $messageController->send();
} elseif ($page === 'shop') {

    $productController->shop();
} elseif ($page === 'product') {

    $productController->show();
} elseif ($action === 'add-to-cart') {

    $cartController->add();
} elseif ($action === 'cart-count') {

    $cartController->count();
} elseif ($action === 'remove-from-cart') {

    $cartController->remove();
} elseif ($action === 'ajax-update-cart') {

    $cartController->ajaxUpdate();
} elseif ($page === 'cart') {

    $cartController->view();
} elseif ($action === 'update-cart') {

    $cartController->update();
} elseif ($page === 'checkout') {

    $checkoutController->show();
} elseif ($action === 'checkout') {

    $checkoutController->checkout();
} elseif ($page === 'checkout-success') {

    require_once __DIR__ . '/../app/views/checkout/success.php';
} elseif ($page === 'my-orders') {

    $orderController->myOrders();
} elseif ($action === 'confirm-order-received') {

    $orderController->confirmReceived();
} elseif ($page === 'seller-orders') {

    $orderController->sellerOrders();
} elseif ($action === 'update-order-status') {

    $orderController->updateStatus();
} elseif ($action === 'create-review') {

    $reviewController->create();
} elseif ($page === 'admin-dashboard') {

    $adminController->dashboard();
} elseif ($page === 'admin-categories') {

    $adminController->categories();
} elseif ($action === 'create-category') {

    $adminController->createCategory();
} elseif ($action === 'update-category') {

    $adminController->updateCategory();
} elseif ($action === 'delete-category') {

    $adminController->deleteCategory();
} elseif ($page === 'admin-products') {

    $adminController->products();
} elseif ($action === 'update-product-status') {

    $adminController->updateProductStatus();
} elseif ($page === 'payfast-start') {

    $paymentController->startPayfast();
} elseif ($page === 'payment-success') {

    $paymentController->success();
} elseif ($page === 'payment-cancelled') {

    $paymentController->cancelled();
} elseif ($page === 'wishlist') {

    $wishlistController->index();
} elseif ($action === 'add-to-wishlist') {

    $wishlistController->add();
} elseif ($action === 'wishlist-count') {

    $wishlistController->count();
} elseif ($action === 'remove-from-wishlist') {

    $wishlistController->remove();
} elseif ($page === 'admin-users') {

    $adminController->users();
} elseif ($action === 'create-ticket') {

    $supportController->create();
} elseif ($action === 'update-support-ticket') {

    $supportController->updateStatus();
} elseif ($action === 'update-user') {

    $adminController->updateUser();
} elseif ($page === 'home') {

    require_once __DIR__ . '/../app/views/home.php';
} elseif ($page === 'profile') {

    $profileController->show();
} elseif ($action === 'update-profile') {

    $profileController->update();
} elseif ($page === 'seller') {

    $sellerStoreController->show();
} elseif ($page === 'notifications') {

    $notificationController->index();
} elseif ($action === 'mark-notifications-read') {

    $notificationController->markAllRead();
} elseif ($page === 'payfast-itn') {

    $paymentController->itn();
} elseif ($page === 'seller-earnings') {

    $orderController->sellerEarnings();
} elseif ($page === 'contact') {

    require_once __DIR__ . '/../app/views/support/contact.php';
} elseif ($page === 'admin-tickets') {

    $adminController->tickets();
} elseif ($action === 'request-payout') {

    $orderController->requestPayout();
} elseif ($page === 'admin-payouts') {

    $adminController->payouts();
} elseif ($action === 'update-payout') {

    $adminController->updatePayout();
} else {

    if ($page === 'login') {

        require_once __DIR__ . '/../app/views/auth/login.php';
    } else {

        require_once __DIR__ . '/../app/views/auth/register.php';
    }
}
