<?php
/**
 * Admin - Image Upload API for TinyMCE
 */

require_once __DIR__ . '/../../config/config.php';

// Require authentication
requireAuth();

// Set JSON response header
header('Content-Type: application/json');

// Check if file was uploaded
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded or upload error']);
    exit;
}

// Upload the file
$upload = uploadFile($_FILES['file'], 'uploads/articles');

if ($upload['success']) {
    // Return the location for TinyMCE
    echo json_encode([
        'location' => $upload['url']
    ]);
} else {
    http_response_code(400);
    echo json_encode(['error' => $upload['error']]);
}
