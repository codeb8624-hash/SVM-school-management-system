<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'TEACHER') {
    header("Location: ../index.php");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if (!$conn) die("DB Connection Failed");

$class = $_POST['class'] ?? '';
$batch = $_POST['batch'] ?? '';
$date  = $_POST['date'] ?? '';

$students = null;
if ($class && $batch) {
    $students = mysqli_query($conn,"
        SELECT 
            students.id,
            users.name
        FROM students
        JOIN users ON students.user_id = users.id
        WHERE students.class='$class'
        AND students.batch='$batch'
    ");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Mark Attendance | Teacher</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen">

<!-- HEADER -->
<div class="bg-white shadow px-8 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-indigo-600">📋 Mark Attendance</h1>
    <a href="teacher_dashboard.php"
       class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
       Dashboard
    </a>
</div>

<div class="max-w-5xl mx-auto px-6 py-10">
<div class="bg-white rounded-3xl shadow-xl p-8">

<h2 class="text-xl font-bold mb-6">Select Class, Batch & Date</h2>

<!-- FORM 1 : LOAD STUDENTS -->
<form method="POST">
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

<select name="class" required class="border p-2 rounded">
<option value="">Select Class</option>
<?php for($i=1;$i<=10;$i++): ?>
<option value="<?= $i ?>" <?= ($class==$i)?'selected':'' ?>>
    Class <?= $i ?>
</option>
<?php endfor; ?>
</select>

<select name="batch" required class="border p-2 rounded">
<option value="">Select Batch</option>
<?php foreach(['A','B','C','D'] as $b): ?>
<option value="<?= $b ?>" <?= ($batch==$b)?'selected':'' ?>>
    Batch <?= $b ?>
</option>
<?php endforeach; ?>
</select>

<input type="date" name="date"
       value="<?= htmlspecialchars($date) ?>"
       required class="border p-2 rounded">

</div>

<button class="bg-indigo-600 text-white px-6 py-2 rounded">
Load Students
</button>
</form>

<hr class="my-6">

<!-- FORM 2 : SAVE ATTENDANCE -->
<form method="POST" action="save_attendance.php">

<input type="hidden" name="class" value="<?= $class ?>">
<input type="hidden" name="batch" value="<?= $batch ?>">
<input type="hidden" name="date" value="<?= $date ?>">

<!-- 🔥 MARK ALL BUTTONS -->
<?php if ($students && mysqli_num_rows($students) > 0): ?>
<div class="flex gap-4 mb-4">
    <button type="button"
        onclick="markAll('P')"
        class="px-4 py-2 bg-green-600 text-white rounded">
        ✅ Mark All Present
    </button>

    <button type="button"
        onclick="markAll('A')"
        class="px-4 py-2 bg-red-600 text-white rounded">
        ❌ Mark All Absent
    </button>
</div>
<?php endif; ?>

<table class="w-full border rounded">
<thead class="bg-indigo-600 text-white">
<tr>
<th class="p-3 text-left">Student Name</th>
<th class="p-3 text-center">Present</th>
<th class="p-3 text-center">Absent</th>
</tr>
</thead>

<tbody>
<?php if ($students && mysqli_num_rows($students)>0): ?>
<?php while($row=mysqli_fetch_assoc($students)): ?>
<tr class="border-b">
<td class="p-3"><?= htmlspecialchars($row['name']) ?></td>

<td class="p-3 text-center">
<input type="radio"
       class="attendance-present"
       name="status[<?= $row['id'] ?>]"
       value="P" required>
</td>

<td class="p-3 text-center">
<input type="radio"
       class="attendance-absent"
       name="status[<?= $row['id'] ?>]"
       value="A">
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="3" class="p-4 text-center text-gray-500">
Select Class & Batch and click <b>Load Students</b>
</td>
</tr>
<?php endif; ?>
</tbody>
</table>

<div class="mt-6 text-right">
<button class="bg-indigo-600 text-white px-8 py-3 rounded-xl">
💾 Save Attendance
</button>
</div>

</form>
</div>
</div>

<footer class="text-center text-gray-500 py-6">
© 2026 School Management System
</footer>

<!-- 🔥 JAVASCRIPT -->
<script>
function markAll(type) {
    if (type === 'P') {
        document.querySelectorAll('.attendance-present')
            .forEach(r => r.checked = true);
    } else {
        document.querySelectorAll('.attendance-absent')
            .forEach(r => r.checked = true);
    }
}
</script>

</body>
</html>
