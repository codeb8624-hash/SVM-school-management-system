<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'TEACHER') {
    header("Location: ../index.php");
    exit;
}
$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: mark_attendance.php");
    exit;
}

$date  = $_POST['date']  ?? '';
$class = $_POST['class'] ?? '';
$batch = $_POST['batch'] ?? '';

foreach(($_POST['status'] ?? []) as $student_id => $status){
    mysqli_query($conn,"
        INSERT INTO attendance (student_id,class,batch,date,status)
        VALUES ('$student_id','$class','$batch','$date','$status')
    ");
}

header("Location: mark_attendance.php?success=1");
