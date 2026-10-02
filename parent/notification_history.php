<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'PARENT') {
    header("Location: ../index.php");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if(!$conn){
    die("DB Connection Failed");
}

$user_id = $_SESSION['user_id'];

/* Fetch notifications */
$notifications = mysqli_query($conn,"
    SELECT title, message, status, created_at
    FROM notifications
    WHERE user_id = '$user_id'
    AND role = 'PARENT'
    ORDER BY created_at DESC
");

if(!$notifications){
    die("SQL Error: ".mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Notification History</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen">

<!-- HEADER -->
<div class="bg-white shadow px-8 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-indigo-600">🔔 Notifications</h1>
    <a href="parent_dashboard.php"
       class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
       Dashboard
    </a>
</div>

<div class="max-w-5xl mx-auto px-6 py-10">

<div class="bg-white rounded-3xl shadow-xl p-8">

<h2 class="text-xl font-bold mb-6">Notification History</h2>

<?php if(mysqli_num_rows($notifications) > 0): ?>

<ul class="space-y-4">
<?php while($n = mysqli_fetch_assoc($notifications)): ?>

<li class="p-4 rounded-xl border-l-4
    <?= $n['status']=='UNREAD'
        ? 'border-indigo-600 bg-indigo-50'
        : 'border-gray-300 bg-gray-50' ?>">

    <div class="flex justify-between items-center">
        <h3 class="font-semibold text-gray-800">
            <?= htmlspecialchars($n['title']) ?>
        </h3>
        <span class="text-xs text-gray-500">
            <?= date("d M Y, h:i A", strtotime($n['created_at'])) ?>
        </span>
    </div>

    <p class="text-gray-600 mt-1">
        <?= htmlspecialchars($n['message']) ?>
    </p>

</li>

<?php endwhile; ?>
</ul>

<?php else: ?>
<p class="text-gray-500 text-center">
    No notifications available.
</p>
<?php endif; ?>

</div>
</div>

<footer class="text-center text-gray-500 py-6">
© <?= date('Y') ?> School Management System
</footer>

</body>
</html>
