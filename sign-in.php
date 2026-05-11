<?php
session_start();
$host = 'db.cytgowntzmecebcbgnxw.supabase.co'; 
$password_db = 'Dave2k521123'; // Replace with your real Supabase password

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO("pgsql:host=$host;port=5432;dbname=postgres;user=postgres;password=$password_db");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $hashedPass = password_hash($_POST['password'], PASSWORD_DEFAULT);[cite: 1]

        $sql = "INSERT INTO students (student_id, full_name, department, email, password, role) 
                VALUES (:id, :name, :dept, :email, :pass, :role)
                ON CONFLICT (student_id) DO NOTHING";[cite: 1]
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id'    => $_POST['id_number'],
            'name'  => $_POST['full_name'],
            'dept'  => $_POST['department'],
            'email' => $_POST['email'],
            'pass'  => $hashedPass,
            'role'  => $_POST['role_type']
        ]);

        $_SESSION['student_id'] = $_POST['id_number'];
        $_SESSION['full_name']  = $_POST['full_name'];
        $_SESSION['dept']       = $_POST['department'];
        
        header("Location: profile.php");[cite: 1]
        exit;
    } catch (PDOException $e) { $error = "Registration failed: " . $e->getMessage(); }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - DOIT Track</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        :root { --brand-red: #8B0000; --brand-gold: #D4AF37; --bg-cream: #FDFBF7; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-cream); color: #1a1a1a; display: flex; flex-direction: column; min-height: 100vh; }
        .brand-bg-red { background-color: var(--brand-red); }
        .glass-card { background: white; border: 1px solid rgba(212, 175, 55, 0.1); box-shadow: 0 20px 50px rgba(139, 0, 0, 0.05); }
        .form-input { width: 100%; padding: 0.75rem 1rem 0.75rem 3rem; background-color: #F9F7F2; border: 1px solid #E5E7EB; border-radius: 0.75rem; font-size: 0.9rem; transition: all 0.2s; appearance: none; }
        .form-input:focus { outline: none; border-color: var(--brand-red); background-color: white; box-shadow: 0 0 0 4px rgba(139, 0, 0, 0.05); }
        .role-btn { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="items-center justify-center p-6">
    <div id="signup-view" class="w-full max-w-[480px] glass-card rounded-[2.5rem] p-8 md:p-12 text-center relative mt-16 mb-16">
        <h1 class="text-3xl font-black text-slate-900 mb-2 tracking-tight">Create your account</h1>
        
        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-xl mb-4 text-xs font-bold"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="signup.php" class="space-y-4 text-left">
            <input type="hidden" name="role_type" id="role_type" value="student">
            
            <div class="flex p-1.5 bg-[#F9F7F2] rounded-2xl mb-8 border border-slate-100 relative">
                <button type="button" id="student-btn" onclick="setRole('student')" class="role-btn flex-1 py-3 rounded-xl text-sm font-bold flex items-center justify-center gap-2 brand-bg-red text-white shadow-xl">
                    <i class="fas fa-graduation-cap"></i> Student
                </button>
                <button type="button" id="faculty-btn" onclick="setRole('faculty')" class="role-btn flex-1 py-3 rounded-xl text-sm font-bold flex items-center justify-center gap-2 text-slate-400">
                    <i class="fas fa-user-shield"></i> Faculty
                </button>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                <div class="relative group">
                    <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="text" name="full_name" placeholder="John Doe" required class="form-input">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1"><span id="id-label">Student</span> ID</label>
                <div class="relative group">
                    <i class="fas fa-id-card absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="text" name="id_number" placeholder="e.g. 2024-001" required class="form-input">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Department</label>
                <div class="relative group">
                    <i class="fas fa-building absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 z-10"></i>
                    <select name="department" required class="form-input cursor-pointer">
                        <option value="BSIT">BSIT</option>
                        <option value="BSCS">BSCS</option>
                        <option value="BEED">BEED</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Official Email</label>
                <div class="relative group">
                    <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="email" name="email" required placeholder="name@doit.edu" class="form-input">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Password</label>
                <div class="relative group">
                    <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="password" name="password" required placeholder="••••••••" class="form-input">
                </div>
            </div>

            <button type="submit" class="w-full brand-bg-red text-white py-4 rounded-2xl font-extrabold text-lg mt-6">
                Create <span id="submit-label">Student</span> Account
            </button>
        </form>
    </div>
    <script>
        function setRole(role) {
            document.getElementById('role_type').value = role;
            const sBtn = document.getElementById('student-btn');
            const fBtn = document.getElementById('faculty-btn');
            const submitLabel = document.getElementById('submit-label');
            const idLabel = document.getElementById('id-label');
            if(role === 'student') {
                sBtn.className = "role-btn flex-1 py-3 rounded-xl text-sm font-bold flex items-center justify-center gap-2 brand-bg-red text-white shadow-xl";
                fBtn.className = "role-btn flex-1 py-3 rounded-xl text-sm font-bold flex items-center justify-center gap-2 text-slate-400";
                submitLabel.innerText = "Student"; idLabel.innerText = "Student";
            } else {
                fBtn.className = "role-btn flex-1 py-3 rounded-xl text-sm font-bold flex items-center justify-center gap-2 brand-bg-red text-white shadow-xl";
                sBtn.className = "role-btn flex-1 py-3 rounded-xl text-sm font-bold flex items-center justify-center gap-2 text-slate-400";
                submitLabel.innerText = "Faculty"; idLabel.innerText = "Faculty";
            }
        }
    </script>
</body>
</html>
