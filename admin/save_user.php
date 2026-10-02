<?php
session_start();

// Only ADMIN can add users
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'ADMIN') {
    header("Location: ../index.php");
    exit;
}

// DB connection
$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if (!$conn) {
    die("DB Connection Failed");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // -------- USERS TABLE DATA --------
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name  = mysqli_real_escape_string($conn, $_POST['last_name']);
    $name       = $first_name . " " . $last_name;
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $password   = trim($_POST['password']);
    $confirm    = $_POST['confirm_password'];

    // -------- TEACHERS TABLE DATA --------
    $subject     = mysqli_real_escape_string($conn, $_POST['subject']);
    $institution = mysqli_real_escape_string($conn, $_POST['institution']);
    $experience  = mysqli_real_escape_string($conn, $_POST['experience']);

    if ($password !== $confirm) {
        die("Passwords do not match");
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    // 1️⃣ INSERT INTO USERS TABLE
    $user_sql = "INSERT INTO users (role, name, email, password, status)
                 VALUES ('TEACHER', '$name', '$email', '$password', 1)";

    if (!mysqli_query($conn, $user_sql)) {
        die("User insert error: " . mysqli_error($conn));
    }

    // Get inserted user id
    $user_id = mysqli_insert_id($conn);

    // 2️⃣ INSERT INTO TEACHERS TABLE
    $teacher_sql = "INSERT INTO teachers (user_id, subject, institution, experience)
                    VALUES ('$user_id', '$subject', '$institution', '$experience')";

    if (!mysqli_query($conn, $teacher_sql)) {
        die("Teacher insert error: " . mysqli_error($conn));
    }

    // SUCCESS
    header("Location: register_teacher.php?success=1");
    exit;
}
?>
