<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// ADMIN protection
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../index.php");
    exit;
}

// success flag
$success = isset($_GET['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Teacher Registration</title>

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen flex items-center justify-center">

<div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-10 text-center">

<?php if ($success): ?>

    <!-- SUCCESS SCREEN -->
    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-green-100 flex items-center justify-center">
        <i class="fas fa-check text-4xl text-green-600"></i>
    </div>

    <h2 class="text-3xl font-bold text-gray-800 mb-2">
        Teacher Registered!
    </h2>

    <p class="text-gray-600 mb-6">
        The teacher account has been successfully created.
    </p>

    <div class="flex gap-4 justify-center">
        <a href="register_teacher.php"
           class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
            Register Another
        </a>

        <a href="admin_dashboard.php"
           class="px-6 py-3 bg-gray-200 text-gray-800 rounded-xl hover:bg-gray-300 transition">
            Dashboard
        </a>
    </div>

<?php else: ?>

    <!-- DEFAULT SCREEN -->
    <h2 class="text-2xl font-bold text-gray-800 mb-4">
        Teacher Registration
    </h2>

    <p class="text-gray-600 mb-6">
        Use the registration form to add new teachers.
    </p>

    <a href="register_teacher_form.php"
       class="px-8 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
        Open Registration Form
    </a>

<?php endif; ?>

</div>

</body>
</html>
