<?php
namespace product;
require_once "../service/ImageService.php";
session_start();

$service = new ImageService;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['method']) && $_POST['method'] === 'upload') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $service->saveImage();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No file uploaded or upload error']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}