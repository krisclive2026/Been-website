<?php
require_once __DIR__ . '/db.php';
 
if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: admin_login.php');
    exit;
}
 
// Payment notifications disabled
header('Location: admin_dashboard.php');
exit;
 
$payments = query_all("SELECT * FROM payments ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Notifications | BEEN Recruitment Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Inter:wght@300;400;500;600&family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --black: #0D1017;
    --dark: #111318;
    --dark2: #1A1D26;
    --dark3: #1F2330;
    --gold: #C9A84C;
    --gold-light: #E8C96A;
    --silver: #A8B2C0;
    --red: #D63031;
    --green: #2E9E5B;
    --white: #F0EDE6;
    --white2: #C8C4BC;
    --border: rgba(201,168,76,0.2);
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Inter', sans-serif; background: var(--dark); color: var(--white); min-height: 100vh; }
  header { display: flex; align-items: center; justify-content: space-between; padding: 18px 5%; background: rgba(13,16,23,0.95); border-bottom: 1px solid var(--border); }
  header h1 { font-family: 'Cinzel', serif; font-size: 22px; font-weight: 700; background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; letter-spacing: 1px; }
  .back-btn, .logout-btn { display:inline-flex; align-items:center; gap:8px; text-decoration:none; font-family:'Sora',sans-serif; font-weight:700; font-size:13px; padding:10px 20px; border-radius:6px; cursor:pointer; }
  .back-btn { background: transparent; border:1px solid var(--gold); color: var(--gold); }
  .logout-btn { background: var(--red); color: var(--white); border:none; }
  main { max-width: 1200px; margin: 0 auto; padding: 40px 5% 80px; }
  .section-title { font-family: 'Sora', sans-serif; font-size: 24px; font-weight: 700; color: var(--white); margin-bottom: 20px; }
  .table-card { background: var(--dark2); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.3); }
  .table-scroll { overflow-x: auto; }
  table { width: 100%; border-collapse: collapse; }
  thead { background: var(--dark3); }
  thead th { text-align: left; padding: 16px 24px; font-family: 'Sora', sans-serif; font-size: 12px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--gold); border-bottom: 1px solid var(--border); white-space: nowrap; }
  tbody td { padding: 16px 24px; font-size: 14px; color: var(--white2); border-bottom: 1px solid rgba(201,168,76,0.08); white-space: nowrap; }
  tbody tr:last-child td { border-bottom: none; }
  tbody tr:hover { background: rgba(201,168,76,0.04); }
  .name-cell { color: var(--white); font-weight: 600; }
  .status-badge { display: inline-block; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; }
  .status-paid { background: rgba(46,158,91,0.15); color: #4ade80; border: 1px solid rgba(46,158,91,0.4); }
  .status-pending { background: rgba(201,168,76,0.15); color: var(--gold); border: 1px solid rgba(201,168,76,0.4); }
  .empty-row td { text-align: center; padding: 36px; color: var(--silver); font-style: italic; }
  .top-bar { display:flex; align-items:center; justify-content:space-between; margin-bottom:28px; }
</style>
</head>
<body>
 
<header>
  <h1>BEEN Recruitment Admin Dashboard</h1>
  <a href="logout.php" class="logout-btn">Logout</a>
</header>
 
<main>
 
  <div class="top-bar">
    <h2 class="section-title" style="margin-bottom:0;">Payment Notifications</h2>
    <a href="admin_dashboard.php" class="back-btn">&larr; Back to Dashboard</a>
  </div>
 
  <div class="table-card">
    <div class="table-scroll">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Amount (₹)</th>
            <th>Order ID</th>
            <th>Payment ID</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($payments)): ?>
          <tr class="empty-row">
            <td colspan="7">No payments received yet.</td>
          </tr>
          <?php else: foreach ($payments as $p): ?>
          <tr>
            <td class="name-cell"><?php echo htmlspecialchars($p['name'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($p['email'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($p['amount'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($p['razorpay_order_id'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($p['razorpay_payment_id'] ?: '—'); ?></td>
            <td>
              <?php if (($p['status'] ?? '') === 'Paid'): ?>
              <span class="status-badge status-paid">Paid</span>
              <?php else: ?>
              <span class="status-badge status-pending">Pending</span>
              <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($p['created_at'] ?? ''); ?></td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
 
</main>
 
</body>
</html>