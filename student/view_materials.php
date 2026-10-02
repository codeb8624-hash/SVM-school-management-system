<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'STUDENT') {
    header("Location: ../index.php");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if (!$conn) die("DB Connection Failed");

$materials = mysqli_query($conn,"
    SELECT id, title, subject, file_path, uploaded_at
    FROM study_materials
    ORDER BY uploaded_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Study Materials | Student</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen">

<div class="bg-white shadow px-8 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-indigo-600">📚 Study Materials</h1>
    <a href="student_dashboard.php" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        Dashboard
    </a>
</div>

<div class="max-w-5xl mx-auto px-6 py-10">
<div class="bg-white rounded-3xl shadow-xl p-8">

<h2 class="text-xl font-bold mb-6">
    Available Materials
    <span class="text-gray-400 text-base font-normal">(<?= mysqli_num_rows($materials) ?>)</span>
</h2>

<?php if (mysqli_num_rows($materials) == 0): ?>
<p class="text-gray-500 text-center py-8">No study materials available yet. Check back later.</p>
<?php else: ?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<?php while ($m = mysqli_fetch_assoc($materials)): ?>
<div class="border rounded-2xl p-6 hover:shadow-lg transition">
    <div class="flex items-start justify-between">
        <h3 class="font-bold text-gray-800"><?= htmlspecialchars($m['title']) ?></h3>
        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold whitespace-nowrap ml-2">
            <?= htmlspecialchars($m['subject'] ?? 'General') ?>
        </span>
    </div>
    <p class="text-xs text-gray-400 mt-2">Uploaded: <?= htmlspecialchars($m['uploaded_at']) ?></p>
    <a href="../<?= htmlspecialchars($m['file_path']) ?>" target="_blank"
       class="inline-block mt-4 px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
        ⬇ Download
    </a>
</div>
<?php endwhile; ?>
</div>
<?php endif; ?>

</div>
</div>

<footer class="text-center text-gray-500 py-6">
© <?= date('Y') ?> School Management System
</footer>

</body>
</html>
