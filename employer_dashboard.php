<?php
require_once __DIR__ . '/db.php';
 
if (($_SESSION['role'] ?? '') !== 'employer') {
    header('Location: employer_login.php');
    exit;
}
 
$user_id = $_SESSION['user_id'] ?? 0;
$employer = query_one("SELECT * FROM employers WHERE id = ?", [$user_id]);
 
if (!$employer) {
    header('Location: logout.php');
    exit;
}
 
$job_seekers = query_all("SELECT * FROM job_seekers ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Employer Dashboard | BEEN Recruitment</title>
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
    --white: #F0EDE6;
    --white2: #C8C4BC;
    --border: rgba(201,168,76,0.2);
  }
 
  * { margin: 0; padding: 0; box-sizing: border-box; }
 
  body {
    font-family: 'Inter', sans-serif;
    background: var(--dark);
    color: var(--white);
    min-height: 100vh;
  }
 
  header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 5%;
    background: rgba(13,16,23,0.95);
    border-bottom: 1px solid var(--border);
  }
  header h1 {
    font-family: 'Cinzel', serif;
    font-size: 22px;
    font-weight: 700;
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: 1px;
  }
  .logout-btn {
    background: var(--red);
    color: var(--white);
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-family: 'Sora', sans-serif;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.25s;
  }
  .logout-btn:hover {
    background: #b82a2a;
    transform: translateY(-1px);
  }
 
  main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 5% 80px;
  }
 
  .welcome-card {
    background: var(--dark2);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 24px 28px;
    margin-bottom: 36px;
  }
  .welcome-card h2 {
    font-family: 'Sora', sans-serif;
    font-size: 20px;
    color: var(--white);
    margin-bottom: 6px;
  }
  .welcome-card p {
    font-size: 13px;
    color: var(--silver);
  }
 
  .top-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
  }
  .section-title {
    font-family: 'Sora', sans-serif;
    font-size: 24px;
    font-weight: 700;
    color: var(--white);
  }
 
  .status-pill {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
  }
  .status-approved { background: rgba(46,158,91,0.15); color: #4ade80; border: 1px solid rgba(46,158,91,0.4); }
  .status-pending { background: rgba(201,168,76,0.15); color: var(--gold); border: 1px solid rgba(201,168,76,0.4); }
  .status-rejected { background: rgba(214,96,49,0.15); color: #f87171; border: 1px solid rgba(214,96,49,0.4); }
 
  .pending-notice {
    background: var(--dark2);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    color: var(--silver);
  }
  .pending-notice strong { color: var(--gold); }
  .pay-now-btn {
    margin-top: 24px;
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
    color: var(--black);
    border: none;
    padding: 14px 36px;
    border-radius: 8px;
    font-family: 'Sora', sans-serif;
    font-weight: 700;
    font-size: 14px;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .pay-now-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(201,168,76,0.3);
  }
 
  .pay-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.65);
    z-index: 9999;
    align-items: center;
    justify-content: center;
  }
  .pay-modal-box {
    background: var(--dark2);
    border: 1px solid rgba(201,168,76,0.3);
    border-radius: 14px;
    padding: 32px;
    max-width: 380px;
    width: 90%;
    position: relative;
    box-shadow: 0 20px 50px rgba(0,0,0,0.5);
  }
  .pay-modal-close {
    position: absolute;
    top: 14px;
    right: 14px;
    background: none;
    border: none;
    color: var(--silver);
    font-size: 20px;
    cursor: pointer;
    line-height: 1;
  }
  .pay-modal-title {
    font-family: 'Cinzel', serif;
    color: var(--gold);
    font-size: 20px;
    margin-bottom: 8px;
    text-align: center;
  }
  .pay-modal-sub {
    color: var(--silver);
    font-size: 13px;
    text-align: center;
    margin-bottom: 22px;
    line-height: 1.5;
  }
  .pay-amount-display {
    text-align: center;
    font-family: 'Sora', sans-serif;
    font-size: 32px;
    font-weight: 800;
    color: var(--white);
    margin-bottom: 22px;
  }
  .pay-amount-display span {
    display: block;
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: var(--silver);
    margin-top: 4px;
  }
  .pay-proceed-btn {
    width: 100%;
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
    color: var(--black);
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-family: 'Sora', sans-serif;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
  }
  .pay-proceed-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
  .pay-error-msg {
    color: #f87171;
    font-size: 12px;
    margin-top: 12px;
    text-align: center;
    display: none;
  }
 
  /* TABLE CARD */
  .table-card {
    background: var(--dark2);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,0,0,0.3);
  }
  .table-scroll {
    overflow-x: auto;
  }
  table {
    width: 100%;
    border-collapse: collapse;
  }
  thead {
    background: var(--dark3);
  }
  thead th {
    text-align: left;
    padding: 16px 24px;
    font-family: 'Sora', sans-serif;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: var(--gold);
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
  }
  tbody td {
    padding: 16px 24px;
    font-size: 14px;
    color: var(--white2);
    border-bottom: 1px solid rgba(201,168,76,0.08);
    white-space: nowrap;
  }
  tbody tr:last-child td { border-bottom: none; }
  tbody tr:hover {
    background: rgba(201,168,76,0.04);
  }
  td.name-cell {
    color: var(--white);
    font-weight: 600;
  }
 
  .resume-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--gold);
    color: var(--black);
    padding: 8px 16px;
    border-radius: 6px;
    font-family: 'Sora', sans-serif;
    font-weight: 700;
    font-size: 12px;
    text-decoration: none;
    transition: all 0.25s;
  }
  .resume-btn:hover {
    background: var(--gold-light);
    transform: translateY(-1px);
  }
  .resume-btn svg {
    width: 14px;
    height: 14px;
  }
  .no-resume {
    color: var(--silver);
    font-size: 13px;
    font-style: italic;
  }
 
  .empty-row td {
    text-align: center;
    padding: 36px;
    color: var(--silver);
    font-style: italic;
  }
 
  .count-text {
    margin-top: 16px;
    font-size: 13px;
    color: var(--silver);
  }
