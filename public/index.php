<?php

session_start();

require_once __DIR__ . '/../config/db.php';


require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/CartController.php';
require_once __DIR__ . '/../app/controllers/CheckoutController.php';
require_once __DIR__ . '/../app/controllers/OrderController.php';
require_once __DIR__ . '/../app/controllers/ReviewController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/PaymentController.php';
require_once __DIR__ . '/../app/controllers/WishlistController.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';
require_once __DIR__ . '/../app/controllers/SellerStoreController.php';
require_once __DIR__ . '/../app/controllers/NotificationController.php';
require_once __DIR__ . '/../app/controllers/MessageController.php';


require_once __DIR__ . '/../app/models/Notification.php';
require_once __DIR__ . '/../app/models/Order.php';
require_once __DIR__ . '/../app/models/Product.php';
require_once __DIR__ . '/../app/models/Message.php';
require_once __DIR__ . '/../app/models/Escrow.php';
require_once __DIR__ . '/../app/helpers/data_helper.php';
require_once __DIR__ . '/../app/helpers/email_helper.php';
require_once __DIR__ . '/../app/helpers/csrf_helper.php';
require_once __DIR__ . '/../app/helpers/error_helper.php';
require_once __DIR__ . '/../app/controllers/SupportController.php';



$action = $_POST['action'] ?? $_GET['action'] ?? null;
$page = $_GET['page'] ?? 'home';

$csrfExemptActions = [
    'cart-count',
    'wishlist-count'
];

$csrfExemptPages = [
    'payfast-itn'
];

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    !in_array($action, $csrfExemptActions) &&
    !in_array($page, $csrfExemptPages)
) {
    validateCsrf();
}

$supportController = new SupportController($conn);
$orderModel = new Order($conn);
$productModel = new Product($conn);

//$escrowModel = new Escrow($conn);
//$escrowModel->releaseExpiredShippedOrders(14);
$messageController = new MessageController($conn);
$notificationController = new NotificationController($conn);
$sellerStoreController = new SellerStoreController($conn);
$profileController = new ProfileController($conn);
$wishlistController = new WishlistController($conn);
$paymentController = new PaymentController($conn);
$adminController = new AdminController($conn);
$reviewController = new ReviewController($conn);
$orderController = new OrderController($conn);
$checkoutController = new CheckoutController($conn);
$cartController = new CartController($conn);
$controller = new AuthController($conn);
$productController = new ProductController($conn);

if (isset($_SESSION['user_id'])) {

    $messageModel = new Message($conn);

    $_SESSION['unread_messages'] =
        $messageModel->unreadCount($_SESSION['user_id']);

    $wishlistStmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM wishlists
        WHERE user_id = :user_id
    ");

    $wishlistStmt->execute([
        ':user_id' => $_SESSION['user_id']
    ]);

    $_SESSION['wishlist_count'] =
        $wishlistStmt->fetch(PDO::FETCH_ASSOC)['total'];

    $cartCount = 0;

    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cartCount += (int)($item['quantity'] ?? 0);
        }
    }

    $_SESSION['cart_count'] = $cartCount;

    $_SESSION['open_support_tickets'] = 0;

    if (($_SESSION['user_role'] ?? '') === 'admin') {
        $ticketStmt = $conn->prepare("
            SELECT COUNT(*) AS total
            FROM support_tickets
            WHERE status = 'open'
        ");

        $ticketStmt->execute();

        $_SESSION['open_support_tickets'] =
            $ticketStmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}

require_once __DIR__ . '/../routes/web.php';
