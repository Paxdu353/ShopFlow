<?php

// Let PHP's development server serve static assets with their proper MIME type.
$publicDir = realpath(dirname(__DIR__).'/public');
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
$file = realpath($publicDir.$path);

if ($file !== false && str_starts_with($file, $publicDir.DIRECTORY_SEPARATOR) && is_file($file)) {
    return false;
}

require $publicDir.'/index.php';
