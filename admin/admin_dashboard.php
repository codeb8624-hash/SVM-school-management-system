<?php
session_start();

// Admin protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | School Management</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
.dashboard-card{
    background:white;
    padding:24px;
    border-radius:16px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    transition:.3s;
}
.dashboard-card:hover{
    transform:translateY(-5px);
    box-shadow:0 20px 40px rgba(0,0,0,0.12);
}
.dashboard-card i{
    font-size:28px;
    margin-bottom:12px;
}
.dashboard-card h3{
    font-size:20px;
    font-weight:700;
    margin-bottom:4px;
}
.dashboard-card p{
    font-size:14px;
    color:#6b7280;
}
</style>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen">

<!-- NAVBAR -->
<div class="bg-white shadow px-8 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-indigo-600">🏫 Admin Dashboard</h1>

    <div class="flex items-center gap-4">
        <span class="text-gray-600 font-medium">
            Welcome, <?= $_SESSION['name'] ?? 'Admin' ?>
        </span>
        <a href="../auth/logout.php"
           class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
           Logout
        </a>
    </div>
</div>

<!-- MAIN -->
<div class="max-w-6xl mx-auto px-6 py-10">

<h2 class="text-3xl font-bold text-gray-800 mb-8">
    Management Panel
</h2>

<!-- DASHBOARD CARDS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

<!-- REGISTER TEACHER -->
<a href="register_teacher.php" class="dashboard-card">
    <i class="fas fa-chalkboard-teacher text-indigo-600"></i>
    <h3>Register Teacher</h3>
    <p>Add new teachers</p>
</a>

<!-- REGISTER STUDENT -->
<a href="register_student.php" class="dashboard-card">
    <i class="fas fa-user-graduate text-green-600"></i>
    <h3>Register Student</h3>
    <p>Enroll students</p>
</a>

<!-- REGISTER PARENT -->
<a href="register_parent.php" class="dashboard-card">
    <i class="fas fa-users text-yellow-600"></i>
    <h3>Register Parent</h3>
    <p>Add parents</p>
</a>

<!-- ATTENDANCE -->
<a href="attendance_overview.php" class="dashboard-card">
    <i class="fas fa-calendar-check text-blue-600"></i>
    <h3>Attendance</h3>
    <p>Check class attendance</p>
</a>

<!-- FEES -->
<a href="fees_management.php" class="dashboard-card">
    <i class="fas fa-money-bill-wave text-emerald-600"></i>
    <h3>Fees Management</h3>
    <p>Payments & dues</p>
</a>

<!-- RESULTS -->
<a href="results.php" class="dashboard-card">
    <i class="fas fa-chart-line text-purple-600"></i>
    <h3>Results</h3>
    <p>Marks & grades</p>
</a>

<!-- 🔔 SEND NOTIFICATION -->
<a href="send_notification.php" class="dashboard-card">
    <i class="fas fa-bullhorn text-orange-600"></i>
    <h3>Send Notification</h3>
    <p>Notify parents & students</p>
</a>

<!-- 🗂️ NOTIFICATION HISTORY -->
<a href="../parent/notification_history.php" class="dashboard-card">
    <i class="fas fa-bell text-pink-600"></i>
    <h3>Notification History</h3>
    <p>View sent alerts</p>
</a>

</div>
</div>

<!-- FOOTER -->
<div class="text-center text-gray-500 text-sm py-6">
    © <?= date('Y') ?> School Management System
</div>

</body>
</html>
