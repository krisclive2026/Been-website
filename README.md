# BEEN Architectural & Engineering — Employee Recruitment Website

A PHP + MySQL website for BEEN Architectural & Engineering, covering the public site
(home page, portfolio, contact), employer/job-seeker accounts, resume uploads, an
admin dashboard, and Razorpay payment integration.

---

## 1. Project Structure

```
index.php                  Public homepage (about, services, portfolio, contact)
employer_register.php      Employer sign-up form
employer_login.php         Employer login
employer_dashboard.php     Employer dashboard (post jobs, view applicants, etc.)
admin_login.php            Admin login
admin_dashboard.php        Admin dashboard (manage employers/job seekers/payments)
admin_approve.php          Approve a pending employer/job seeker
admin_reject.php           Reject a pending employer/job seeker
admin_notifications.php    Admin notification feed
admin_settings.php         Change admin username/password (from the UI, hashes it)
reset_admin.php            One-time script to reset admin credentials directly
                            (edit, run once, then DELETE from server)
download_resume.php        Secure resume/PDF download handler
logout.php                 Ends the session for admin/employer
config.php                 Database credentials, Razorpay keys, timezone, upload folder
db.php                     PDO database connection helper (query_one, query_all, execute)
schema.sql                 Database schema — run this once to create all tables
api/
  employer.php              Employer-related API endpoints
  jobseeker.php              Job-seeker-related API endpoints
  client_inquiry.php         Contact form submissions
  create_payment_order.php   Creates a Razorpay order
  verify_payment.php         Verifies a Razorpay payment after checkout
uploads/                   Uploaded resumes/PDFs (must be writable by PHP)
```

---

## 2. Requirements

- PHP 7.4+ (with `pdo_mysql` extension enabled)
- MySQL / MariaDB
- A Razorpay account (for payment features) — https://dashboard.razorpay.com
- Any standard shared hosting works (this project is currently deployed on **Bluehost**)

---

## 3. Deployment Steps (Bluehost or similar cPanel hosting)

### Step 1 — Create the database
1. In cPanel, go to **MySQL® Databases**.
2. Create a new database (e.g. `yourprefix_recruitment`).
3. Create a database user and password (cPanel auto-prefixes the username, e.g. `yourprefix_dbuser`).
4. Add that user to the database with **All Privileges**.

### Step 2 — Import the schema
1. Open **phpMyAdmin** from cPanel.
2. Select your new database.
3. Go to the **Import** tab, choose `schema.sql`, and run it.
4. This creates all tables (`admin`, `employers`, `job_seekers`, `payments`, `clients`)
   and seeds one default admin row:
   - Username: `admin`
   - Password: `admin123`
   **Change this immediately after first login** — see Step 5.

### Step 3 — Upload the files
1. Upload all project files to your domain's root folder (e.g. `public_html/`) via
   cPanel File Manager or FTP.
2. Make sure the `uploads/` folder has write permissions (usually `755` or `775`)
   so resume uploads work.

### Step 4 — Update `config.php`
Open `config.php` and set your **real** Bluehost database credentials
(the placeholder `root` / blank password will NOT work on Bluehost):

```php
define('DB_HOST', 'localhost');                 // usually 'localhost' on Bluehost
define('DB_NAME', 'yourprefix_recruitment');     // your actual DB name
define('DB_USER', 'yourprefix_dbuser');          // your actual DB user
define('DB_PASS', 'your-actual-db-password');
```

Also set your live Razorpay keys when ready to accept real payments
(replace the `rzp_test_...` test keys with your `rzp_live_...` keys):

```php
define('RAZORPAY_KEY_ID', 'rzp_live_...');
define('RAZORPAY_KEY_SECRET', '...');
```

> **Timezone note:** `config.php` already forces `Asia/Kolkata` (IST) for all PHP
> date/time calls, and `db.php` sets the MySQL session to `+05:30`. This keeps all
> timestamps in IST even though Bluehost's servers default to US time — no further
> action needed here.

### Step 5 — Log in and change the default admin password
1. Visit `https://yourdomain.com/admin_login.php`.
2. Log in with `admin` / `admin123`.
3. Go to **Settings** (`admin_settings.php`) and set a new username/password.
   This page properly hashes the password before saving — always prefer this
   over editing the database by hand.

---

## 4. Changing Admin Credentials Later

You have three options, in order of preference:

1. **Recommended:** Log in and use `admin_settings.php` — hashes the password automatically.
2. **Manual (phpMyAdmin):** Edit the `admin` table's row directly. Set the `password`
   field's function dropdown to **None** (not MD5/SHA1) before saving, or the login
   will reject it.
3. **Source-code script:** Edit the `$new_username` / `$new_password` values at the
   top of `reset_admin.php`, upload it, visit it once in your browser, confirm
   success, then **delete it from the server immediately** (leaving it live is a
   security risk).

---

## 5. Notes on the Public Homepage (`index.php`)

- All images on the homepage (about section, portfolio, logo, footer) are currently
  embedded directly as base64 inside `index.php` rather than linked as separate
  files. This keeps everything in one file but makes it large (~29MB) and slows
  down page load. For better performance, consider moving these images into an
  `assets/images/` folder and linking them with normal `<img src="...">` tags.

---

## 6. Security Notes

- `admin_login.php` requires the submitted username **and** password to match the
  same database row (fixed from an earlier version that had a login bypass bug).
- Always delete one-time scripts like `reset_admin.php` from the live server after use.
- Keep `RAZORPAY_KEY_SECRET` and database credentials out of any public repository.
