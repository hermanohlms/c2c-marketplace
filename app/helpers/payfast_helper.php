<?php

function generatePayfastSignature($data, $passphrase = '')
{
    unset($data['signature']);

    $pfOutput = '';

    foreach ($data as $key => $value) {
        if ($value !== '' && $value !== null) {
            $pfOutput .= $key . '=' . urlencode(trim($value)) . '&';
        }
    }

    $pfOutput = substr($pfOutput, 0, -1);

    if ($passphrase !== '') {
        $pfOutput .= '&passphrase=' . urlencode(trim($passphrase));
    }

    return strtolower(md5($pfOutput));
}
