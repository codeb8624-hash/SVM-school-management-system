<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection
$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Handle POST (AJAX login)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        echo json_encode([
            "status" => "error",
            "message" => "Username or Password missing"
        ]);
        exit;
    }

    // Secure query: fetch by email, verify password below
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, name, role, password FROM users 
         WHERE email = ? AND status = 1"
    );
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {

        $user = mysqli_fetch_assoc($result);

        // bcrypt hash (password_hash) or legacy plaintext
        $valid = password_verify($password, $user['password'])
              || hash_equals($user['password'], $password);

        if (!$valid) {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid Username or Password"
            ]);
            exit;
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['name']    = $user['name'];

        // Role-based redirect (IMPORTANT)
        switch ($user['role']) {
            case 'ADMIN':
                $redirect = "../admin/admin_dashboard.php";
                break;

            case 'TEACHER':
                $redirect = "../teacher/teacher_dashboard.php";
                break;

            case 'STUDENT':
                $redirect = "../student/student_dashboard.php";
                break;

            case 'PARENT':
                $redirect = "../parent/parent_dashboard.php";
                break;

            default:
                session_destroy();
                $redirect = "admin_login.php";
        }

        echo json_encode([
            "status"   => "success",
            "redirect" => $redirect
        ]);
        exit;

    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid Username or Password"
        ]);
        exit;
    }
}
?>
