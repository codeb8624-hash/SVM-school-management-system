<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../index.php");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if (!$conn) {
    die("DB Connection Failed");
}

$msg = "";
$err = "";

if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    mysqli_query($conn, "DELETE FROM results WHERE id = " . (int)$_GET['delete']);
    $msg = "Result deleted.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $student_id   = (int)($_POST['student_id'] ?? 0);
    $subject_id   = (int)($_POST['subject_id'] ?? 0);
    $exam_name    = trim($_POST['exam_name'] ?? '');
    $marks        = trim($_POST['marks_obtained'] ?? '');
    $total        = trim($_POST['total_marks'] ?? '');
    $published    = isset($_POST['published']) ? 1 : 0;

    if (!$student_id || !$subject_id || $exam_name === '' || $marks === '' || $total === ''
        || !is_numeric($marks) || !is_numeric($total)) {
        $err = "All fields are required and marks must be numeric.";
    } elseif ((float)$total <= 0 || (float)$marks < 0 || (float)$marks > (float)$total) {
        $err = "Marks obtained must be between 0 and total marks.";
    } else {
        $percent = ((float)$marks / (float)$total) * 100;
        if ($percent >= 90)      $grade = 'A+';
        elseif ($percent >= 80)  $grade = 'A';
        elseif ($percent >= 70)  $grade = 'B';
        elseif ($percent >= 60)  $grade = 'C';
        elseif ($percent >= 40)  $grade = 'D';
        else                     $grade = 'F';

        $exam_esc = mysqli_real_escape_string($conn, $exam_name);
        $published_at = $published ? "NOW()" : "NULL";

        $q = mysqli_query($conn,"
            INSERT INTO results (student_id, subject_id, exam_name, marks_obtained, total_marks, grade, published_at)
            VALUES ($student_id, $subject_id, '$exam_esc', " . (float)$marks . ", " . (float)$total . ", '$grade', $published_at)
        ");

        if ($q) {
            $msg = "Result added successfully!";
        } else {
            $err = "Error adding result: " . mysqli_error($conn);
        }
    }
}

/* STATS */
$stats = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT
        COUNT(*) AS total,
        SUM(published_at IS NOT NULL) AS published,
        AVG(CASE WHEN total_marks > 0 THEN marks_obtained / total_marks * 100 END) AS avg_pct
    FROM results
"));

