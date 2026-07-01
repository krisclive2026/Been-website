<?php
require_once __DIR__ . '/db.php';

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: admin_login.php');
    exit;
}

$admin_id = $_SESSION['user_id'];
$admin    = query_one("SELECT * FROM admin WHERE id = ?", [$admin_id]);

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = trim($_POST['current_password'] ?? '');
    $new_username      = trim($_POST['new_username'] ?? '');
    $new_password      = trim($_POST['new_password'] ?? '');
    $confirm_password  = trim($_POST['confirm_password'] ?? '');

    // 1. Verify current password (supports hashed or legacy plaintext value)
    $current_ok = false;
    if ($admin) {
        if (password_verify($current_password, $admin['password'])) {
            $current_ok = true;
        } elseif ($current_password === $admin['password']) {
            $current_ok = true;
        }
    }

    if (!$current_ok) {
        $error = 'Current password is incorrect.';
    } elseif ($new_username === '') {
        $error = 'Username cannot be empty.';
    } elseif (strlen($new_password) < 6) {
        $error = 'New password must be at least 6 characters.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'New password and confirmation do not match.';
    } else {
        // Make sure the new username isn't already used by a different admin row
        $clash = query_one("SELECT id FROM admin WHERE username = ? AND id != ?", [$new_username, $admin_id]);
        if ($clash) {
            $error = 'That username is already taken.';
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $ok = execute(
                "UPDATE admin SET username = ?, password = ? WHERE id = ?",
                [$new_username, $hashed, $admin_id]
            );
            if ($ok) {
                $success = 'Credentials updated successfully. Use your new username and password next time you log in.';
                $admin['username'] = $new_username; // refresh displayed value
            } else {
                $error = 'Could not update credentials. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings - BEEN</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen text-white py-10">
    <div class="bg-slate-800 p-8 rounded-xl shadow-2xl w-full max-w-md border border-slate-700">
        <h2 class="text-2xl font-bold text-center mb-2 text-amber-400">ADMIN SETTINGS</h2>
        <p class="text-center text-slate-400 text-sm mb-6">Current username: <span class="text-slate-200 font-medium"><?php echo htmlspecialchars($admin['username'] ?? ''); ?></span></p>

        <?php if ($error): ?>
        <div class="bg-rose-950 border border-rose-500 text-rose-400 p-3 rounded mb-4 text-sm">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="bg-emerald-950 border border-emerald-500 text-emerald-400 p-3 rounded mb-4 text-sm">
            <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>

        <form action="admin_settings.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Current Password</label>
                <input type="password" name="current_password" required class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 focus:outline-none focus:border-amber-400">
            </div>
            <hr class="border-slate-700">
            <div>
                <label class="block text-sm font-medium mb-1">New Username</label>
                <input type="text" name="new_username" required value="<?php echo htmlspecialchars($admin['username'] ?? ''); ?>" class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">New Password</label>
                <input type="password" name="new_password" required minlength="6" class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Confirm New Password</label>
                <input type="password" name="confirm_password" required minlength="6" class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 focus:outline-none focus:border-amber-400">
            </div>
            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold py-2.5 rounded transition mt-4">Save Changes</button>
        </form>
        <div class="text-center mt-4 text-sm">
            <a href="admin_dashboard.php" class="text-slate-400 hover:text-white">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
