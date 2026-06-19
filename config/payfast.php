<?php

require_once __DIR__ . '/env.php';

$sandbox = getenv('PAYFAST_SANDBOX') === 'true';

return [
    'merchant_id' => getenv('PAYFAST_MERCHANT_ID'),
    'merchant_key' => getenv('PAYFAST_MERCHANT_KEY'),
    'passphrase' => getenv('PAYFAST_PASSPHRASE'),

    'sandbox' => getenv('PAYFAST_SANDBOX') === 'true',

    'sandbox_url' => 'https://sandbox.payfast.co.za/eng/process',
    'live_url' => 'https://www.payfast.co.za/eng/process',

    'return_url' => 'https://one-stop-shop.onrender.com/index.php?page=payment-success',
    'cancel_url' => 'https://one-stop-shop.onrender.com/index.php?page=payment-cancelled',
    'notify_url' => 'https://one-stop-shop.onrender.com/index.php?page=payfast-itn'
];
