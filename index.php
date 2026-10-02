<?php
session_start();

// Database connection
$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// If form submitted using fetch (AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query from users table (ADMIN only)
    $query = "SELECT * FROM users 
              WHERE email='$username' 
              AND password='$password' 
              AND role='ADMIN'";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $_SESSION['admin'] = $username;

        echo json_encode([
            "status" => "success",
            "redirect" => "admin/admin_dashboard.php"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid Username or Password!"
        ]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | School Management</title>

    <style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #4facfe, #00f2fe);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.98); }
        to { opacity: 1; transform: scale(1); }
    }

    .login-container {
        width: 800px;
        height: 420px;
        background: #fff;
        display: flex;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 25px 50px rgba(0,0,0,0.25);
        transition: 0.4s;
    }

    .login-container:hover {
        transform: translateY(-4px);
        box-shadow: 0 35px 60px rgba(0,0,0,0.35);
    }

    .login-left {
        width: 40%;
        background: linear-gradient(135deg, #0d6efd, #0047ab);
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 20px;
    }

    .login-left img {
        width: 110px;
        margin-bottom: 15px;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    .login-left h2 {
        font-size: 20px;
        font-weight: 600;
        line-height: 1.4;
    }

    .login-right {
        width: 60%;
        padding: 55px;
    }

    .login-right h2 {
        font-size: 26px;
        margin-bottom: 25px;
        color: #333;
    }

    .form-group {
        margin-bottom: 22px;
    }

    .form-group input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
        transition: 0.3s;
    }

    .form-group input:focus {
        outline: none;
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13,110,253,0.2);
    }

    .login-btn {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #0d6efd, #0047ab);
        border: none;
        color: white;
        font-size: 16px;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.3s;
    }

    .login-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(13,110,253,0.4);
    }

    .login-btn:active {
        transform: scale(0.97);
    }

    #message-box {
        display: none;
        padding: 12px;
        margin-bottom: 18px;
        border-radius: 8px;
        text-align: center;
        font-size: 14px;
        animation: fadeIn 0.4s ease-in-out;
    }

    .error-msg {
        background: #f8d7da;
        color: #721c24;
    }

    .success-msg {
        background: #d4edda;
        color: #155724;
    }
</style>

</head>

<body>

<div class="login-container">
    <div class="login-left">
        <img src="assets/images.jpg" style="width:100px;">
        <h2>Sharda Vidhya Mandir Sancul</h2>
    </div>

    <div class="login-right">
        <h2> Login</h2>

        <div id="message-box"></div>

        <form id="loginForm">
            <div class="form-group">
                <input type="text" name="username" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const msgBox = document.getElementById('message-box');
    const formData = new FormData(this);

    msgBox.style.display = 'block';
    msgBox.className = 'success-msg';
    msgBox.innerHTML = 'Checking credentials...';

    fetch('auth/admin_login.php', {   // SAME FILE
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            msgBox.className = 'success-msg';
            msgBox.innerHTML = 'Login successful!';
            setTimeout(() => window.location.href = data.redirect, 1000);
        } else {
            msgBox.className = 'error-msg';
            msgBox.innerHTML = data.message;
        }
    })
    .catch(() => {
        msgBox.className = 'error-msg';
        msgBox.innerHTML = 'Server error';
    });
});
</script>

</body>
</html>
