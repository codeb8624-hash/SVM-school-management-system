<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'STUDENT') {
    header("Location: ../index.php");
    exit;
}
$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
$student_id = $_SESSION['user_id'];
?>
<table>
<?php
$q = mysqli_query($conn,"
SELECT date,status FROM attendance WHERE student_id='$student_id'
");
while($r=mysqli_fetch_assoc($q)){
    echo "<tr><td>{$r['date']}</td><td>{$r['status']}</td></tr>";
}
?>
</table>
