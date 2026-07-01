<?php
require_once __DIR__ . '/db.php';
 
if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: admin_login.php');
    exit;
}
 
$employers   = query_all("SELECT * FROM employers ORDER BY id DESC");
$job_seekers = query_all("SELECT * FROM job_seekers ORDER BY id DESC");
$clients     = query_all("SELECT * FROM clients ORDER BY id DESC");
 
function status_badge(string $status): string {
    if ($status === 'Approved') return '<span class="status-badge status-approved">Approved</span>';
    if ($status === 'Rejected') return '<span class="status-badge status-rejected">Rejected</span>';
    return '<span class="status-badge status-pending">Pending</span>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | BEEN Recruitment</title>
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
  .logout-btn:hover { background: #b82a2a; transform: translateY(-1px); }
 
  /* TABS */
  .tabs-bar {
    display: flex;
    gap: 4px;
    padding: 28px 5% 0;
    border-bottom: 1px solid var(--border);
    background: var(--black);
  }
  .tab-btn {
    padding: 12px 24px;
    font-family: 'Sora', sans-serif;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.5px;
    border: none;
    border-radius: 8px 8px 0 0;
    cursor: pointer;
    background: transparent;
    color: var(--silver);
    border: 1px solid transparent;
    border-bottom: none;
    transition: all 0.2s;
    position: relative;
    bottom: -1px;
  }
  .tab-btn:hover { color: var(--gold); }
  .tab-btn.active {
    background: var(--dark2);
    color: var(--gold);
    border-color: var(--border);
    border-bottom-color: var(--dark2);
  }
 
  main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 5% 80px;
  }
 
  .section-title {
    font-family: 'Sora', sans-serif;
    font-size: 22px;
    font-weight: 700;
    color: var(--white);
    margin-bottom: 20px;
  }
 
  .tab-panel { display: none; }
  .tab-panel.active { display: block; }
 
  /* TABLE */
  .table-card {
    background: var(--dark2);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 30px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.3);
  }
  .table-scroll { overflow-x: auto; }
  table { width: 100%; border-collapse: collapse; }
  thead { background: var(--dark3); }
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
  tbody tr:hover { background: rgba(201,168,76,0.04); }
  td.name-cell { color: var(--white); font-weight: 600; }
 
  /* STATUS */
  .status-badge {
    display: inline-block;
    padding: 5px 13px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
  }
  .status-pending  { background: rgba(201,168,76,0.15); color: var(--gold); border: 1px solid rgba(201,168,76,0.4); }
  .status-approved { background: rgba(46,158,91,0.15);  color: #4ade80;     border: 1px solid rgba(46,158,91,0.4); }
  .status-rejected { background: rgba(214,96,49,0.15);  color: #f87171;     border: 1px solid rgba(214,96,49,0.4); }
 
  /* ACTION BUTTONS */
  .action-btn {
    display: inline-block;
    padding: 7px 16px;
    border-radius: 6px;
    font-family: 'Sora', sans-serif;
    font-size: 12px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    text-decoration: none;
    margin-right: 6px;
    transition: all 0.25s;
  }
  .approve-btn { background: var(--green); color: var(--white); }
  .approve-btn:hover { background: #259051; transform: translateY(-1px); }
  .reject-btn  { background: var(--red);   color: var(--white); }
  .reject-btn:hover  { background: #b82a2a; transform: translateY(-1px); }
 
  /* VIEW DETAIL BUTTON */
  .detail-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: transparent;
    border: 1px solid var(--gold);
    color: var(--gold);
    padding: 7px 14px;
    border-radius: 6px;
    font-family: 'Sora', sans-serif;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.25s;
  }
  .detail-btn:hover { background: var(--gold); color: var(--black); }
 
  /* RESUME */
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
  .resume-btn:hover { background: var(--gold-light); transform: translateY(-1px); }
  .no-resume { color: var(--silver); font-size: 13px; font-style: italic; }
 
  .empty-row td {
    text-align: center;
    padding: 36px;
    color: var(--silver);
    font-style: italic;
  }
  .count-text { margin-top: 14px; font-size: 13px; color: var(--silver); }
 
  /* MODAL */
  .modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.75);
    z-index: 100;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
  .modal-overlay.open { display: flex; }
  .modal {
    background: var(--dark2);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 32px;
    max-width: 540px;
    width: 100%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.6);
    position: relative;
  }
  .modal-close {
    position: absolute;
    top: 16px; right: 20px;
    background: none;
    border: none;
    color: var(--silver);
    font-size: 22px;
    cursor: pointer;
    line-height: 1;
  }
  .modal-close:hover { color: var(--white); }
  .modal h3 {
    font-family: 'Sora', sans-serif;
    font-size: 18px;
    font-weight: 700;
    color: var(--gold);
    margin-bottom: 20px;
    padding-right: 24px;
  }
  .detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
  }
  .detail-item { display: flex; flex-direction: column; gap: 3px; }
  .detail-item .label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--silver);
  }
  .detail-item .value {
    font-size: 14px;
    color: var(--white);
    font-weight: 500;
    word-break: break-word;
  }
  .detail-item.full-width { grid-column: 1 / -1; }
  .detail-divider {
    grid-column: 1 / -1;
    border: none;
    border-top: 1px solid var(--border);
    margin: 6px 0;
  }
  .modal-status-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 22px;
    padding-top: 18px;
    border-top: 1px solid var(--border);
  }
