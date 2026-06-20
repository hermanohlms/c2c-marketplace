<?php

function generatePayfastSignature($data, $passphrase = '')
{
    unset($data['signature']);

    foreach ($data as $key => $value) {
        if ($value === '' || $value === null) {
            unset($data[$key]);
        }
    }

    $signatureString = http_build_query($data);

    if (trim($passphrase) !== '') {
        $signatureString .= '&passphrase=' . urlencode(trim($passphrase));
    }

    return md5($signatureString);
}

function verifyPayfastItnSignature($data, $passphrase = '')
{
    $pfParamString = '';

    foreach ($data as $key => $value) {
        if ($key === 'signature') {
            break;
        }

        $pfParamString .= $key . '=' . urlencode($value) . '&';
    }

    $pfParamString = substr($pfParamString, 0, -1);

    if (trim($passphrase) !== '') {
        $pfParamString .= '&passphrase=' . urlencode(trim($passphrase));
    }

    return md5($pfParamString);
}
