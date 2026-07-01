<?php
require_once __DIR__ . '/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: admin_login.php');
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
if ($id > 0) {
    execute("UPDATE employers SET status = 'Approved' WHERE id = ?", [$id]);
}

header('Location: admin_dashboard.php');
exit;
