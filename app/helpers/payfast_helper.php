<?php

function payfastUrlEncode($value)
{
    return str_replace('%20', '+', urlencode(trim($value)));
}

function generatePayfastSignature($data, $passphrase = '')
{
    unset($data['signature']);

    $pfOutput = '';

    foreach ($data as $key => $value) {
        if ($value !== '' && $value !== null) {
            $pfOutput .= $key . '=' . payfastUrlEncode($value) . '&';
        }
    }

    $pfOutput = rtrim($pfOutput, '&');

    if ($passphrase !== '') {
        $pfOutput .= '&passphrase=' . payfastUrlEncode($passphrase);
    }

    return strtolower(md5($pfOutput));
}
