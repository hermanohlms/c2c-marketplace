<?php

function abort403()
{
    http_response_code(403);
    require __DIR__ . '/../views/errors/403.php';
    exit;
}

function abort404()
{
    http_response_code(404);
    require __DIR__ . '/../views/errors/404.php';
    exit;
}
