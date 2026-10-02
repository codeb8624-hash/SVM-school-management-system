<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../index.php");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if(!$conn){
    die("DB Connection Failed");
}

/* 🗃️ ARCHIVE ATTENDANCE (OLDER THAN 30 DAYS) */
mysqli_query($conn,"
    INSERT INTO attendance_archive (student_id, date, status)
    SELECT student_id, date, status
    FROM attendance
    WHERE date < DATE_SUB(CURDATE(), INTERVAL 30 DAY)
");

mysqli_query($conn,"
    DELETE FROM attendance
    WHERE date < DATE_SUB(CURDATE(), INTERVAL 30 DAY)
");

/* 📊 QUICK STATS (LIVE DATA ONLY) */
$stats_q = mysqli_query($conn,"
    SELECT
        COUNT(*) AS total,
        SUM(status='P') AS present,
        SUM(status='A') AS absent
    FROM attendance
");
$stats = mysqli_fetch_assoc($stats_q);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Attendance Overview | Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
.card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.08);
}
.stat-card{
    background:white;
    padding:24px;
    border-radius:18px;
    box-shadow:0 15px 30px rgba(0,0,0,0.08);
}
</style>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen">

<!-- NAVBAR -->
<div class="bg-white shadow px-8 py-4 flex justify-between items-center sticky top-0 z-10">
    <h1 class="text-2xl font-bold text-indigo-600">📊 Attendance Overview</h1>
    <a href="admin_dashboard.php"
       class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        Dashboard
    </a>
</div>

<div class="max-w-6xl mx-auto px-6 py-10">

<!-- 🔥 QUICK STATS -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

<div class="stat-card">
<p class="text-gray-500 text-sm">Total Records</p>
<h3 class="text-3xl font-bold text-indigo-600"><?= $stats['total'] ?? 0 ?></h3>
</div>

<div class="stat-card">
<p class="text-gray-500 text-sm">Present</p>
<h3 class="text-3xl font-bold text-green-600"><?= $stats['present'] ?? 0 ?></h3>
</div>

<div class="stat-card">
<p class="text-gray-500 text-sm">Absent</p>
<h3 class="text-3xl font-bold text-red-600"><?= $stats['absent'] ?? 0 ?></h3>
</div>

</div>

<!-- TABLE -->
<div class="card p-8">

<h2 class="text-xl font-bold mb-6">
Student Attendance Records (Last 30 Days)
</h2>

<div class="overflow-x-auto">
<table class="w-full border-collapse">

<thead>
<tr class="bg-indigo-600 text-white">
<th class="p-4">Date</th>
<th class="p-4">Student Name</th>
<th class="p-4 text-center">Status</th>
</tr>
</thead>

<tbody>
<?php
$q = mysqli_query($conn,"
    SELECT 
        a.date,
        u.name AS student_name,
        a.status
    FROM attendance a
    JOIN students s ON a.student_id = s.id
    JOIN users u ON s.user_id = u.id
    ORDER BY a.date DESC
");

if(mysqli_num_rows($q) == 0){
    echo "<tr><td colspan='3' class='p-6 text-center text-gray-500'>
          No recent attendance records.
          </td></tr>";
}

while($r = mysqli_fetch_assoc($q)){
?>
<tr class="border-b hover:bg-indigo-50">
<td class="p-4"><?= htmlspecialchars($r['date']) ?></td>
<td class="p-4 font-semibold"><?= htmlspecialchars($r['student_name']) ?></td>
<td class="p-4 text-center font-bold">
<?php if($r['status']=='P'): ?>
<span class="px-4 py-1 bg-green-100 text-green-700 rounded-full">Present</span>
<?php else: ?>
<span class="px-4 py-1 bg-red-100 text-red-700 rounded-full">Absent</span>
<?php endif; ?>
</td>
</tr>
<?php } ?>
</tbody>

</table>
</div>

</div>
</div>

<footer class="text-center text-gray-500 py-6">
© <?= date('Y') ?> School Management System
</footer>

</body>
</html>
