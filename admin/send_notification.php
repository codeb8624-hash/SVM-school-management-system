<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../index.php");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if(!$conn) die("DB Connection Failed");

$msg = "";

/* Handle submit */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title   = mysqli_real_escape_string($conn, $_POST['title']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $role    = $_POST['role'];

    mysqli_query($conn,"
        INSERT INTO notifications (title, message, role)
        VALUES ('$title','$message','$role')
    ");

    $msg = "Notification sent successfully!";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Send Notification</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen">

<!-- HEADER -->
<div class="bg-white shadow px-8 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-indigo-600">🔔 Send Notification</h1>
    <a href="admin_dashboard.php"
       class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
        Dashboard
    </a>
</div>

<div class="max-w-4xl mx-auto px-6 py-10">

<div class="bg-white rounded-3xl shadow-xl p-8">

<h2 class="text-xl font-bold mb-6">Create Notification</h2>

<?php if($msg): ?>
<div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
    <?= $msg ?>
</div>
<?php endif; ?>

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
<option value="ALL">All Users</option>
<option value="STUDENT">Students</option>
<option value="PARENT">Parents</option>
<option value="TEACHER">Teachers</option>
</select>
</div>

<div class="text-right">
<button class="px-8 py-3 bg-indigo-600 text-white rounded-xl">
📨 Send Notification
</button>
</div>

</form>

</div>
</div>

<footer class="text-center text-gray-500 py-6">
© <?= date('Y') ?> School Management System
</footer>

</body>
</html>
