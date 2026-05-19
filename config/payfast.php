<?php

return [
    'merchant_id' => '10000100',
    'merchant_key' => '46f0cd694581a',
    'passphrase' => '',
    'sandbox' => true,

    'sandbox_url' => 'https://sandbox.payfast.co.za/eng/process',
    'live_url' => 'https://www.payfast.co.za/eng/process',

    'return_url' => 'http://localhost:8080/public/index.php?page=payment-success',
    'cancel_url' => 'http://localhost:8080/public/index.php?page=payment-cancelled',
    'notify_url' => 'http://localhost:8080/public/index.php?page=payfast-itn'
];
