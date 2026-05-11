<?php
session_start();

// --- 1. SUPABASE CONNECTION ---
$host     = 'db.cytgowntzmecebcbgnxw.supabase.co'; // Your Project ID from screenshot
$port     = '5432';
$dbname   = 'postgres';
$user     = 'postgres';
$password = 'Dave2k52112321'; 

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password";
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection error: Check your password or PHP pgsql extension. " . $e->getMessage());
}

// --- 2. MOCK DATA FROM LOGIN ---
// In your real login page, you will set these $_SESSION variables.
if (!isset($_SESSION['student_id'])) {
    $_SESSION['student_id'] = '2024-0001'; 
    $_SESSION['full_name']  = 'Jordan Smith';
    $_SESSION['dept']       = 'Computer Science';
}

$s_id   = $_SESSION['student_id'];
$s_name = $_SESSION['full_name'];
$s_dept = $_SESSION['dept'];

// --- 3. AUTOMATIC UPSERT ---
// This adds them to your Supabase 'students' table automatically upon viewing
$sql = "INSERT INTO students (student_id, full_name, department) 
        VALUES (:id, :name, :dept) 
        ON CONFLICT (student_id) DO UPDATE 
        SET full_name = EXCLUDED.full_name, department = EXCLUDED.department
        RETURNING *";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $s_id, 'name' => $s_name, 'dept' => $s_dept]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// --- 4. FETCH HISTORY ---
$historyStmt = $pdo->prepare("SELECT * FROM attendance WHERE student_id = ? ORDER BY event_date DESC");
$historyStmt->execute([$s_id]);
$history = $historyStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOIT Track | Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #FDF6F0; color: #2D2D2D; }
        .bg-maroon { background-color: #8B0000; }
        .text-maroon { color: #8B0000; }
        .card { background: white; border-radius: 2rem; border: 1px solid #EADAC9; }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8">

    <main class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left: QR Section -->
        <section class="lg:col-span-4">
            <div class="card p-8 text-center shadow-sm">
                <h2 class="text-2xl font-bold mb-1"><?= htmlspecialchars($student['full_name']) ?></h2>
                <p class="text-gray-500 font-medium mb-8"><?= htmlspecialchars($student['student_id']) ?> • <?= htmlspecialchars($student['department']) ?></p>
                
                <div class="bg-white p-4 inline-block rounded-2xl border border-[#EADAC9] mb-8">
                    <div id="qrcode"></div>
                </div>

                <p class="text-xs text-gray-400 mb-8">Present this code at DOIT events to register your attendance.</p>
                
                <button class="w-full bg-maroon text-white font-bold py-4 rounded-xl shadow-lg shadow-maroon/20 hover:opacity-90 transition-all">
                    Download QR
                </button>
            </div>
        </section>

        <!-- Right: History Section -->
        <section class="lg:col-span-8">
            <div class="card p-8 min-h-[400px]">
                <h3 class="text-xl font-bold mb-6">Attendance History</h3>
                
                <?php if (empty($history)): ?>
                    <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-4"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <p>No events attended yet.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($history as $event): ?>
                            <div class="flex items-center justify-between p-4 border border-[#EADAC9] rounded-2xl">
                                <div>
                                    <h4 class="font-bold"><?= htmlspecialchars($event['event_name']) ?></h4>
                                    <p class="text-sm text-gray-500"><?= date('F j, Y', strtotime($event['event_date'])) ?></p>
                                </div>
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">VERIFIED</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: "<?= $student['student_id'] ?>",
            width: 180,
            height: 180,
            colorDark : "#8B0000",
            colorLight : "#ffffff"
        });
    </script>
</body>
</html>