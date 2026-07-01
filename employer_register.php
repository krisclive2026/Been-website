<?php
require_once __DIR__ . '/db.php';
 
$error = '';
$show_popup = false;
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $fullname  = trim($firstname . ' ' . $lastname);
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $company_type = trim($_POST['company_type'] ?? '');
    $designation  = trim($_POST['designation'] ?? '');
    $location     = trim($_POST['location'] ?? '');
    $website      = trim($_POST['website'] ?? '');
    $password     = trim($_POST['password'] ?? '');
 
    $existing = query_one("SELECT id FROM employers WHERE email = ?", [$email]);
    if ($existing) {
        $error = "This email is already registered. Please log in.";
    } else {
        $ok = execute(
            "INSERT INTO employers
                (fullname, email, password, phone, company_name, company_type,
                 designation, location, website, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')",
            [$fullname, $email, $password, $phone, $company_name, $company_type,
             $designation, $location, $website]
        );
        if ($ok) {
            $show_popup = true;
        } else {
            $error = "Could not save your registration. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Registration - BEEN</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen text-white relative py-10">
 
    <div class="bg-slate-800 p-8 rounded-xl shadow-2xl w-full max-w-2xl border border-slate-700">
        <h2 class="text-2xl font-bold text-center mb-2 text-amber-400">EMPLOYER REGISTRATION</h2>
        <p class="text-center text-slate-400 text-sm mb-6">Fill in your details to register as an employer</p>
 
        <?php if ($error): ?>
                <div class="bg-red-500/20 border border-red-500 text-red-200 p-3 rounded mb-4 text-sm text-center">
                    <?php echo htmlspecialchars($error); ?>
                </div>
        <?php endif; ?>
 
        <form action="employer_register.php" method="POST" class="space-y-5" autocomplete="off">
 
            <!-- Personal Info -->
            <div>
                <p class="text-amber-400 text-xs font-bold uppercase tracking-widest mb-3">Personal Information</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">First Name <span class="text-red-400">*</span></label>
                        <input type="text" name="firstname" required class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 focus:outline-none focus:border-amber-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Last Name <span class="text-red-400">*</span></label>
                        <input type="text" name="lastname" required class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 focus:outline-none focus:border-amber-400">
                    </div>
                </div>
            </div>
 
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Email Address <span class="text-red-400">*</span></label>
                    <input type="email" name="email" required class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 focus:outline-none focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Phone Number <span class="text-red-400">*</span></label>
                    <input type="tel" name="phone" required class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 focus:outline-none focus:border-amber-400" placeholder="+91 XXXXX XXXXX">
                </div>
            </div>
 
            <!-- Company Info -->
            <div class="border-t border-slate-600 pt-5">
                <p class="text-amber-400 text-xs font-bold uppercase tracking-widest mb-3">Company Information</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Company Name <span class="text-red-400">*</span></label>
                        <input type="text" name="company_name" required class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 focus:outline-none focus:border-amber-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Company Type <span class="text-red-400">*</span></label>
                        <select name="company_type" required class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 focus:outline-none focus:border-amber-400">
                            <option value="" disabled selected>Select type</option>
                            <option value="Private Limited">Private Limited</option>
                            <option value="Public Limited">Public Limited</option>
                            <option value="Partnership">Partnership</option>
                            <option value="Sole Proprietorship">Sole Proprietorship</option>
                            <option value="LLP">LLP</option>
                            <option value="NGO / Trust">NGO / Trust</option>
                            <option value="Government">Government</option>
                            <option value="Startup">Startup</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
            </div>
 
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Your Designation <span class="text-red-400">*</span></label>
                    <input type="text" name="designation" required placeholder="e.g. HR Manager, CEO" class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 focus:outline-none focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Company Location <span class="text-red-400">*</span></label>
                    <input type="text" name="location" required placeholder="e.g. Chennai, Tamil Nadu" class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 focus:outline-none focus:border-amber-400">
                </div>
            </div>
 
            <div>
                <label class="block text-sm font-medium mb-1">Company Website <span class="text-slate-400 font-normal">(optional)</span></label>
                <input type="text" name="website" placeholder="yourcompany.com" class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 focus:outline-none focus:border-amber-400">
            </div>
 
            <!-- Account -->
            <div class="border-t border-slate-600 pt-5">
                <p class="text-amber-400 text-xs font-bold uppercase tracking-widest mb-3">Account Password</p>
                <div>
                    <label class="block text-sm font-medium mb-1">Password <span class="text-red-400">*</span></label>
                    <input type="password" name="password" required class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 focus:outline-none focus:border-amber-400">
                </div>
            </div>
 
            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold py-2.5 rounded transition mt-2">Register Account</button>
        </form>
 
        <div class="text-center mt-4 text-sm">
            <a href="index.php" class="text-slate-400 hover:text-white">← Back to Home</a>
        </div>
    </div>
 
    <?php if ($show_popup): ?>
    <div class="fixed inset-0 bg-black/80 flex items-center justify-center z-50">
        <div class="bg-slate-800 border-2 border-amber-400 p-8 rounded-xl text-center max-w-sm w-11/12 shadow-2xl">
            <div class="w-16 h-16 bg-green-500/20 border border-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-green-400 text-3xl font-bold">✓</span>
            </div>
            <h3 class="text-xl font-bold text-amber-400 mb-2">Registration Successful!</h3>
            <p class="text-slate-300 text-sm mb-6">Your profile has been submitted and sent to the admin for approval verification.</p>
            <a href="index.php" class="inline-block w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold py-2.5 rounded transition shadow-lg shadow-amber-500/20">
                Back to Home
            </a>
        </div>
    </div>
    <?php endif; ?>
 
</body>
</html>