<?php
session_start();

// Admin protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../index.php");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if(!$conn){
    die("DB Connection Failed");
}

/* Fetch fee records */
$fees = mysqli_query($conn,"
    SELECT 
        f.id,
        u.name AS student_name,
        f.amount,
        f.status,
        f.razorpay_payment_id,
        f.payment_date
    FROM fees f
    JOIN students s ON f.student_id = s.id
    JOIN users u ON s.user_id = u.id
    ORDER BY f.payment_date DESC
");

if (!$fees) {
    die("SQL Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Fees Management | Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen">

<!-- NAVBAR -->
<div class="bg-white shadow px-8 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-indigo-600">💰 Fees Management</h1>
    <a href="admin_dashboard.php"
       class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        Dashboard
    </a>
</div>

<!-- MAIN -->
<div class="max-w-7xl mx-auto px-6 py-10">

<div class="bg-white rounded-3xl shadow-xl p-8">

<h2 class="text-xl font-bold mb-6 text-gray-800">
    Student Fee Records
</h2>

<div class="overflow-x-auto">
<table class="w-full border rounded-xl overflow-hidden">

<thead>
<tr class="bg-indigo-600 text-white text-left">
    <th class="p-3">Student</th>
    <th class="p-3">Amount</th>
    <th class="p-3">Status</th>
    <th class="p-3">Payment ID</th>
    <th class="p-3">Date</th>
</tr>
</thead>

<tbody>
<?php if(mysqli_num_rows($fees) > 0): ?>
<?php while($row = mysqli_fetch_assoc($fees)): ?>
<tr class="border-b hover:bg-gray-50">
    <td class="p-3 font-medium">
        <?= htmlspecialchars($row['student_name']) ?>
    </td>

    <td class="p-3 font-semibold">
        ₹ <?= number_format($row['amount']) ?>
    </td>

    <td class="p-3">
        <?php if($row['status'] === 'PAID'): ?>
            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">
                PAID
            </span>
        <?php else: ?>
            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">
                PENDING
            </span>
        <?php endif; ?>
    </td>

    <td class="p-3 text-sm text-gray-600">
        <?= $row['razorpay_payment_id'] ?: '-' ?>
    </td>

    <td class="p-3 text-sm">
        <?= $row['payment_date'] 
            ? date("d M Y, h:i A", strtotime($row['payment_date'])) 
            : '-' ?>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="5" class="p-6 text-center text-gray-500">
        No fee records found.
    </td>
</tr>
<?php endif; ?>
</tbody>

</table>
</div>

</div>
</div>

<footer class="text-center text-gray-500 text-sm py-6">
    © <?= date('Y') ?> School Management System
</footer>

</body>
</html>
