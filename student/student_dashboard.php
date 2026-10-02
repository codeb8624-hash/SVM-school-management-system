<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'STUDENT') {
    header("Location: ../index.php");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if(!$conn){
    die("DB Connection Failed");
}

$user_id = $_SESSION['user_id'];

/* Student info */
$student_q = mysqli_query($conn,"
    SELECT s.id, u.name, s.class, s.batch
    FROM students s
    JOIN users u ON s.user_id = u.id
    WHERE s.user_id = '$user_id'
");
$student = mysqli_fetch_assoc($student_q) ?: [];
$student_id = $student['id'] ?? 0;

/* Attendance summary */
$total_q = mysqli_query($conn,"
    SELECT 
        COUNT(*) AS total,
        SUM(status='P') AS present,
        SUM(status='A') AS absent
    FROM attendance
    WHERE student_id = '$student_id'
");
$att = mysqli_fetch_assoc($total_q);

$total = $att['total'] ?? 0;
$present = $att['present'] ?? 0;
$absent = $att['absent'] ?? 0;
$percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-100 via-blue-100 to-indigo-200 min-h-screen">

<!-- NAVBAR -->
<div class="bg-white/80 backdrop-blur-md shadow-lg px-8 py-4 flex justify-between items-center sticky top-0 z-50">
    <h1 class="text-2xl font-extrabold text-indigo-700 tracking-wide">
        🎓 Student Dashboard
    </h1>
    <a href="../auth/logout.php"
       class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 hover:scale-105 transition">
       Logout
    </a>
</div>

<div class="max-w-6xl mx-auto px-6 py-10">

<!-- WELCOME -->
<div class="mb-10 bg-white/70 backdrop-blur rounded-3xl shadow-xl p-8">
    <h2 class="text-3xl font-extrabold text-gray-800">
        Welcome, <?= htmlspecialchars($student['name'] ?? $_SESSION['name']) ?> 👋
    </h2>
    <p class="text-gray-600 mt-1">
        Class <?= $student['class'] ?? '-' ?> • Batch <?= $student['batch'] ?? '-' ?>
    </p>
</div>

<!-- STATS -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">

<div class="bg-white rounded-2xl shadow hover:shadow-2xl hover:-translate-y-1 transition p-6">
<p class="text-gray-500 text-sm">Total Days</p>
<h3 class="text-4xl font-bold mt-2"><?= $total ?></h3>
</div>

<div class="bg-white rounded-2xl shadow hover:shadow-2xl hover:-translate-y-1 transition p-6">
<p class="text-gray-500 text-sm">Present</p>
<h3 class="text-4xl font-bold mt-2 text-green-600"><?= $present ?></h3>
</div>

<div class="bg-white rounded-2xl shadow hover:shadow-2xl hover:-translate-y-1 transition p-6">
<p class="text-gray-500 text-sm">Absent</p>
<h3 class="text-4xl font-bold mt-2 text-red-600"><?= $absent ?></h3>
</div>

<div class="bg-white rounded-2xl shadow hover:shadow-2xl hover:-translate-y-1 transition p-6">
<p class="text-gray-500 text-sm">Attendance %</p>
<h3 class="text-4xl font-bold mt-2 text-indigo-600"><?= $percentage ?>%</h3>

<!-- Progress bar -->
<div class="w-full bg-gray-200 rounded-full h-2 mt-4">
    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-700"
         style="width: <?= $percentage ?>%">
    </div>
</div>
</div>

</div>

<!-- ACTIONS -->
<div class="bg-white/80 backdrop-blur rounded-3xl shadow-xl p-10">
    <h3 class="text-2xl font-bold text-gray-800 mb-6">
        Quick Actions
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <a href="view_attendance.php"
           class="flex flex-col items-center justify-center gap-3 p-6 bg-indigo-600 text-white rounded-2xl shadow-lg hover:bg-indigo-700 hover:scale-105 transition">
            📅
            <span class="font-semibold">View Attendance</span>
        </a>

        <a href="view_materials.php"
           class="flex flex-col items-center justify-center gap-3 p-6 bg-blue-600 text-white rounded-2xl shadow-lg hover:bg-blue-700 hover:scale-105 transition">
            📚
            <span class="font-semibold">Study Materials</span>
        </a>

        <a href="view_results.php"
           class="flex flex-col items-center justify-center gap-3 p-6 bg-purple-600 text-white rounded-2xl shadow-lg hover:bg-purple-700 hover:scale-105 transition">
            📝
            <span class="font-semibold">Exam Results</span>
        </a>

    </div>
</div>

</div>

<footer class="text-center text-gray-600 text-sm py-6">
© <?= date('Y') ?> School Management System
</footer>

</body>
</html>

