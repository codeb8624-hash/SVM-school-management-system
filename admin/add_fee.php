<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') exit;

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);

if($_SERVER['REQUEST_METHOD']=="POST"){
    $student_id = $_POST['student_id'];
    $amount     = $_POST['amount'];
    $due_date   = $_POST['due_date'];

    mysqli_query($conn,"
        INSERT INTO fees (student_id, amount, due_date)
        VALUES ('$student_id','$amount','$due_date')
    ");

    // 🔔 Notification
    mysqli_query($conn,"
        INSERT INTO notifications (user_id, message)
        SELECT p.user_id, CONCAT('New fee added: ₹','$amount')
        FROM parents p WHERE p.student_id='$student_id'
    ");

    $msg="Fee added successfully";
}

$students=mysqli_query($conn,"
    SELECT s.id,u.name FROM students s
    JOIN users u ON s.user_id=u.id
");
?>
