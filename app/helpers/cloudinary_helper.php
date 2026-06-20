<?php

/**
 * @param string 
 * @return string|null
 */

function uploadImageToCloudinary($tmpFilePath)
{
    $cloudName = getenv('CLOUDINARY_CLOUD_NAME');
    $apiKey = getenv('CLOUDINARY_API_KEY');
    $apiSecret = getenv('CLOUDINARY_API_SECRET');

    if (!$cloudName || !$apiKey || !$apiSecret) {
        error_log('Cloudinary upload skipped: missing CLOUDINARY_* environment variables.');
        return null;
    }

    $timestamp = time();

    $paramsToSign = [
        'folder' => 'one-stop-shop/products',
        'timestamp' => $timestamp,
    ];

    ksort($paramsToSign);

    $signatureString = '';
    foreach ($paramsToSign as $key => $value) {
        $signatureString .= $key . '=' . $value . '&';
    }
    $signatureString = rtrim($signatureString, '&') . $apiSecret;

    $signature = sha1($signatureString);

    $postFields = [
        'file' => new CURLFile($tmpFilePath),
        'api_key' => $apiKey,
        'timestamp' => $timestamp,
        'folder' => 'one-stop-shop/products',
        'signature' => $signature,
    ];

    $url = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        error_log('Cloudinary upload cURL error: ' . $curlError);
        return null;
    }

    $data = json_decode($response, true);

    if ($httpCode !== 200 || empty($data['secure_url'])) {
        error_log('Cloudinary upload failed: ' . $response);
        return null;
    }

    return $data['secure_url'];
}
/**
 * @param string|null
 * @return string 
 */
function productImageUrl($image)
{
    if (!$image) {
        return '/assets/images/placeholder.png';
    }

    if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
        return $image;
    }

    // Legacy local filename from before the Cloudinary migration.
    return '/uploads/' . $image;
}
