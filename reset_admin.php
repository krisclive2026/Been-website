<?php
/**
 * reset_admin.php — ONE-TIME use script to set the admin username/password
 * directly from source code, instead of using the admin_settings.php UI.
 *
 * HOW TO USE:
 *   1. Edit $new_username and $new_password below to whatever you want.
 *   2. Upload this file to your Bluehost site (same folder as index.php).
 *   3. Visit https://yourdomain.com/reset_admin.php once in your browser.
 *   4. Confirm you see the "success" message.
 *   5. DELETE this file from the server immediately after — leaving it up
 *      is a security risk since anyone could re-run it and take over the
 *      admin account.
 */

require_once __DIR__ . '/db.php';

// ------------------------------------------------------------------
// EDIT THESE TWO VALUES:
// ------------------------------------------------------------------
$new_username = 'yourNewUsername';
$new_password = 'yourNewPassword123';
// ------------------------------------------------------------------

$hashed = password_hash($new_password, PASSWORD_DEFAULT);

// Update the first admin row (id = 1). If you have multiple admin rows
// and want a different one, change the WHERE clause accordingly.
$ok = execute(
    "UPDATE admin SET username = ?, password = ? WHERE id = (SELECT id FROM (SELECT MIN(id) AS id FROM admin) t)",
    [$new_username, $hashed]
);

if ($ok) {
    echo "Success! Admin username/password updated.<br>";
    echo "Username: " . htmlspecialchars($new_username) . "<br>";
    echo "Now go delete this file (reset_admin.php) from your server.";
} else {
    echo "Something went wrong. Check that the 'admin' table exists and has at least one row.";
}
