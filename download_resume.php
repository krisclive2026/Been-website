<?php
require_once __DIR__ . '/db.php';

// Original Flask app restricted this to admins only, but the employer dashboard
// also links here for employers to download applicant resumes — so both roles
// are allowed here for the feature to actually work end-to-end.
$role = $_SESSION['role'] ?? '';
if ($role !== 'admin' && $role !== 'employer') {
    header('Location: admin_login.php');
    exit;
}

$filename = basename($_GET['file'] ?? '');
$path = UPLOAD_FOLDER . '/' . $filename;

if ($filename === '' || !is_file($path)) {
    http_response_code(404);
    echo 'File not found.';
    exit;
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
