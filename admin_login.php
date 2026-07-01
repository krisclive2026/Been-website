<?php
require_once __DIR__ . '/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $user = query_one("SELECT * FROM admin WHERE username = ?", [$username]);

    // Require the password to actually match this specific user's stored
    // password (hashed, with a plaintext fallback for the legacy default
    // admin123 row from schema.sql).
    $password_ok = false;
    if ($user) {
        if (password_verify($password, $user['password'])) {
            $password_ok = true;
        } elseif ($password === $user['password']) {
            $password_ok = true;
        }
    }

    if ($user && $password_ok) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = 'admin';
        header('Location: admin_dashboard.php');
        exit;
    }
    $error = 'Invalid Credentials';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - BEEN</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-900 flex items-center justify-center h-screen text-white">
    <div class="bg-slate-800 p-8 rounded-xl shadow-2xl w-full max-w-md border border-slate-700">
        <h2 class="text-2xl font-bold text-center mb-6 text-amber-400">ADMIN LOGIN</h2>
        <?php if ($error): ?>
        <div class="bg-rose-950 border border-rose-500 text-rose-400 p-3 rounded mb-4 text-sm">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        <form action="admin_login.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Username / Email</label>
                <input type="text" name="username" required class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 focus:outline-none focus:border-amber-400">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 focus:outline-none focus:border-amber-400">
            </div>
            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold py-2.5 rounded transition mt-4">Login</button>
        </form>
        <div class="text-center mt-4 text-sm">
            <a href="index.php" class="text-slate-400 hover:text-white">← Back to Home</a>
        </div>
    </div>
</body>
</html>