</style>
</head>
<body>
 
<header>
  <h1>BEEN Recruitment Employer Dashboard</h1>
  <a href="logout.php" class="logout-btn">Logout</a>
</header>
 
<main>
 
  <div class="welcome-card">
    <h2>Welcome, <?php echo htmlspecialchars($employer['fullname'] ?? ''); ?></h2>
    <p><?php echo htmlspecialchars($employer['email'] ?? ''); ?> &nbsp;&middot;&nbsp;
      <?php if (($employer['status'] ?? '') === 'Approved'): ?>
        <span class="status-pill status-approved">Approved</span>
      <?php elseif (($employer['status'] ?? '') === 'Rejected'): ?>
        <span class="status-pill status-rejected">Rejected</span>
      <?php else: ?>
        <span class="status-pill status-pending">Pending For Approval</span>
      <?php endif; ?>
    </p>
  </div>
 
  <?php if (($employer['status'] ?? '') === 'Approved'): ?>
 
    <div class="top-bar">
      <h2 class="section-title">Registered Job Seekers</h2>
    </div>
 
    <div class="table-card">
      <div class="table-scroll">
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Role Applied</th>
              <th>Experience</th>
              <th>Resume</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($job_seekers)): ?>
            <tr class="empty-row">
              <td colspan="6">No job seekers registered yet.</td>
            </tr>
            <?php else: foreach ($job_seekers as $seeker): ?>
            <tr>
              <td class="name-cell"><?php echo htmlspecialchars($seeker['full_name'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($seeker['email'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($seeker['phone'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($seeker['role_applied'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($seeker['experience'] ?? ''); ?></td>
              <td>
                <?php if (!empty($seeker['resume'])): ?>
                <a href="download_resume.php?file=<?php echo urlencode($seeker['resume']); ?>" class="resume-btn">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                  </svg>
                  Download
                </a>
                <?php else: ?>
                <span class="no-resume">No resume</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
 
    <p class="count-text">Total: <?php echo count($job_seekers); ?> job seeker<?php echo count($job_seekers) !== 1 ? 's' : ''; ?></p>
 
  <?php else: ?>
 
    <div class="pending-notice">
      <?php if (($employer['status'] ?? '') === 'Rejected'): ?>
        <p>Your account has been <strong>rejected</strong> by the admin. Please contact support for more information.</p>
      <?php else: ?>
        <p>Your account is <strong>pending for approval</strong>. You will be able to view registered job seekers once the admin approves your account.</p>
        <!-- Payment disabled -->
      <?php endif; ?>
    </div>
 
  <?php endif; ?>
 
</main>
 
<?php if (false): // Payment disabled ?>
<!-- ===================== PAY NOW MODAL ===================== -->
<div id="payModalOverlay" class="pay-modal-overlay">
  <div class="pay-modal-box">
 
    <button id="closePayBtn" class="pay-modal-close">&times;</button>
 
    <h3 class="pay-modal-title">Approval Payment</h3>
    <p class="pay-modal-sub">Complete the payment below to submit your account for admin approval.</p>
 
    <form id="payForm">
      <input type="hidden" id="payName" value="<?php echo htmlspecialchars($employer['fullname'] ?? ''); ?>">
      <input type="hidden" id="payEmail" value="<?php echo htmlspecialchars($employer['email'] ?? ''); ?>">
      <input type="hidden" id="payAmount" value="499">
 
      <div class="pay-amount-display">&#8377;499 <span>Approval Fee</span></div>
 
      <button type="submit" id="payProceedBtn" class="pay-proceed-btn">
        Proceed to Pay
      </button>
    </form>
 
    <p id="payErrorMsg" class="pay-error-msg"></p>
 
  </div>
</div>
 
<!-- Razorpay Checkout script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
  const openPayBtn = document.getElementById('openPayBtn');
  const closePayBtn = document.getElementById('closePayBtn');
  const payModalOverlay = document.getElementById('payModalOverlay');
  const payForm = document.getElementById('payForm');
  const payErrorMsg = document.getElementById('payErrorMsg');
  const payProceedBtn = document.getElementById('payProceedBtn');
 
  openPayBtn.addEventListener('click', function() {
    payModalOverlay.style.display = 'flex';
  });
 
  closePayBtn.addEventListener('click', function() {
    payModalOverlay.style.display = 'none';
  });
 
  payModalOverlay.addEventListener('click', function(e) {
    if (e.target === payModalOverlay) {
      payModalOverlay.style.display = 'none';
    }
  });
 
  payForm.addEventListener('submit', function(e) {
    e.preventDefault();
    payErrorMsg.style.display = 'none';
    payProceedBtn.disabled = true;
    payProceedBtn.textContent = 'Processing...';
 
    const name = document.getElementById('payName').value;
    const email = document.getElementById('payEmail').value;
    const amount = document.getElementById('payAmount').value;
 
    fetch('api/create_payment_order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name: name, email: email, amount: amount })
    })
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        throw new Error(data.error || 'Could not create payment order');
      }
 
      const options = {
        key: data.razorpay_key,
        amount: data.amount,
        currency: "INR",
        name: "BEEN Recruitment",
        description: "Employer Approval Fee",
        order_id: data.order_id,
        prefill: {
          name: name,
          email: email
        },
        theme: { color: "#C9A84C" },
        handler: function (response) {
          fetch('api/verify_payment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              razorpay_payment_id: response.razorpay_payment_id,
              razorpay_order_id: response.razorpay_order_id,
              razorpay_signature: response.razorpay_signature,
              name: name,
              email: email,
              amount: amount
            })
          })
          .then(res => res.json())
          .then(verifyData => {
            if (verifyData.success) {
              payModalOverlay.style.display = 'none';
              alert('Payment successful! Your account will be reviewed by the admin shortly.');
              payForm.reset();
            } else {
              payErrorMsg.textContent = 'Payment verification failed. Please contact support.';
              payErrorMsg.style.display = 'block';
            }
          })
          .finally(() => {
            payProceedBtn.disabled = false;
            payProceedBtn.textContent = 'Proceed to Pay';
          });
        },
        modal: {
          ondismiss: function() {
            payProceedBtn.disabled = false;
            payProceedBtn.textContent = 'Proceed to Pay';
          }
        }
      };
 
      const rzp = new Razorpay(options);
      rzp.open();
    })
    .catch(err => {
      payErrorMsg.textContent = err.message;
      payErrorMsg.style.display = 'block';
      payProceedBtn.disabled = false;
      payProceedBtn.textContent = 'Proceed to Pay';
    });
  });
</script>
<?php endif; ?>
 
</body>
</html>