$students = mysqli_query($conn,"
    SELECT s.id, u.name, s.class, s.batch
    FROM students s
    JOIN users u ON s.user_id = u.id
    ORDER BY u.name
");

$subjects = mysqli_query($conn,"
    SELECT id, subject_name, class
    FROM subjects
    ORDER BY subject_name
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Results | Admin</title>
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
    <h1 class="text-2xl font-bold text-indigo-600">📊 Results — Marks & Grades</h1>
    <a href="admin_dashboard.php"
       class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        Dashboard
    </a>
</div>

<div class="max-w-6xl mx-auto px-6 py-10">

<?php if ($msg): ?>
<div class="mb-6 p-3 bg-green-100 text-green-700 rounded"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
<?php if ($err): ?>
<div class="mb-6 p-3 bg-red-100 text-red-700 rounded"><?= htmlspecialchars($err) ?></div>
<?php endif; ?>

<!-- QUICK STATS -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
<div class="stat-card">
    <p class="text-gray-500 text-sm">Total Results</p>
    <h3 class="text-3xl font-bold text-indigo-600"><?= (int)($stats['total'] ?? 0) ?></h3>
</div>
<div class="stat-card">
    <p class="text-gray-500 text-sm">Published</p>
    <h3 class="text-3xl font-bold text-green-600"><?= (int)($stats['published'] ?? 0) ?></h3>
</div>
<div class="stat-card">
    <p class="text-gray-500 text-sm">Average %</p>
    <h3 class="text-3xl font-bold text-purple-600"><?= $stats['avg_pct'] !== null ? round($stats['avg_pct'], 1) : 0 ?>%</h3>
</div>
</div>

<!-- ADD RESULT -->
<div class="card p-8 mb-10">
<h2 class="text-xl font-bold mb-6">➕ Add Result</h2>

<form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">

<div>
<label class="font-semibold text-gray-700">Student</label>
<select name="student_id" required class="w-full mt-1 px-4 py-2 border rounded-lg">
    <option value="">Select Student</option>
    <?php while ($s = mysqli_fetch_assoc($students)): ?>
    <option value="<?= (int)$s['id'] ?>">
        <?= htmlspecialchars($s['name']) ?> (Class <?= htmlspecialchars($s['class']) ?> - Batch <?= htmlspecialchars($s['batch']) ?>)
    </option>
    <?php endwhile; ?>
</select>
</div>

<div>
<label class="font-semibold text-gray-700">Subject</label>
<select name="subject_id" required class="w-full mt-1 px-4 py-2 border rounded-lg">
    <option value="">Select Subject</option>
    <?php mysqli_data_seek($subjects, 0);
          while ($sub = mysqli_fetch_assoc($subjects)): ?>
    <option value="<?= (int)$sub['id'] ?>">
        <?= htmlspecialchars($sub['subject_name']) ?> (Class <?= htmlspecialchars($sub['class']) ?>)
    </option>
    <?php endwhile; ?>
</select>
</div>

<div>
<label class="font-semibold text-gray-700">Exam Name</label>
<input type="text" name="exam_name" required placeholder="e.g. Mid Term 2026"
       class="w-full mt-1 px-4 py-2 border rounded-lg">
</div>

<div>
<label class="font-semibold text-gray-700">Marks Obtained</label>
<input type="number" name="marks_obtained" required min="0" step="0.01"
       class="w-full mt-1 px-4 py-2 border rounded-lg">
</div>

<div>
<label class="font-semibold text-gray-700">Total Marks</label>
<input type="number" name="total_marks" required min="1" step="0.01"
       class="w-full mt-1 px-4 py-2 border rounded-lg">
</div>

<div class="flex items-end gap-3">
<label class="flex items-center gap-2 text-gray-700 font-semibold pb-2">
    <input type="checkbox" name="published" value="1" class="w-4 h-4">
    Publish now
</label>
</div>

<div class="md:col-span-3 text-right">
<button class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700">
    ➕ Add Result
</button>
</div>

</form>
</div>

<!-- RESULTS TABLE -->
<div class="card p-8">

<h2 class="text-xl font-bold mb-6">All Results</h2>

<div class="overflow-x-auto">
<table class="w-full border-collapse">

<thead>
<tr class="bg-indigo-600 text-white">
<th class="p-4 text-left">Student</th>
<th class="p-4 text-left">Subject</th>
<th class="p-4 text-left">Exam</th>
<th class="p-4 text-center">Marks</th>
<th class="p-4 text-center">%</th>
<th class="p-4 text-center">Grade</th>
<th class="p-4 text-center">Status</th>
<th class="p-4 text-center">Action</th>
</tr>
</thead>

<tbody>
<?php
$q = mysqli_query($conn,"
    SELECT r.*, u.name AS student_name, s.class, sub.subject_name
    FROM results r
    JOIN students s ON r.student_id = s.id
    JOIN users u ON s.user_id = u.id
    JOIN subjects sub ON r.subject_id = sub.id
    ORDER BY r.created_at DESC, r.id DESC
");

if (mysqli_num_rows($q) == 0) {
    echo "<tr><td colspan='8' class='p-6 text-center text-gray-500'>
          No results found. Add one above.
          </td></tr>";
}

while ($r = mysqli_fetch_assoc($q)) {
    $pct = $r['total_marks'] > 0 ? round($r['marks_obtained'] / $r['total_marks'] * 100, 1) : 0;
    $grade_color = ($r['grade'] === 'F') ? 'red' : (($r['grade'] === 'A+' || $r['grade'] === 'A') ? 'green' : 'indigo');
?>
<tr class="border-b hover:bg-indigo-50">
<td class="p-4 font-semibold">
    <?= htmlspecialchars($r['student_name']) ?>
    <span class="text-xs text-gray-400 block">Class <?= htmlspecialchars($r['class']) ?></span>
</td>
<td class="p-4"><?= htmlspecialchars($r['subject_name']) ?></td>
<td class="p-4"><?= htmlspecialchars($r['exam_name']) ?></td>
<td class="p-4 text-center"><?= $r['marks_obtained'] + 0 ?> / <?= $r['total_marks'] + 0 ?></td>
<td class="p-4 text-center font-bold"><?= $pct ?>%</td>
<td class="p-4 text-center">
    <span class="px-3 py-1 bg-<?= $grade_color ?>-100 text-<?= $grade_color ?>-700 rounded-full font-bold">
        <?= htmlspecialchars($r['grade']) ?>
    </span>
</td>
<td class="p-4 text-center">
<?php if ($r['published_at']): ?>
    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Published</span>
<?php else: ?>
    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm">Draft</span>
<?php endif; ?>
</td>
<td class="p-4 text-center">
    <a href="?delete=<?= (int)$r['id'] ?>"
       onclick="return confirm('Delete this result?')"
       class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">
        🗑 Delete
    </a>
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
