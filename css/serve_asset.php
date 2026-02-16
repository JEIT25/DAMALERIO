<?php
// Get the referer from the request
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

// Define an array of valid referers (host-specific and path-only for flexibility)
$validReferers = [
    'http://localhost/DAMALERIO/php/forms/',
    'http://localhost/DAMALERIO/php/admin/',
    'http://localhost/DAMALERIO/php/superadmin/',
    'http://localhost/DAMALERIO/php/auth/',
    'http://localhost/DAMALERIO/php/pages/',
    'http://127.0.0.1/DAMALERIO/php/forms/',
    'http://127.0.0.1/DAMALERIO/php/admin/',
    'http://127.0.0.1/DAMALERIO/php/superadmin/',
    'http://127.0.0.1/DAMALERIO/php/auth/',
    'http://127.0.0.1/DAMALERIO/php/pages/',
    '/DAMALERIO/php/forms/',
    '/DAMALERIO/php/admin/',
    '/DAMALERIO/php/superadmin/',
    '/DAMALERIO/php/auth/',
    '/DAMALERIO/php/pages/',
];

// Check if the referer matches any of the valid referers
$refererValid = false;
foreach ($validReferers as $validReferer) {
    if (strpos($referer, $validReferer) !== false) {
        $refererValid = true;
        break;
    }
}

// Deny access if no valid referer is found
if (!$refererValid) {
    http_response_code(403);
    exit;
}

// Sanitize the file parameter
if (isset($_GET['file'])) {
    $file = basename($_GET['file']);
    $filePath = __DIR__ . '/' . $file;

    // Validate the file exists and is a CSS/JS file
    if (file_exists($filePath) && in_array(pathinfo($file, PATHINFO_EXTENSION), ['css', 'js'])) {
        // Serve the file with the appropriate MIME type
        $mimeType = pathinfo($file, PATHINFO_EXTENSION) === 'css' ? 'text/css' : 'application/javascript';
        header("Content-Type: $mimeType");
        readfile($filePath);
        exit;
    } else {
        http_response_code(404);
    }
} else {
    http_response_code(400);
    echo "No file specified.";
}
