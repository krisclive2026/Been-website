<?php
require_once __DIR__ . '/db.php';
 
$popup_message = '';
$popup_type = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
 
    $user = query_one("SELECT * FROM employers WHERE email = ?", [$email]);
 
    if ($user && $user['password'] === $password) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = 'employer';
        $popup_message = "Welcome back! Redirecting to your dashboard.";
        $popup_type = "success";
    } elseif ($user) {
        $popup_message = "Incorrect password. Please try again.";
        $popup_type = "error";
    } else {
        $popup_message = "You are not yet registered! Please register first.";
        $popup_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Employer Login – BEEN</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #100A20;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(139,92,246,0.25);
      border-radius: 16px;
      padding: 40px 36px;
      width: 100%;
      max-width: 420px;
    }
    .login-card h2 {
      font-size: 22px;
      font-weight: 700;
      color: #F3F1FA;
      margin-bottom: 6px;
    }
    .login-card p.sub {
      font-size: 13px;
      color: rgba(202,196,224,0.55);
      margin-bottom: 28px;
    }
    label {
      display: block;
      font-size: 12px;
      font-weight: 600;
      color: rgba(202,196,224,0.75);
      margin-bottom: 6px;
      letter-spacing: .5px;
      text-transform: uppercase;
    }
    input {
      width: 100%;
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(139,92,246,0.3);
      border-radius: 8px;
      padding: 11px 14px;
      font-size: 14px;
      color: #F3F1FA;
      outline: none;
      margin-bottom: 18px;
      transition: border-color .2s;
    }
    input:focus { border-color: #8B5CF6; }
    button[type="submit"] {
      width: 100%;
      padding: 13px;
      background: linear-gradient(135deg, #8B5CF6, #22D3EE);
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 700;
      color: #100A20;
      cursor: pointer;
      letter-spacing: .5px;
      transition: opacity .2s;
    }
    button[type="submit"]:hover { opacity: .88; }
    .back-link {
      display: block;
      text-align: center;
      margin-top: 18px;
      font-size: 12px;
      color: rgba(202,196,224,0.5);
      text-decoration: none;
    }
    .back-link span { color: #8B5CF6; }
    .register-link {
      display: block;
      text-align: center;
      margin-top: 10px;
      font-size: 13px;
      color: rgba(202,196,224,0.65);
      text-decoration: none;
    }
    .register-link span { color: #22D3EE; font-weight: 700; }
    .register-link:hover span { text-decoration: underline; }
    .popup-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.55);
      z-index: 999;
      align-items: center;
      justify-content: center;
    }
    .popup-overlay.show { display: flex; }
    .popup-box {
      background: #1C1230;
      border: 1px solid rgba(139,92,246,0.35);
      border-radius: 14px;
      padding: 36px 32px;
      text-align: center;
      max-width: 340px;
      width: 90%;
      animation: popIn .25s ease;
    }
    @keyframes popIn {
      from { transform: scale(.85); opacity: 0; }
      to   { transform: scale(1);   opacity: 1; }
    }
    .popup-icon {
      width: 52px; height: 52px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 22px;
      margin: 0 auto 14px;
    }
    .popup-icon.success {
      background: linear-gradient(135deg, #8B5CF6, #22D3EE);
      color: #100A20;
    }
    .popup-icon.error {
      background: linear-gradient(135deg, #EF4444, #F97316);
      color: #fff;
    }
    .popup-box h3 {
      font-size: 16px; font-weight: 700;
      color: #F3F1FA; margin-bottom: 8px;
    }
    .popup-box p {
      font-size: 13px;
      color: rgba(202,196,224,0.65);
      line-height: 1.6;
    }
    .popup-btn {
      margin-top: 20px;
      padding: 10px 28px;
      background: linear-gradient(135deg, #8B5CF6, #22D3EE);
      border: none; border-radius: 8px;
      font-size: 13px; font-weight: 700;
      color: #100A20; cursor: pointer;
    }
  </style>
</head>
<body>
 
  <div class="login-card">
    <h2>Employer Login</h2>
    <p class="sub">Welcome back! Enter your credentials to continue.</p>
 
    <form method="POST" action="employer_login.php">
      <label>Email Address</label>
      <input type="email" name="email" placeholder="you@example.com" required />
 
      <label>Password</label>
      <input type="password" name="password" placeholder="••••••••" required />
 
      <button type="submit">Login</button>
    </form>
 
    <a href="index.php#consultancy-panel-employer" class="register-link">New consultancy? <span>Register here</span></a>
    <a href="index.php" class="back-link">← Back to <span>Home</span></a>
  </div>
 
  <?php if ($popup_message): ?>
  <div class="popup-overlay show"
       id="popupOverlay"
       data-type="<?php echo htmlspecialchars($popup_type); ?>"
       data-redirect="employer_dashboard.php">
    <div class="popup-box">
      <div class="popup-icon <?php echo htmlspecialchars($popup_type); ?>">
        <?php echo $popup_type === 'success' ? '✓' : '✕'; ?>
      </div>
      <h3><?php echo $popup_type === 'success' ? 'Login Successful!' : 'Not Registered'; ?></h3>
      <p><?php echo htmlspecialchars($popup_message); ?></p>
      <button class="popup-btn" onclick="closePopup()">OK</button>
    </div>
  </div>
  <?php endif; ?>
 
  <script>
    function closePopup() {
      var overlay = document.getElementById('popupOverlay');
      if (!overlay) return;
      overlay.classList.remove('show');
      var type = overlay.getAttribute('data-type');
      if (type === 'success') {
        var redirect = overlay.getAttribute('data-redirect');
        window.location.href = redirect;
      }
    }
  </script>
 
</body>
</html>