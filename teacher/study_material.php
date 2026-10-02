<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'TEACHER') {
    header("Location: ../index.php");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if (!$conn) die("DB Connection Failed");

$user_id = (int)$_SESSION['user_id'];
$teacher_row = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT id FROM teachers WHERE user_id = $user_id
")) ?: [];
$teacher_id = (int)($teacher_row['id'] ?? 0);

$msg = "";
$err = "";

if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $row = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT file_path FROM study_materials WHERE id = $del_id AND teacher_id = $teacher_id
    "));
    if ($row) {
        $full = dirname(__DIR__) . '/' . $row['file_path'];
        if (is_file($full)) @unlink($full);
        mysqli_query($conn, "DELETE FROM study_materials WHERE id = $del_id");
        $msg = "Material deleted.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title   = trim($_POST['title'] ?? '');
    $subject = trim($_POST['subject'] ?? '');

    if ($title === '') {
        $err = "Title is required.";
    } elseif (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $err = "Please choose a file to upload.";
    } else {
        $allowed = ['pdf', 'doc', 'docx', 'txt', 'png', 'jpg', 'jpeg'];
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $err = "Only PDF, DOC, DOCX, TXT, PNG, JPG files are allowed.";
        } elseif ($_FILES['file']['size'] > 5 * 1024 * 1024) {
            $err = "File must be smaller than 5 MB.";
        } else {
            $dir = dirname(__DIR__) . '/assets/materials';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $fname = uniqid('mat_') . '.' . $ext;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $dir . '/' . $fname)) {
                $title_esc = mysqli_real_escape_string($conn, $title);
                $subject_esc = mysqli_real_escape_string($conn, $subject);
                mysqli_query($conn,"
                    INSERT INTO study_materials (teacher_id, title, subject, file_path)
                    VALUES ($teacher_id, '$title_esc', '$subject_esc', 'assets/materials/$fname')
                ");
                $msg = "Study material uploaded successfully!";
            } else {
                $err = "Upload failed. Please try again.";
            }
        }
    }
}

$materials = mysqli_query($conn,"
    SELECT m.*, t.id AS tid
    FROM study_materials m
    LEFT JOIN teachers t ON m.teacher_id = t.id
    ORDER BY m.uploaded_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Study Material | Teacher</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen">

<div class="bg-white shadow px-8 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-indigo-600">📚 Study Material</h1>
    <a href="teacher_dashboard.php" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        Dashboard
    </a>
</div>

<div class="max-w-5xl mx-auto px-6 py-10">

<?php if ($msg): ?>
<div class="mb-6 p-3 bg-green-100 text-green-700 rounded"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
<?php if ($err): ?>
<div class="mb-6 p-3 bg-red-100 text-red-700 rounded"><?= htmlspecialchars($err) ?></div>
<?php endif; ?>

<!-- UPLOAD -->
<div class="bg-white rounded-3xl shadow-xl p-8 mb-10">
<h2 class="text-xl font-bold mb-6">📤 Upload Notes & PDFs</h2>

<form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">

<div>
<label class="font-semibold text-gray-700">Title</label>
<input type="text" name="title" required placeholder="e.g. Chapter 5 Notes"
       class="w-full mt-1 px-4 py-2 border rounded-lg">
</div>

<div>
<label class="font-semibold text-gray-700">Subject (optional)</label>
<input type="text" name="subject" placeholder="e.g. Mathematics"
       class="w-full mt-1 px-4 py-2 border rounded-lg">
</div>

<div class="flex gap-3">
<input type="file" name="file" required
       accept=".pdf,.doc,.docx,.txt,.png,.jpg,.jpeg"
       class="w-full px-4 py-2 border rounded-lg">
</div>

<div class="md:col-span-3 text-right">
<button class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700">
    📤 Upload
</button>
<p class="text-xs text-gray-400 mt-2">PDF, DOC, DOCX, TXT, PNG, JPG — max 5 MB</p>
</div>

</form>
</div>

<!-- LIST -->
<div class="bg-white rounded-3xl shadow-xl p-8">
<h2 class="text-xl font-bold mb-6">Uploaded Materials</h2>

<div class="overflow-x-auto">
<table class="w-full border-collapse">
<thead>
<tr class="bg-indigo-600 text-white">
<th class="p-4 text-left">Title</th>
<th class="p-4 text-left">Subject</th>
<th class="p-4 text-left">Uploaded</th>
<th class="p-4 text-center">Action</th>
</tr>
</thead>
<tbody>
<?php if (mysqli_num_rows($materials) == 0): ?>
<tr><td colspan="4" class="p-6 text-center text-gray-500">No materials uploaded yet.</td></tr>
<?php else: while ($m = mysqli_fetch_assoc($materials)): ?>
<tr class="border-b hover:bg-indigo-50">
<td class="p-4 font-semibold">
    <a class="text-indigo-600 hover:underline" target="_blank"
       href="../<?= htmlspecialchars($m['file_path']) ?>">
        <?= htmlspecialchars($m['title']) ?>
    </a>
</td>
<td class="p-4"><?= htmlspecialchars($m['subject'] ?? '-') ?></td>
<td class="p-4 text-gray-500"><?= htmlspecialchars($m['uploaded_at']) ?></td>
<td class="p-4 text-center">
<?php if ((int)$m['teacher_id'] === $teacher_id): ?>
<a href="?delete=<?= (int)$m['id'] ?>" onclick="return confirm('Delete this material?')"
   class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">🗑 Delete</a>
<?php else: ?>
<span class="text-gray-300">—</span>
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
