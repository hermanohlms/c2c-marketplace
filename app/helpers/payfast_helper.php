<?php

function payfastUrlEncode($value)
{
    return str_replace('%20', '+', urlencode(trim((string)$value)));
}

function generatePayfastSignature($data, $passphrase = '')
{
    unset($data['signature']);

    $fieldOrder = [
        'merchant_id',
        'merchant_key',
        'return_url',
        'cancel_url',
        'notify_url',
        'name_first',
        'name_last',
        'email_address',
        'cell_number',
        'm_payment_id',
        'amount',
        'item_name',
        'item_description',
        'custom_int1',
        'custom_int2',
        'custom_int3',
        'custom_int4',
        'custom_int5',
        'custom_str1',
        'custom_str2',
        'custom_str3',
        'custom_str4',
        'custom_str5',
        'email_confirmation',
        'confirmation_address',
        'payment_method'
    ];

    $pfOutput = '';

    foreach ($fieldOrder as $key) {
        if (isset($data[$key]) && $data[$key] !== '' && $data[$key] !== null) {
            $pfOutput .= $key . '=' . payfastUrlEncode($data[$key]) . '&';
        }
    }

    $pfOutput = rtrim($pfOutput, '&');

    if (trim((string)$passphrase) !== '') {
        $pfOutput .= '&passphrase=' . payfastUrlEncode($passphrase);
    }

    return md5($pfOutput);
}
