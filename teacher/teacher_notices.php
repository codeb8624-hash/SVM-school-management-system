<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'TEACHER') {
    header("Location: ../index.php");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if (!$conn) die("DB Connection Failed");

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title   = trim($_POST['title'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $role    = $_POST['role'] ?? '';

    if ($title === '' || $message === '' || !in_array($role, ['ALL', 'STUDENT', 'PARENT'], true)) {
        $msg = "error|Title, message and audience are required.";
    } else {
        $title_esc   = mysqli_real_escape_string($conn, $title);
        $message_esc = mysqli_real_escape_string($conn, $message);

        $ok = mysqli_query($conn,"
            INSERT INTO notifications (title, message, role)
            VALUES ('$title_esc', '$message_esc', '$role')
        ");

        $msg = $ok ? "success|Notice sent successfully!" : "error|Error sending notice.";
    }
}

$notices = mysqli_query($conn,"
    SELECT title, message, role, created_at
    FROM notifications
    WHERE role IN ('ALL', 'STUDENT', 'PARENT')
    ORDER BY created_at DESC
    LIMIT 20
");

list($msg_type, $msg_text) = $msg ? explode('|', $msg) : ['', ''];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Notices | Teacher</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen">

<div class="bg-white shadow px-8 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-indigo-600">📢 Class Notices</h1>
    <a href="teacher_dashboard.php" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        Dashboard
    </a>
</div>

<div class="max-w-4xl mx-auto px-6 py-10">

<?php if ($msg_type === 'success'): ?>
<div class="mb-6 p-3 bg-green-100 text-green-700 rounded"><?= htmlspecialchars($msg_text) ?></div>
<?php elseif ($msg_type === 'error'): ?>
<div class="mb-6 p-3 bg-red-100 text-red-700 rounded"><?= htmlspecialchars($msg_text) ?></div>
<?php endif; ?>

<!-- SEND -->
<div class="bg-white rounded-3xl shadow-xl p-8 mb-10">
<h2 class="text-xl font-bold mb-6">Send Notice</h2>

<form method="POST" class="space-y-6">

<div>
<label class="font-semibold">Title</label>
<input type="text" name="title" required
       class="w-full border p-3 rounded">
</div>

<div>
<label class="font-semibold">Message</label>
<textarea name="message" rows="4" required
          class="w-full border p-3 rounded"></textarea>
</div>

<div>
<label class="font-semibold">Send To</label>
<select name="role" required class="w-full border p-3 rounded">
    <option value="STUDENT">Students</option>
    <option value="PARENT">Parents</option>
    <option value="ALL">Everyone</option>
</select>
</div>

<div class="text-right">
<button class="px-8 py-3 bg-indigo-600 text-white rounded-xl">
    📨 Send Notice
</button>
</div>

</form>
</div>

<!-- SENT LIST -->
<div class="bg-white rounded-3xl shadow-xl p-8">
<h2 class="text-xl font-bold mb-6">Recent Notices</h2>

<?php if (mysqli_num_rows($notices) == 0): ?>
<p class="text-gray-500 text-center py-4">No notices sent yet.</p>
<?php else: while ($n = mysqli_fetch_assoc($notices)): ?>
<div class="border-b py-4">
    <div class="flex justify-between items-start">
        <h3 class="font-bold text-gray-800"><?= htmlspecialchars($n['title']) ?></h3>
        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold whitespace-ml-2">
            <?= htmlspecialchars($n['role']) ?>
        </span>
    </div>
    <p class="text-gray-600 mt-1"><?= nl2br(htmlspecialchars($n['message'])) ?></p>
    <span class="text-xs text-gray-400"><?= htmlspecialchars($n['created_at']) ?></span>
</div>
<?php endwhile; endif; ?>
</div>

</div>

<footer class="text-center text-gray-500 py-6">
© <?= date('Y') ?> School Management System
</footer>

</body>
</html>
