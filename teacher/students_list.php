<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'TEACHER') {
    header("Location: ../index.php");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if (!$conn) die("DB Connection Failed");

$students = mysqli_query($conn,"
    SELECT u.name, u.email, s.class, s.batch, s.roll_no
    FROM students s
    JOIN users u ON s.user_id = u.id
    ORDER BY s.class, s.batch, s.roll_no
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Students | Teacher</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen">

<div class="bg-white shadow px-8 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-indigo-600">👥 Students</h1>
    <a href="teacher_dashboard.php" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        Dashboard
    </a>
</div>

<div class="max-w-6xl mx-auto px-6 py-10">
<div class="bg-white rounded-3xl shadow-xl p-8">

<h2 class="text-xl font-bold mb-6">
    Student List
    <span class="text-gray-400 text-base font-normal">(<?= mysqli_num_rows($students) ?> students)</span>
</h2>

<div class="overflow-x-auto">
<table class="w-full border-collapse">
<thead>
<tr class="bg-indigo-600 text-white">
<th class="p-4 text-left">Roll No</th>
<th class="p-4 text-left">Name</th>
<th class="p-4 text-left">Class</th>
<th class="p-4 text-left">Batch</th>
<th class="p-4 text-left">Email</th>
</tr>
</thead>
<tbody>
<?php if (mysqli_num_rows($students) == 0): ?>
<tr><td colspan="5" class="p-6 text-center text-gray-500">No students registered yet.</td></tr>
<?php else: while ($s = mysqli_fetch_assoc($students)): ?>
<tr class="border-b hover:bg-indigo-50">
<td class="p-4 font-semibold"><?= htmlspecialchars($s['roll_no']) ?></td>
<td class="p-4"><?= htmlspecialchars($s['name']) ?></td>
<td class="p-4"><?= htmlspecialchars($s['class']) ?></td>
<td class="p-4"><?= htmlspecialchars($s['batch']) ?></td>
<td class="p-4 text-gray-500"><?= htmlspecialchars($s['email']) ?></td>
</tr>
<?php endwhile; endif; ?>
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
