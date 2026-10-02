<?php
session_start();

/* 🔐 ADMIN ACCESS ONLY */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register Teacher | School Management</title>

<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
.step { display:none; }
.step.active { display:block; animation: fadeIn .3s ease; }

@keyframes fadeIn {
    from { opacity:0; transform:translateY(10px); }
    to { opacity:1; transform:translateY(0); }
}

.step-circle.active,
.step-circle.done {
    background:#4f46e5;
    color:white;
    border-color:#4f46e5;
}
</style>
</head>

<body class="bg-gradient-to-br from-indigo-100 to-blue-200 min-h-screen flex items-center justify-center p-6">

<div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row">

<!-- LEFT PANEL -->
<div class="hidden md:flex md:w-1/3 bg-indigo-600 p-10 text-white flex-col justify-between">
    <div>
        <h1 class="text-4xl font-bold mb-4">Teacher Registration</h1>
        <p class="text-indigo-200 text-sm">
            Admin-only secure teacher onboarding.
        </p>
    </div>
    <div class="text-xs text-indigo-200">
        © School Management System
    </div>
</div>

<!-- FORM PANEL -->
<div class="flex-1 p-8 md:p-12">
<div class="max-w-md mx-auto">

<!-- PROGRESS -->
<div class="mb-8">
    <div class="flex justify-between mb-2">
        <div class="step-circle w-10 h-10 rounded-full border-2 flex items-center justify-center font-bold active">1</div>
        <div class="step-circle w-10 h-10 rounded-full border-2 flex items-center justify-center font-bold">2</div>
        <div class="step-circle w-10 h-10 rounded-full border-2 flex items-center justify-center font-bold">3</div>
    </div>
    <div class="h-1 bg-gray-200 rounded-full">
        <div id="progress" class="h-1 bg-indigo-600 rounded-full transition-all" style="width:33%"></div>
    </div>
</div>

<form method="POST" action="save_user.php" id="regForm">

<!-- STEP 1 -->
<div class="step active">
    <h2 class="text-xl font-bold mb-4">Personal Details</h2>
    <input name="first_name" required placeholder="First Name"
           class="w-full mb-4 px-4 py-3 border rounded-xl">
    <input name="last_name" required placeholder="Last Name"
           class="w-full mb-4 px-4 py-3 border rounded-xl">
    <input name="email" type="email" required placeholder="Email Address"
           class="w-full mb-4 px-4 py-3 border rounded-xl">

    <!-- hidden role -->
    <input type="hidden" name="role" value="TEACHER">
</div>

<!-- STEP 2 -->
<div class="step">
    <h2 class="text-xl font-bold mb-4">Professional Details</h2>
    <select name="subject" class="w-full mb-4 px-4 py-3 border rounded-xl">
        <option>Mathematics</option>
        <option>Science</option>
        <option>Computer</option>
        <option>English</option>
    </select>
    <input name="institution" placeholder="Institution"
           class="w-full mb-4 px-4 py-3 border rounded-xl">
    <input name="experience" type="number" placeholder="Experience (Years)"
           class="w-full mb-4 px-4 py-3 border rounded-xl">
</div>

<!-- STEP 3 -->
<div class="step">
    <h2 class="text-xl font-bold mb-4">Security</h2>
    <input name="password" type="password" required placeholder="Password"
           class="w-full mb-4 px-4 py-3 border rounded-xl">
    <input name="confirm_password" type="password" required placeholder="Confirm Password"
           class="w-full mb-4 px-4 py-3 border rounded-xl">
</div>

<button type="submit" id="submitBtn" class="hidden"></button>
</form>

<!-- NAV BUTTONS -->
<div class="flex justify-between mt-8">
    <button id="prevBtn"
            class="px-6 py-2 rounded-lg text-gray-400 cursor-not-allowed">
        Back
    </button>

    <button id="nextBtn"
            class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition">
        Continue →
    </button>
</div>

</div>
</div>
</div>

<script>
let current = 0;
const steps = document.querySelectorAll(".step");
const circles = document.querySelectorAll(".step-circle");
const progress = document.getElementById("progress");
const prevBtn = document.getElementById("prevBtn");

function updateUI() {
    steps.forEach((s,i)=>s.classList.toggle("active", i===current));
    circles.forEach((c,i)=>{
        c.classList.toggle("active", i===current);
        if(i<current) c.classList.add("done");
    });
    progress.style.width = ((current+1)/steps.length)*100 + "%";
    prevBtn.classList.toggle("cursor-not-allowed", current===0);
    prevBtn.classList.toggle("text-gray-400", current===0);
}

document.getElementById("nextBtn").onclick = () => {
    if(current < steps.length-1){
        current++;
        updateUI();
    } else {
        document.getElementById("submitBtn").click();
    }
};

prevBtn.onclick = () => {
    if(current > 0){
        current--;
        updateUI();
    }
};
</script>

</body>
</html>
