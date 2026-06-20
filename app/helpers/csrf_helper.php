<?php

function csrfToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrfField()
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken()) . '">';
}

function validateCsrf()
{
    $token = $_POST['csrf_token'] ?? '';

    if (
        empty($_SESSION['csrf_token']) ||
        empty($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        $_SESSION['error'] = "Your session expired. Please try again.";

        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $redirectTo = (strpos($referer, $_SERVER['HTTP_HOST'] ?? '') !== false)
            ? $referer
            : '/index.php?page=home';

        header("Location: " . $redirectTo);
        exit;
    }
}
