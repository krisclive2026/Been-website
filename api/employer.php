<?php
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json');
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}
 
try {
    $company_name   = trim($_POST['company'] ?? '');
    $contact_person = trim($_POST['contact_person'] ?? '');
    $phone          = trim($_POST['phone'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $designation    = trim($_POST['role'] ?? '');
    $location       = trim($_POST['location'] ?? '');
    $openings       = trim($_POST['openings'] ?? '');
    $requirements   = trim($_POST['requirements'] ?? '');
    $company_type   = trim($_POST['company_type'] ?? '');
    $website        = trim($_POST['website'] ?? '');
    $password       = trim($_POST['password'] ?? '');
 
    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters.']);
        exit;
    }
 
    $existing = query_one("SELECT id FROM employers WHERE email = ?", [$email]);
    if ($existing) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'This email is already registered.']);
        exit;
    }
 
    $ok = execute(
        "INSERT INTO employers
            (fullname, email, password, phone, company_name, company_type,
             designation, location, website, openings, requirements, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')",
        [$contact_person, $email, $password, $phone, $company_name, $company_type,
         $designation, $location, $website, $openings, $requirements]
    );
 
    if (!$ok) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Could not save to database. Please contact support.']);
        exit;
    }
 
    echo json_encode(['success' => true, 'message' => 'Registered successfully!']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}