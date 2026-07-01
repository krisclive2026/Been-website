<?php
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $name         = trim($_POST['name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $role         = trim($_POST['role'] ?? '');
    $experience   = trim($_POST['experience'] ?? '');
    $location     = trim($_POST['location'] ?? '');
    $availability = trim($_POST['availability'] ?? '');

    // availability is free text ("Immediate" or a date); the DB column is a DATE,
    // so only store it if it parses as a real YYYY-MM-DD date, otherwise NULL.
    $available_date = null;
    $d = DateTime::createFromFormat('Y-m-d', $availability);
    if ($d && $d->format('Y-m-d') === $availability) {
        $available_date = $availability;
    }

    $resume_filename = null;
    if (!empty($_FILES['resume']['name'])) {
        $original = basename($_FILES['resume']['name']);
        $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $original);
        $resume_filename = preg_replace('/[^A-Za-z0-9._-]/', '_', $email) . '_' . $safe;
        $dest = UPLOAD_FOLDER . '/' . $resume_filename;
        if (!move_uploaded_file($_FILES['resume']['tmp_name'], $dest)) {
            $resume_filename = null;
        }
    }

    $ok = execute(
        "INSERT INTO job_seekers
            (full_name, email, phone, role_applied, experience, preferred_location, available_from, resume)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
        [$name, $email, $phone, $role, $experience, $location, $available_date, $resume_filename]
    );

    if (!$ok) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Could not save to database.']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Registered successfully!']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
