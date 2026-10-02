<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    exit("Unauthorized");
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register_student.php");
    exit;
}
if (strtolower(substr(trim($_POST['email']), -10)) !== '@gmail.com') {
    header("Location: register_student.php?error=gmail");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if(!$conn) die("DB Error");

$name     = mysqli_real_escape_string($conn,$_POST['name']);
$email    = mysqli_real_escape_string($conn,$_POST['email']);
$phone    = mysqli_real_escape_string($conn,$_POST['phone']);
$class    = mysqli_real_escape_string($conn,$_POST['class']);
$roll_no  = mysqli_real_escape_string($conn,$_POST['roll_no']);
$password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

// 1️⃣ Insert into users
$u = mysqli_query($conn,"
    INSERT INTO users (role,name,email,phone,password,status)
    VALUES ('STUDENT','$name','$email','$phone','$password',1)
");

if(!$u){
    die("User Insert Failed: ".mysqli_error($conn));
}

$user_id = mysqli_insert_id($conn);

// 2️⃣ Insert into students
$s = mysqli_query($conn,"
    INSERT INTO students (user_id,class,roll_no)
    VALUES ('$user_id','$class','$roll_no')
");

if(!$s){
    die("Student Insert Failed: ".mysqli_error($conn));
}

header("Location: register_student.php?success=1");
exit;
?>
