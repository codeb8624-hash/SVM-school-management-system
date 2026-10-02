<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'STUDENT') {
    header("Location: ../index.php");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if (!$conn) die("DB Connection Failed");

$user_id = (int)$_SESSION['user_id'];

$student_row = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT id FROM students WHERE user_id = $user_id
")) ?: [];
$student_id = (int)($student_row['id'] ?? 0);

$results = $student_id ? mysqli_query($conn,"
    SELECT r.*, sub.subject_name
    FROM results r
    JOIN subjects sub ON r.subject_id = sub.id
    WHERE r.student_id = $student_id AND r.published_at IS NOT NULL
    ORDER BY r.created_at DESC, r.id DESC
") : false;

$stats = ['n' => 0, 'avg' => null, 'best' => null];
if ($results && mysqli_num_rows($results) > 0) {
    $rows = [];
    while ($r = mysqli_fetch_assoc($results)) {
        $rows[] = $r;
    }
    mysqli_data_seek($results, 0);
    $pcts = array_map(fn($r) => $r['total_marks'] > 0 ? ($r['marks_obtained'] / $r['total_marks']) * 100 : 0, $rows);
    $stats['n']   = count($rows);
    $stats['avg'] = round(array_sum($pcts) / count($pcts), 1);
    $stats['best'] = round(max($pcts), 1);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Results | Student</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen">

<div class="bg-white shadow px-8 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-indigo-600">📝 My Results</h1>
    <a href="student_dashboard.php" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        Dashboard
    </a>
</div>

<div class="max-w-5xl mx-auto px-6 py-10">

<!-- STATS -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
<div class="bg-white p-6 rounded-2xl shadow">
    <p class="text-gray-500 text-sm">Published Exams</p>
    <h3 class="text-3xl font-bold text-indigo-600"><?= $stats['n'] ?></h3>
</div>
<div class="bg-white p-6 rounded-2xl shadow">
    <p class="text-gray-500 text-sm">Average</p>
    <h3 class="text-3xl font-bold text-green-600"><?= $stats['avg'] !== null ? $stats['avg'] : 0 ?>%</h3>
</div>
<div class="bg-white p-6 rounded-2xl shadow">
    <p class="text-gray-500 text-sm">Best Score</p>
    <h3 class="text-3xl font-bold text-purple-600"><?= $stats['best'] !== null ? $stats['best'] : 0 ?>%</h3>
</div>
</div>

<!-- TABLE -->
<div class="bg-white rounded-3xl shadow-xl p-8">
<h2 class="text-xl font-bold mb-6">Exam Results</h2>

<div class="overflow-x-auto">
<table class="w-full border-collapse">
<thead>
<tr class="bg-indigo-600 text-white">
<th class="p-4 text-left">Exam</th>
<th class="p-4 text-left">Subject</th>
<th class="p-4 text-center">Marks</th>
<th class="p-4 text-center">%</th>
<th class="p-4 text-center">Grade</th>
<th class="p-4 text-left">Published</th>
</tr>
</thead>
<tbody>
<?php if (!$results || mysqli_num_rows($results) == 0): ?>
<tr><td colspan="6" class="p-6 text-center text-gray-500">No published results yet.</td></tr>
<?php else: while ($r = mysqli_fetch_assoc($results)):
    $pct = $r['total_marks'] > 0 ? round($r['marks_obtained'] / $r['total_marks'] * 100, 1) : 0;
    $color = ($r['grade'] === 'F') ? 'red' : (($r['grade'] === 'A+' || $r['grade'] === 'A') ? 'green' : 'indigo');
?>
<tr class="border-b hover:bg-indigo-50">
<td class="p-4 font-semibold"><?= htmlspecialchars($r['exam_name']) ?></td>
<td class="p-4"><?= htmlspecialchars($r['subject_name']) ?></td>
<td class="p-4 text-center"><?= $r['marks_obtained'] + 0 ?> / <?= $r['total_marks'] + 0 ?></td>
<td class="p-4 text-center font-bold"><?= $pct ?>%</td>
<td class="p-4 text-center">
    <span class="px-3 py-1 bg-<?= $color ?>-100 text-<?= $color ?>-700 rounded-full font-bold">
        <?= htmlspecialchars($r['grade']) ?>
    </span>
</td>
<td class="p-4 text-gray-500 text-sm"><?= htmlspecialchars($r['published_at']) ?></td>
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
