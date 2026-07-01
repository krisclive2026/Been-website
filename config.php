<?php
/**
 * config.php — central place for database credentials and Razorpay keys.
 * Edit these values for your own server before deploying.
 */

// ============================================================
// TIMEZONE — Bluehost servers default to US time, so we force
// Indian Standard Time here for every date()/time() call.
// ============================================================
date_default_timezone_set('Asia/Kolkata');

// ============================================================
// DATABASE CONFIG (MySQL)
// ============================================================
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'upryfzmy_Emplyeerecruitment');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
 
// ============================================================
// RAZORPAY CONFIG — replace these with your own keys
// Get them from: https://dashboard.razorpay.com/app/keys
// Use TEST keys (start with rzp_test_) while developing
// ============================================================
define('RAZORPAY_KEY_ID', 'rzp_test_T7KqMt0wN7DBpm');
define('RAZORPAY_KEY_SECRET', 'LmrzyKPpKeuH6rdCerdc5g72');
 
// ============================================================
// FILE UPLOAD CONFIG
// ============================================================
define('UPLOAD_FOLDER', __DIR__ . '/uploads');
if (!is_dir(UPLOAD_FOLDER)) {
    mkdir(UPLOAD_FOLDER, 0775, true);
}
 
// Session must be started before any output, in every entry script
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}