<?php
session_start();
$host = 'db.cytgowntzmecebcbgnxw.supabase.co';
$password_db = 'YOUR_DATABASE_PASSWORD'; 

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO("pgsql:host=$host;port=5432;dbname=postgres;user=postgres;password=$password_db");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = :email LIMIT 1");[cite: 1]
        $stmt->execute(['email' => $_POST['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($_POST['password'], $user['password'])) {[cite: 1]
            if ($user['role'] === $_POST['role_type']) {[cite: 1]
                $_SESSION['student_id'] = $user['student_id'];
                $_SESSION['full_name']  = $user['full_name'];
                $_SESSION['dept']       = $user['department'];
                
                header($user['role'] === 'faculty' ? "Location: faculty.php" : "Location: profile.php");[cite: 1]
                exit;
            } else { $error = "Role mismatch for this account."; }[cite: 1]
        } else { $error = "Invalid email or password."; }[cite: 1]
    } catch (PDOException $e) { $error = "Connection error."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - DOIT Track</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        :root { --brand-red: #8B0000; --brand-gold: #D4AF37; --bg-cream: #FDFBF7; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-cream); color: #1a1a1a; }
        .brand-bg-red { background-color: var(--brand-red); }
        .glass-card { background: white; border: 1px solid rgba(212, 175, 55, 0.1); box-shadow: 0 20px 50px rgba(139, 0, 0, 0.05); }
        .form-input { width: 100%; padding: 0.875rem 1rem 0.875rem 3rem; background-color: #F9F7F2; border: 1px solid #E5E7EB; border-radius: 0.75rem; }
        .form-input:focus { outline: none; border-color: var(--brand-red); background-color: white; box-shadow: 0 0 0 4px rgba(139, 0, 0, 0.05); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-[480px] glass-card rounded-[2.5rem] p-10 md:p-14 text-center mt-12">
        <h1 class="text-4xl font-black text-slate-900 mb-3 tracking-tight">Sign in to DOIT Track</h1>
        
        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-3 text-left">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <p class="text-xs font-bold text-red-600"><?= $error ?></p>
            </div>[cite: 1]
        <?php endif; ?>

        <form method="POST" action="login.php" class="space-y-6 text-left">
            <input type="hidden" name="role_type" id="role_type" value="student">
            
            <div class="flex p-1.5 bg-[#F9F7F2] rounded-2xl mb-10 border border-slate-100 relative">
                <button type="button" id="btn-s" onclick="setRole('student')" class="flex-1 py-3.5 rounded-xl text-sm font-bold brand-bg-red text-white shadow-xl">Student</button>
                <button type="button" id="btn-f" onclick="setRole('faculty')" class="flex-1 py-3.5 rounded-xl text-sm font-bold text-slate-400">Faculty</button>
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Official Email</label>
                <div class="relative group">
                    <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="email" name="email" required placeholder="name@doit.edu" class="form-input">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Password</label>
                <div class="relative group">
                    <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="password" name="password" required placeholder="••••••••" class="form-input">
                </div>
            </div>

            <button type="submit" class="w-full brand-bg-red text-white py-4.5 rounded-2xl font-extrabold text-lg mt-4">
                Sign in as <span id="submit-label">Student</span>
            </button>
        </form>
    </div>
    <script>
        function setRole(role) {
            document.getElementById('role_type').value = role;
            document.getElementById('btn-s').className = role === 'student' ? "flex-1 py-3.5 rounded-xl text-sm font-bold brand-bg-red text-white shadow-xl" : "flex-1 py-3.5 rounded-xl text-sm font-bold text-slate-400";
            document.getElementById('btn-f').className = role === 'faculty' ? "flex-1 py-3.5 rounded-xl text-sm font-bold brand-bg-red text-white shadow-xl" : "flex-1 py-3.5 rounded-xl text-sm font-bold text-slate-400";
            document.getElementById('submit-label').innerText = role.charAt(0).toUpperCase() + role.slice(1);
        }
    </script>
</body>
</html>
