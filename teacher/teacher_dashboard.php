<?php
session_start();

// Teacher protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'TEACHER') {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Teacher Dashboard | School Management</title>
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

<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen">

<!-- NAVBAR -->
<div class="bg-white shadow px-8 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-emerald-600">
        👨‍🏫 Teacher Dashboard
    </h1>

    <div class="flex items-center gap-4">
        <span class="text-gray-600 font-medium">
            Welcome, <?php echo $_SESSION['name'] ?? 'Teacher'; ?>
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
    Teacher Panel
</h2>

<!-- DASHBOARD CARDS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

    <!-- MARK ATTENDANCE -->
    <a href="mark_attendance.php" class="dashboard-card">
        <i class="fas fa-calendar-check text-blue-600"></i>
        <h3>Mark Attendance</h3>
        <p>Daily student attendance</p>
    </a>

    <!-- VIEW ATTENDANCE -->
    <a href="view_attendance.php" class="dashboard-card">
        <i class="fas fa-eye text-indigo-600"></i>
        <h3>View Attendance</h3>
        <p>Class attendance history</p>
    </a>

    <!-- ADD RESULTS -->
    <a href="add_results.php" class="dashboard-card">
        <i class="fas fa-pen text-purple-600"></i>
        <h3>Add Results</h3>
        <p>Enter marks & grades</p>
    </a>

    <!-- STUDENT LIST -->
    <a href="students_list.php" class="dashboard-card">
        <i class="fas fa-users text-green-600"></i>
        <h3>Students</h3>
        <p>View assigned students</p>
    </a>

    <!-- UPLOAD MATERIAL -->
    <a href="study_material.php" class="dashboard-card">
        <i class="fas fa-file-upload text-orange-600"></i>
        <h3>Study Material</h3>
        <p>Upload notes & PDFs</p>
    </a>

    <!-- NOTICE -->
    <a href="teacher_notices.php" class="dashboard-card">
        <i class="fas fa-bullhorn text-red-600"></i>
        <h3>Notices</h3>
        <p>Send class notices</p>
    </a>

</div>

</div>

<!-- FOOTER -->
<div class="text-center text-gray-500 text-sm py-6">
    © <?php echo date('Y'); ?> School Management System
</div>

</body>
</html>
