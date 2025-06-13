<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_FILES['recipe_image']) || $_FILES['recipe_image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit();
}

$file = $_FILES['recipe_image'];
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$maxSize = 5 * 1024 * 1024; // 5MB

// Validate file type
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
    exit();
}

// Validate file size
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File size too large. Maximum 5MB allowed.']);
    exit();
}

// Create uploads directory if it doesn't exist
$uploadDir = __DIR__ . '/../public/uploads/recipes/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('recipe_') . '.' . $extension;
$filepath = $uploadDir . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $filepath)) {
    $imageUrl = '/public/uploads/recipes/' . $filename;
    echo json_encode(['success' => true, 'image_url' => $imageUrl]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save file']);
}
?>
