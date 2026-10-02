<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../index.php");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if (!$conn) {
    die("DB Connection Failed");
}

$message = "";
if (isset($_GET['error']) && $_GET['error'] === 'gmail') {
    $message = "Only @gmail.com email is allowed for Student";
}

// FORM SUBMIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $class    = $_POST['class'];
    $batch    = $_POST['batch'];
    $roll_no  = $_POST['roll_no'];

    // BASIC VALIDATION
    if ($name && $email && $password && $class && $batch && $roll_no
        && strtolower(substr($email, -10)) === '@gmail.com') {
        $password = password_hash($password, PASSWORD_DEFAULT);

        // 1️⃣ Insert into users
        $userQuery = "INSERT INTO users (name, email, password, role, status)
                      VALUES ('$name', '$email', '$password', 'STUDENT', 1)";

        if (mysqli_query($conn, $userQuery)) {

            $user_id = mysqli_insert_id($conn);

            // 2️⃣ Insert into students
            $studentQuery = "INSERT INTO students (user_id, class, batch, roll_no)
                             VALUES ($user_id, '$class', '$batch', '$roll_no')";

            if (mysqli_query($conn, $studentQuery)) {
                $message = "success";
            } else {
                $message = "Student table error";
            }

        } else {
            $message = "User already exists or error";
        }
    } else if ($name && $email && $password && $class && $batch && $roll_no) {
        $message = "Only @gmail.com email is allowed for Student";
    } else {
        $message = "All fields required";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register Student | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen">

<!-- HEADER -->
<div class="bg-white shadow px-8 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-indigo-600">🎓 Student Registration</h1>
    <a href="admin_dashboard.php"
       class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        Dashboard
    </a>
</div>

<!-- FORM -->
<div class="max-w-3xl mx-auto px-6 py-10">
<div class="bg-white rounded-3xl shadow-xl p-8">

<h2 class="text-xl font-bold mb-6 text-gray-800">Register New Student</h2>

<?php if ($message === "success"): ?>
    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
        Student registered successfully ✅
    </div>
<?php elseif ($message): ?>
    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<form method="POST">

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

<!-- NAME -->
<div>
    <label class="font-semibold text-gray-700">Student Name</label>
    <input type="text" name="name" required
           class="w-full mt-1 px-4 py-2 border rounded-lg">
</div>

<!-- EMAIL -->
<div>
    <label class="font-semibold text-gray-700">Email</label>
    <input type="email" name="email" required
           pattern="[^@]+@gmail\.com" title="Only @gmail.com email allowed"
           class="w-full mt-1 px-4 py-2 border rounded-lg">
</div>

<!-- PASSWORD -->
<div>
    <label class="font-semibold text-gray-700">Password</label>
    <input type="text" name="password" required
           class="w-full mt-1 px-4 py-2 border rounded-lg">
</div>

<!-- ROLL -->
<div>
    <label class="font-semibold text-gray-700">Roll No</label>
    <input type="text" name="roll_no" required
           class="w-full mt-1 px-4 py-2 border rounded-lg">
</div>

<!-- CLASS -->
<div>
    <label class="font-semibold text-gray-700">Class</label>
    <select name="class" required
            class="w-full mt-1 px-4 py-2 border rounded-lg">
        <option value="">Select Class</option>
        <?php for ($i=1; $i<=12; $i++): ?>
            <option value="<?= $i ?>">Class <?= $i ?></option>
        <?php endfor; ?>
    </select>
</div>

<!-- BATCH -->
<div>
    <label class="font-semibold text-gray-700">Batch</label>
    <select name="batch" required
            class="w-full mt-1 px-4 py-2 border rounded-lg">
        <option value="">Select Batch</option>
        <option value="A">Batch A</option>
        <option value="B">Batch B</option>
        <option value="C">Batch C</option>
        <option value="D">Batch D</option>
    </select>
</div>

</div>

<!-- BUTTON -->
<div class="mt-8 text-right">
    <button type="submit"
            class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700">
        ➕ Register Student
    </button>
</div>

</form>

</div>
</div>

</body>
</html>
