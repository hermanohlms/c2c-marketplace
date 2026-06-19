<?php

require_once __DIR__ . '/env.php';

$sandbox = getenv('PAYFAST_SANDBOX') === 'true';

$appUrl = strtolower(rtrim(getenv('APP_URL'), '/'));

return [
    'merchant_id' => getenv('PAYFAST_MERCHANT_ID'),
    'merchant_key' => getenv('PAYFAST_MERCHANT_KEY'),
    'passphrase' => getenv('PAYFAST_PASSPHRASE'),

    'sandbox' => getenv('PAYFAST_SANDBOX') === 'true',

    'sandbox_url' => 'https://sandbox.payfast.co.za/eng/process',
    'live_url' => 'https://www.payfast.co.za/eng/process',

    'return_url' => $appUrl . '/payment-success',
    'cancel_url' => $appUrl . '/payment-cancelled',
    'notify_url' => $appUrl . '/payfast-itn'
];
