<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'TEACHER') {
    header("Location: ../index.php");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if (!$conn) die("DB Connection Failed");

$class_filter = $_GET['class'] ?? '';

$sql = "
    SELECT a.date, u.name AS student_name, s.class, s.batch, a.status
    FROM attendance a
    JOIN students s ON a.student_id = s.id
    JOIN users u ON s.user_id = u.id
";
if ($class_filter !== '' && ctype_digit($class_filter)) {
    $sql .= " WHERE s.class = '" . mysqli_real_escape_string($conn, $class_filter) . "'";
}
$sql .= " ORDER BY a.date DESC, u.name LIMIT 200";

$records = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Attendance | Teacher</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen">

<div class="bg-white shadow px-8 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-indigo-600">👁 View Attendance</h1>
    <a href="teacher_dashboard.php" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        Dashboard
    </a>
</div>

<div class="max-w-6xl mx-auto px-6 py-10">
<div class="bg-white rounded-3xl shadow-xl p-8">

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold">Class Attendance History</h2>

    <form method="GET" class="flex gap-3">
        <select name="class" class="border p-2 rounded">
            <option value="">All Classes</option>
            <?php for ($i = 1; $i <= 12; $i++): ?>
            <option value="<?= $i ?>" <?= $class_filter == $i ? 'selected' : '' ?>>Class <?= $i ?></option>
            <?php endfor; ?>
        </select>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Filter</button>
    </form>
</div>

<div class="overflow-x-auto">
<table class="w-full border-collapse">
<thead>
<tr class="bg-indigo-600 text-white">
<th class="p-4 text-left">Date</th>
<th class="p-4 text-left">Student</th>
<th class="p-4 text-left">Class</th>
<th class="p-4 text-left">Batch</th>
<th class="p-4 text-center">Status</th>
</tr>
</thead>
<tbody>
<?php if (mysqli_num_rows($records) == 0): ?>
<tr><td colspan="5" class="p-6 text-center text-gray-500">No attendance records found.</td></tr>
<?php else: while ($r = mysqli_fetch_assoc($records)): ?>
<tr class="border-b hover:bg-indigo-50">
<td class="p-4"><?= htmlspecialchars($r['date']) ?></td>
<td class="p-4 font-semibold"><?= htmlspecialchars($r['student_name']) ?></td>
<td class="p-4"><?= htmlspecialchars($r['class']) ?></td>
<td class="p-4"><?= htmlspecialchars($r['batch']) ?></td>
<td class="p-4 text-center">
<?php if ($r['status'] === 'P'): ?>
<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-bold">Present</span>
<?php else: ?>
<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-bold">Absent</span>
<?php endif; ?>
</td>
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