</style>
</head>
<body>
 
<header>
  <h1>BEEN Recruitment Admin Dashboard</h1>
  <div style="display:flex; align-items:center; gap:14px;">
    <a href="admin_settings.php" class="logout-btn">Settings</a>
    <a href="logout.php" class="logout-btn">Logout</a>
  </div>
</header>
 
<!-- TABS -->
<div class="tabs-bar">
  <button class="tab-btn active" onclick="switchTab('clients')">Client Details</button>
  <button class="tab-btn" onclick="switchTab('employers')">Employer Approvals</button>
  <button class="tab-btn" onclick="switchTab('employer-details')">Employer Details</button>
  <button class="tab-btn" onclick="switchTab('jobseekers')">Job Seekers</button>
</div>
 
<main>
 
  <!-- ===== TAB 0: CLIENT DETAILS ===== -->
  <div id="tab-clients" class="tab-panel active">
    <h2 class="section-title">Consultation Requests (Client Details)</h2>
    <div class="table-card">
      <div class="table-scroll">
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Phone</th>
              <th>Email</th>
              <th>Project Type</th>
              <th>Budget</th>
              <th>Location</th>
              <th>Message</th>
              <th>Submitted</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($clients)): ?>
            <tr class="empty-row"><td colspan="8">No consultation requests yet.</td></tr>
            <?php else: foreach ($clients as $client): ?>
            <tr>
              <td class="name-cell"><?php echo htmlspecialchars($client['full_name'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($client['phone'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($client['email'] ?: '—'); ?></td>
              <td><?php echo htmlspecialchars($client['project_type'] ?: '—'); ?></td>
              <td><?php echo htmlspecialchars($client['budget_range'] ?: '—'); ?></td>
              <td><?php echo htmlspecialchars($client['project_location'] ?: '—'); ?></td>
              <td><?php echo htmlspecialchars($client['message'] ?: '—'); ?></td>
              <td><?php echo htmlspecialchars($client['created_at'] ?? ''); ?></td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <p class="count-text">Total: <?php echo count($clients); ?> request<?php echo count($clients) !== 1 ? 's' : ''; ?></p>
  </div>
 
  <!-- ===== TAB 1: EMPLOYER APPROVALS ===== -->
  <div id="tab-employers" class="tab-panel">
    <h2 class="section-title">Manage Employer Approvals</h2>
    <div class="table-card">
      <div class="table-scroll">
        <table>
          <thead>
            <tr>
              <th>Employer Name</th>
              <th>Email Address</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($employers)): ?>
            <tr class="empty-row"><td colspan="4">No employer registrations yet.</td></tr>
            <?php else: foreach ($employers as $employer): ?>
            <tr>
              <td class="name-cell"><?php echo htmlspecialchars($employer['fullname'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($employer['email'] ?? ''); ?></td>
              <td><?php echo status_badge($employer['status'] ?? 'Pending'); ?></td>
              <td>
                <?php if (($employer['status'] ?? 'Pending') === 'Pending'): ?>
                <a href="admin_approve.php?id=<?php echo (int)$employer['id']; ?>" class="action-btn approve-btn">Approve</a>
                <a href="admin_reject.php?id=<?php echo (int)$employer['id']; ?>" class="action-btn reject-btn">Reject</a>
                <?php else: ?>
                <span style="color: var(--silver); font-size: 12px;">No action needed</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <p class="count-text">Total: <?php echo count($employers); ?> employer<?php echo count($employers) !== 1 ? 's' : ''; ?></p>
  </div>
 
  <!-- ===== TAB 2: EMPLOYER DETAILS ===== -->
  <div id="tab-employer-details" class="tab-panel">
    <h2 class="section-title">Employer Details</h2>
    <div class="table-card">
      <div class="table-scroll">
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Company</th>
              <th>Type</th>
              <th>Designation</th>
              <th>Location</th>
              <th>Status</th>
              <th>Details</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($employers)): ?>
            <tr class="empty-row"><td colspan="7">No employer registrations yet.</td></tr>
            <?php else: foreach ($employers as $employer): ?>
            <tr>
              <td class="name-cell"><?php echo htmlspecialchars($employer['fullname'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($employer['company_name'] ?: '—'); ?></td>
              <td><?php echo htmlspecialchars($employer['company_type'] ?: '—'); ?></td>
              <td><?php echo htmlspecialchars($employer['designation'] ?: '—'); ?></td>
              <td><?php echo htmlspecialchars($employer['location'] ?: '—'); ?></td>
              <td><?php echo status_badge($employer['status'] ?? 'Pending'); ?></td>
              <td>
                <button class="detail-btn" onclick='openModal(<?php echo htmlspecialchars(json_encode($employer), ENT_QUOTES); ?>)'>
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/></svg>
                  View
                </button>
              </td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <p class="count-text">Total: <?php echo count($employers); ?> employer<?php echo count($employers) !== 1 ? 's' : ''; ?></p>
  </div>
 
  <!-- ===== TAB 3: JOB SEEKERS ===== -->
  <div id="tab-jobseekers" class="tab-panel">
    <h2 class="section-title">Registered Job Seekers</h2>
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
            <tr class="empty-row"><td colspan="6">No job seekers registered yet.</td></tr>
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
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
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
  </div>
 
</main>
 
<!-- EMPLOYER DETAIL MODAL -->
<div class="modal-overlay" id="detailModal" onclick="if(event.target===this)closeModal()">
  <div class="modal">
    <button class="modal-close" onclick="closeModal()">&#x2715;</button>
    <h3 id="modal-title">Employer Details</h3>
    <div class="detail-grid">
      <div class="detail-item">
        <span class="label">Full Name</span>
        <span class="value" id="m-fullname">—</span>
      </div>
      <div class="detail-item">
        <span class="label">Email</span>
        <span class="value" id="m-email">—</span>
      </div>
      <div class="detail-item">
        <span class="label">Phone</span>
        <span class="value" id="m-phone">—</span>
      </div>
      <div class="detail-item">
        <span class="label">Designation</span>
        <span class="value" id="m-designation">—</span>
      </div>
      <hr class="detail-divider">
      <div class="detail-item">
        <span class="label">Company Name</span>
        <span class="value" id="m-company_name">—</span>
      </div>
      <div class="detail-item">
        <span class="label">Company Type</span>
        <span class="value" id="m-company_type">—</span>
      </div>
      <div class="detail-item">
        <span class="label">Location</span>
        <span class="value" id="m-location">—</span>
      </div>
      <div class="detail-item">
        <span class="label">Website</span>
        <span class="value" id="m-website">—</span>
      </div>
      <div class="detail-item">
        <span class="label">Openings</span>
        <span class="value" id="m-openings">—</span>
      </div>
      <div class="detail-item" style="grid-column:1/-1;">
        <span class="label">Requirements</span>
        <span class="value" id="m-requirements">—</span>
      </div>
    </div>
    <div class="modal-status-row">
      <div>
        <span class="label" style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--silver);">Account Status</span><br>
        <span id="m-status-badge" style="margin-top:4px;display:inline-block;"></span>
      </div>
      <div id="m-actions"></div>
    </div>
  </div>
</div>
 
<script>
  function switchTab(name) {
    document.querySelectorAll('.tab-btn').forEach((b, i) => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    const panels = { 'clients': 0, 'employers': 1, 'employer-details': 2, 'jobseekers': 3 };
    document.querySelectorAll('.tab-btn')[panels[name]].classList.add('active');
    document.getElementById('tab-' + name).classList.add('active');
    window.scrollTo(0, 0);
  }
 
  function openModal(emp) {
    const fields = ['fullname','email','phone','designation','company_name','company_type','location','website','openings','requirements'];
    fields.forEach(f => {
      const el = document.getElementById('m-' + f);
      if (el) el.textContent = emp[f] || '—';
    });
    document.getElementById('modal-title').textContent = emp.fullname + ' — Details';
 
    const statusMap = {
      'Approved': '<span class="status-badge status-approved">Approved</span>',
      'Rejected': '<span class="status-badge status-rejected">Rejected</span>',
      'Pending':  '<span class="status-badge status-pending">Pending</span>'
    };
    document.getElementById('m-status-badge').innerHTML = statusMap[emp.status] || statusMap['Pending'];
 
    let actionsHtml = '';
    if (emp.status === 'Pending') {
      actionsHtml = `<a href="admin_approve.php?id=${emp.id}" class="action-btn approve-btn">Approve</a>
                     <a href="admin_reject.php?id=${emp.id}" class="action-btn reject-btn">Reject</a>`;
    }
    document.getElementById('m-actions').innerHTML = actionsHtml;
 
    document.getElementById('detailModal').classList.add('open');
  }
 
  function closeModal() {
    document.getElementById('detailModal').classList.remove('open');
  }
 
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>
 
</body>
</html>