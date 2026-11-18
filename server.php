<?php

/**
 * ZebLearn Development & Audit Server Dispatcher
 * Emulates Apache mod_rewrite, mod_deflate, and mod_headers for PHP CLI Server.
 * Used automatically by `php artisan serve`.
 */
$publicPath = __DIR__.'/public';

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

$filePath = $publicPath.$uri;

// If the file exists in public/
if ($uri !== '/' && file_exists($filePath) && ! is_dir($filePath)) {
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    $contentTypes = [
        'css' => 'text/css; charset=UTF-8',
        'js' => 'application/javascript; charset=UTF-8',
        'mjs' => 'application/javascript; charset=UTF-8',
        'json' => 'application/json; charset=UTF-8',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
        'avif' => 'image/avif',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'woff2' => 'font/woff2',
        'woff' => 'font/woff',
        'ttf' => 'font/ttf',
        'xml' => 'application/xml',
        'txt' => 'text/plain; charset=UTF-8',
    ];

    $contentType = $contentTypes[$extension] ?? mime_content_type($filePath);
    header("Content-Type: {$contentType}");
    header('Vary: Accept-Encoding');

    // 1. Cache-Control Headers
    if (str_starts_with($uri, '/build/assets/')) {
        // Hashed Vite assets are immutable for 1 year
        header('Cache-Control: public, max-age=31536000, immutable');
    } elseif (in_array($extension, ['woff2', 'woff', 'ttf'])) {
        header('Cache-Control: public, max-age=31536000, immutable');
    } elseif (in_array($extension, ['webp', 'avif', 'png', 'jpg', 'jpeg', 'ico', 'svg'])) {
        header('Cache-Control: public, max-age=604800, must-revalidate');
    } else {
        header('Cache-Control: public, max-age=86400');
    }

    // 2. Gzip Compression for eligible text assets
    $compressable = ['css', 'js', 'mjs', 'json', 'svg', 'xml', 'txt'];
    $acceptEncoding = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';

    if (in_array($extension, $compressable) && str_contains($acceptEncoding, 'gzip')) {
        $content = file_get_contents($filePath);
        $compressed = gzencode($content, 6);
        header('Content-Encoding: gzip');
        header('Content-Length: '.strlen($compressed));
        echo $compressed;
        exit;
    }

    header('Content-Length: '.filesize($filePath));
    readfile($filePath);
    exit;
}

// Log request and forward to Laravel front controller
$formattedDateTime = date('D M j H:i:s Y');
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$remoteAddress = ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1').':'.($_SERVER['REMOTE_PORT'] ?? '8000');
file_put_contents('php://stdout', "[$formattedDateTime] $remoteAddress [$requestMethod] URI: $uri\n");

require_once $publicPath.'/index.php';
