<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'PARENT') {
    header("Location: ../index.php");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if(!$conn){
    die("DB Connection Failed");
}

$parent_user_id = $_SESSION['user_id'];

/* =========================
   CHILD INFO
========================= */
$child_q = mysqli_query($conn,"
    SELECT 
        s.id AS student_id,
        u.name AS student_name,
        s.class,
        s.batch
    FROM parents p
    JOIN students s ON p.student_id = s.id
    JOIN users u ON s.user_id = u.id
    WHERE p.user_id = '$parent_user_id'
");
$child = mysqli_fetch_assoc($child_q) ?: [];
$student_id = $child['student_id'] ?? 0;

/* =========================
   ATTENDANCE SUMMARY
========================= */
$att_q = mysqli_query($conn,"
    SELECT 
        COUNT(*) AS total,
        SUM(status='P') AS present,
        SUM(status='A') AS absent
    FROM attendance
    WHERE student_id = '$student_id'
");
$att = mysqli_fetch_assoc($att_q);

$total = $att['total'] ?? 0;
$present = $att['present'] ?? 0;
$absent = $att['absent'] ?? 0;
$percentage = $total > 0 ? round(($present/$total)*100,2) : 0;

/* =========================
   NOTIFICATIONS
========================= */
$noti_q = mysqli_query($conn,"
    SELECT message, created_at
    FROM notifications
    WHERE user_id = '$parent_user_id'
    ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Parent Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen">

<!-- NAVBAR -->
<div class="bg-white shadow px-8 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-indigo-600">👨‍👩‍👧 Parent Dashboard</h1>
    <a href="../auth/logout.php"
       class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
       Logout
    </a>
</div>

<div class="max-w-6xl mx-auto px-6 py-10">

<!-- CHILD INFO -->
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-800">
        <?= htmlspecialchars($child['student_name'] ?? $_SESSION['name']) ?>
    </h2>
    <p class="text-gray-600">
        Class <?= $child['class'] ?? '-' ?> | Batch <?= $child['batch'] ?? '-' ?>
    </p>
</div>

<!-- STATS -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">

<div class="bg-white p-6 rounded-2xl shadow">
<p class="text-gray-500">Total Days</p>
<h3 class="text-3xl font-bold"><?= $total ?></h3>
</div>

<div class="bg-white p-6 rounded-2xl shadow">
<p class="text-gray-500">Present</p>
<h3 class="text-3xl font-bold text-green-600"><?= $present ?></h3>
</div>

<div class="bg-white p-6 rounded-2xl shadow">
<p class="text-gray-500">Absent</p>
<h3 class="text-3xl font-bold text-red-600"><?= $absent ?></h3>
</div>

<div class="bg-white p-6 rounded-2xl shadow">
<p class="text-gray-500">Attendance %</p>
<h3 class="text-3xl font-bold text-indigo-600"><?= $percentage ?>%</h3>
</div>

</div>

<!-- ACTIONS -->
<div class="bg-white p-8 rounded-3xl shadow-xl grid grid-cols-1 md:grid-cols-3 gap-6">

<a href="parent_view_attendance.php"
class="px-6 py-4 bg-indigo-600 text-white rounded-xl text-center font-semibold hover:bg-indigo-700">
📅 View Attendance
</a>

<a href="parent_view_materials.php"
class="px-6 py-4 bg-blue-600 text-white rounded-xl text-center font-semibold hover:bg-blue-700">
📚 Study Materials
</a>

<a href="parent_fees.php"
class="px-6 py-4 bg-green-600 text-white rounded-xl text-center font-semibold hover:bg-green-700">
💰 Fees Details
</a>

</div>

<!-- 🔔 NOTIFICATIONS -->
<div class="mt-10 bg-white p-6 rounded-2xl shadow">
<h3 class="text-xl font-bold mb-4">🔔 Notifications</h3>

<?php if(mysqli_num_rows($noti_q) > 0): ?>
    <?php while($n = mysqli_fetch_assoc($noti_q)): ?>
        <div class="border-b py-3">
            <p class="text-gray-800">
                <?= htmlspecialchars($n['message']) ?>
            </p>
            <span class="text-xs text-gray-400">
                <?= $n['created_at'] ?>
            </span>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p class="text-gray-500">No notifications available.</p>
<?php endif; ?>

</div>

</div>

<footer class="text-center text-gray-500 py-6">
© <?= date('Y') ?> School Management System
</footer>

</body>
</html>
