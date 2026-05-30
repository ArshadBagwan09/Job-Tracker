<?php
session_start();
require_once "../Backend/DBConnection.php";
use Backend\DBConnection;

$conn = DBConnection::getConnection();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$app_id = $_GET['id'];

// FETCH DATA
$stmt = $conn->prepare("
    SELECT * FROM job_applications 
    WHERE id = ? AND user_id = ?
");
$stmt->execute([$app_id, $user_id]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$app) {
    die("Application not found");
}

// ❌ BLOCK if not pending
if (strtolower($app['status']) !== 'pending') {
    echo "<script>alert('You cannot edit after application is processed'); window.location.href='applications.php';</script>";
    exit;
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $qualification = $_POST['qualification'];
    $experience = $_POST['experience'];
    $cover = $_POST['cover_letter'];

    // Resume update (optional)
    if (!empty($_FILES['resume']['name'])) {
        $resume = $_FILES['resume']['name'];
        $tmp = $_FILES['resume']['tmp_name'];

        $path = "../uploads/" . time() . "_" . $resume;
        move_uploaded_file($tmp, $path);
    } else {
        $path = $app['resume'];
    }

    $update = $conn->prepare("
        UPDATE job_applications 
        SET name=?, email=?, phone=?, qualification=?, experience=?, cover_letter=?, resume=? 
        WHERE id=? AND user_id=?
    ");

    $update->execute([
        $name,
        $email,
        $phone,
        $qualification,
        $experience,
        $cover,
        $path,
        $app_id,
        $user_id
    ]);

    echo "<script>alert('Application Updated Successfully'); window.location.href='applications.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Application</title>

<link rel="stylesheet" href="../Style/navBarStyle.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{
    background: linear-gradient(135deg,#eef2ff,#f8fbff);
    font-family:'Poppins',sans-serif;
}

/* Header */
.header{
    background: linear-gradient(135deg,#0d6efd,#6610f2);
    color:white;
    padding:50px 20px;
    text-align:center;
    border-radius:0 0 40px 40px;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
}

.header h1{
    font-size:32px;
    letter-spacing:0.5px;
}

.header p{
    opacity:0.9;
    margin-top:5px;
}

/* Container */
.container{
    max-width:750px;
    margin:50px auto;
    padding:35px;

    background: rgba(255,255,255,0.75);
    backdrop-filter: blur(12px);

    border-radius:25px;
    box-shadow:0 15px 50px rgba(0,0,0,0.08);
    transition:0.3s;
}

.container:hover{
    transform:translateY(-3px);
}

/* Labels */
label{
    font-weight:600;
    margin-bottom:6px;
    display:block;
    color:#333;
}

/* Inputs */
input,select,textarea{
    width:100%;
    padding:12px 14px;
    margin-bottom:18px;

    border-radius:12px;
    border:1px solid #e0e0e0;

    font-size:14px;
    transition:0.25s;
    background:#fff;
}

/* Focus effect */
input:focus,select:focus,textarea:focus{
    border-color:#6610f2;
    outline:none;
    box-shadow:0 0 0 3px rgba(102,16,242,0.15);
}

/* Readonly */
input[readonly]{
    background:#eef2ff;
    color:#0d6efd;
    font-weight:600;
}

/* File Upload */
input[type="file"]{
    padding:10px;
    border:2px dashed #cbd5e1;
    background:#f9fbff;
    cursor:pointer;
}

input[type="file"]:hover{
    border-color:#6610f2;
    background:#f3f0ff;
}

/* Button */
button{
    width:100%;
    padding:15px;

    background: linear-gradient(135deg,#0d6efd,#6610f2);
    color:white;

    border:none;
    border-radius:30px;

    font-size:16px;
    font-weight:600;
    letter-spacing:0.5px;

    cursor:pointer;
    transition:0.3s;
}

button:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(0,0,0,0.15);
}

/* Small Animation */
@keyframes fadeIn {
    from{
        opacity:0;
        transform:translateY(10px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

.container{
    animation:fadeIn 0.5s ease;
}
</style>
</head>

<body>

<?php include "../navBar.html"; ?>

<div class="header">
    <h1><?= htmlspecialchars($app['company_name']) ?></h1>
    <p>Edit Your Application</p>
</div>

<div class="container">

<form method="POST" enctype="multipart/form-data">

    <label>Job Title</label>
    <input type="text" value="<?= htmlspecialchars($app['position']) ?>" readonly>

    <label>Full Name</label>
    <input type="text" name="fullname" 
        value="<?= htmlspecialchars($app['name']) ?>" required>

    <label>Email</label>
    <input type="email" name="email" 
        value="<?= htmlspecialchars($app['email']) ?>" required>

    <label>Phone</label>
    <input type="text" name="phone" 
        value="<?= htmlspecialchars($app['phone']) ?>" required>

    <label>Qualification</label>
    <select name="qualification" required>
        <option <?= $app['qualification']=='BCA'?'selected':'' ?>>BCA</option>
        <option <?= $app['qualification']=='MCA'?'selected':'' ?>>MCA</option>
        <option <?= $app['qualification']=='B.Tech'?'selected':'' ?>>B.Tech</option>
        <option <?= $app['qualification']=='M.Tech'?'selected':'' ?>>M.Tech</option>
        <option <?= $app['qualification']=='Other'?'selected':'' ?>>Other</option>
    </select>

    <label>Experience</label>
    <select name="experience">
        <option <?= $app['experience']=='Fresher'?'selected':'' ?>>Fresher</option>
        <option <?= $app['experience']=='1 Year'?'selected':'' ?>>1 Year</option>
        <option <?= $app['experience']=='2 Years'?'selected':'' ?>>2 Years</option>
        <option <?= $app['experience']=='3+ Years'?'selected':'' ?>>3+ Years</option>
    </select>

    <label>Cover Letter</label>
    <textarea name="cover_letter" rows="4" required><?= htmlspecialchars($app['cover_letter']) ?></textarea>

    <label>Update Resume</label>
    <p>Current Resume: <a href="<?= $app['resume'] ?>" target="_blank">View</a></p>
    <input type="file" name="resume">

    <button type="submit">
        <i class="fa fa-save"></i> Update Application
    </button>

</form>

</div>

</body>
</html>