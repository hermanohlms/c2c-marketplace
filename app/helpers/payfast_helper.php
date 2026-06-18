<?php

function generatePayfastSignature($data, $passphrase = '')
{
    unset($data['signature']);

    $pfOutput = '';

    foreach ($data as $key => $value) {
        if ($value !== '' && $value !== null) {
            $pfOutput .= $key . '=' . urlencode(trim((string)$value)) . '&';
        }
    }

    $pfOutput = rtrim($pfOutput, '&');

    if (!empty($passphrase)) {
        $pfOutput .= '&passphrase=' . urlencode(trim($passphrase));
    }

    return md5($pfOutput);
}
