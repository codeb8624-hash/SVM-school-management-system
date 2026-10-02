<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'PARENT') {
    header("Location: ../index.php");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if(!$conn) die("DB Connection Failed");

$q = mysqli_query($conn,"
    SELECT f.amount, f.due_date, f.status
    FROM fees f
    JOIN parents p ON f.student_id = p.student_id
    WHERE p.user_id = '{$_SESSION['user_id']}'
");

if(!$q){
    die("SQL Error: ".mysqli_error($conn));
}
?>

<table class="w-full border">
<tr class="bg-indigo-600 text-white">
    <th class="p-2">Amount</th>
    <th class="p-2">Due Date</th>
    <th class="p-2">Status</th>
</tr>

<?php while($f = mysqli_fetch_assoc($q)): ?>
<tr class="border-b">
    <td class="p-2">₹<?= number_format($f['amount']) ?></td>
    <td class="p-2"><?= $f['due_date'] ?: '-' ?></td>
    <td class="p-2 font-bold <?= $f['status']=='PAID'?'text-green-600':'text-red-600' ?>">
        <?= $f['status'] ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
