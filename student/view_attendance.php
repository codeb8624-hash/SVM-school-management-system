<?php
session_start();

/* 🔒 Student Protection */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'STUDENT') {
    header("Location: ../index.php");
    exit;
}

/* DB Connection */
$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if(!$conn){
    die("Database Connection Failed");
}

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Attendance</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen">

<!-- NAVBAR -->
<div class="bg-white shadow px-8 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-indigo-600">
        📅 My Attendance
    </h1>
    <a href="student_dashboard.php"
       class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        Dashboard
    </a>
</div>

<!-- MAIN -->
<div class="max-w-5xl mx-auto px-6 py-10">

<div class="bg-white rounded-3xl shadow-xl p-8">

<h2 class="text-xl font-bold text-gray-800 mb-6">
    Attendance History
</h2>

<div class="overflow-x-auto">
<table class="w-full border rounded-xl overflow-hidden">

<thead>
<tr class="bg-indigo-600 text-white">
    <th class="p-3 text-left">Date</th>
    <th class="p-3 text-center">Status</th>
</tr>
</thead>

<tbody>
<?php
$sql = "
    SELECT a.date, a.status
    FROM attendance a
    JOIN students s ON a.student_id = s.id
    WHERE s.user_id = '$user_id'
    ORDER BY a.date DESC
";

$q = mysqli_query($conn,$sql);

if(!$q){
    echo '<tr><td colspan="2" class="p-4 text-red-600">SQL Error</td></tr>';
}
elseif(mysqli_num_rows($q) == 0){
    echo '<tr><td colspan="2" class="p-4 text-center text-gray-500">No attendance records found</td></tr>';
}
else{
    while($row = mysqli_fetch_assoc($q)){
?>
<tr class="border-b hover:bg-gray-50">
    <td class="p-3 text-gray-700">
        <?= htmlspecialchars($row['date']) ?>
    </td>
    <td class="p-3 text-center font-bold">
        <?php if($row['status'] === 'P'): ?>
            <span class="px-4 py-1 rounded-full bg-green-100 text-green-700">
                Present
            </span>
        <?php else: ?>
            <span class="px-4 py-1 rounded-full bg-red-100 text-red-700">
                Absent
            </span>
        <?php endif; ?>
    </td>
</tr>
<?php }} ?>
</tbody>

</table>
</div>

</div>
</div>

<!-- FOOTER -->
<div class="text-center text-gray-500 text-sm py-6">
    © <?php echo date('Y'); ?> School Management System
</div>

</body>
</html>
