<?php

require_once __DIR__ . '/env.php';

$sandbox = getenv('PAYFAST_SANDBOX') === 'true';

return [
    'merchant_id' => getenv('PAYFAST_MERCHANT_ID'),
    'merchant_key' => getenv('PAYFAST_MERCHANT_KEY'),
    'passphrase' => getenv('PAYFAST_PASSPHRASE'),

    'sandbox' => $sandbox,

    'sandbox_url' => 'https://sandbox.payfast.co.za/eng/process',
    'live_url' => 'https://www.payfast.co.za/eng/process',

    'return_url' => 'http://localhost:8080/index.php?page=payment-success',
    'cancel_url' => 'http://localhost:8080/index.php?page=payment-cancelled',
    'notify_url' => 'https://YOUR-NGROK-URL.ngrok-free.app/index.php?page=payfast-itn'
];
