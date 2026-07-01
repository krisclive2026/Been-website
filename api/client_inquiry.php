<?php
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json');
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}
 
try {
    $full_name        = trim($_POST['full_name'] ?? '');
    $phone            = trim($_POST['phone'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $project_type     = trim($_POST['project_type'] ?? '');
    $budget_range     = trim($_POST['budget_range'] ?? '');
    $project_location = trim($_POST['project_location'] ?? '');
    $message          = trim($_POST['message'] ?? '');
 
    if ($full_name === '' || $phone === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Name and phone number are required.']);
        exit;
    }
 
    $ok = execute(
        "INSERT INTO clients
            (full_name, phone, email, project_type, budget_range, project_location, message)
         VALUES (?, ?, ?, ?, ?, ?, ?)",
        [$full_name, $phone, $email, $project_type, $budget_range, $project_location, $message]
    );
 
    if (!$ok) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Could not save your request. Please contact support.']);
        exit;
    }
 
    echo json_encode(['success' => true, 'message' => 'Request received!']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}