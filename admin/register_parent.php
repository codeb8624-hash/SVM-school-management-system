<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../index.php");
    exit;
}

$conn = mysqli_connect("127.0.0.1", "root", "", "admin_panel", 3307);
if(!$conn){
    die("DB Connection Failed");
}

$msg = "";
$err = "";

/* Handle form submit */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);

    if (strtolower(substr($email, -10)) !== '@gmail.com') {
        $err = "Only @gmail.com email is allowed for Parent";
    } else {

        $name       = mysqli_real_escape_string($conn, $_POST['name']);
        $email      = mysqli_real_escape_string($conn, $email);
        $password   = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
        $phone      = mysqli_real_escape_string($conn, $_POST['phone']);
        $student_id = $_POST['student_id'];

        // Insert into users
        $user_q = mysqli_query($conn,"
            INSERT INTO users (name,email,password,role,status)
            VALUES ('$name','$email','$password','PARENT',1)
        ");

        if ($user_q) {
            $user_id = mysqli_insert_id($conn);

            // Insert into parents
            mysqli_query($conn,"
                INSERT INTO parents (user_id, student_id, phone)
                VALUES ('$user_id','$student_id','$phone')
            ");

            $msg = "Parent registered successfully!";
        } else {
            $msg = "Error registering parent.";
        }
    }
}

/* Fetch students */
$students = mysqli_query($conn,"
    SELECT s.id, u.name, s.class, s.batch
    FROM students s
    JOIN users u ON s.user_id = u.id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register Parent</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-100 to-blue-100 min-h-screen">

<!-- HEADER -->
<div class="bg-white shadow px-8 py-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-indigo-600">👨‍👩‍👧 Register Parent</h1>
    <a href="admin_dashboard.php"
       class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
       Dashboard
    </a>
</div>

<div class="max-w-4xl mx-auto px-6 py-10">

<div class="bg-white rounded-3xl shadow-xl p-8">

<h2 class="text-xl font-bold mb-6">Parent Registration Form</h2>

<?php if($err): ?>
<div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
    <?= htmlspecialchars($err) ?>
</div>
<?php endif; ?>

<?php if($msg): ?>
<div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
    <?= $msg ?>
</div>
<?php endif; ?>

<form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">

<!-- Parent Name -->
<div>
<label class="font-semibold">Parent Name</label>
<input type="text" name="name" required
class="w-full border p-2 rounded">
</div>

<!-- Email -->
<div>
<label class="font-semibold">Email</label>
<input type="email" name="email" required
pattern="[^@]+@gmail\.com" title="Only @gmail.com email allowed"
class="w-full border p-2 rounded">
</div>

<!-- Password -->
<div>
<label class="font-semibold">Password</label>
<input type="password" name="password" required
class="w-full border p-2 rounded">
</div>

<!-- Phone -->
<div>
<label class="font-semibold">Phone</label>
<input type="text" name="phone" required
class="w-full border p-2 rounded">
</div>

<!-- Student -->
<div class="md:col-span-2">
<label class="font-semibold">Select Student</label>
<select name="student_id" required
class="w-full border p-2 rounded">
<option value="">Select Student</option>
<?php while($s = mysqli_fetch_assoc($students)): ?>
<option value="<?= $s['id'] ?>">
<?= htmlspecialchars($s['name']) ?> (Class <?= $s['class'] ?> - Batch <?= $s['batch'] ?>)
</option>
<?php endwhile; ?>
</select>
</div>

<!-- Button -->
<div class="md:col-span-2 text-right">
<button class="px-8 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
➕ Register Parent
</button>
</div>

</form>

</div>
</div>

<footer class="text-center text-gray-500 py-6">
© <?= date('Y') ?> School Management System
</footer>

</body>
</html>
