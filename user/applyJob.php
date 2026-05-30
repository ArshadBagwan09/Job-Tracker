<?php
session_start();
require_once "../Backend/DBConnection.php";
use Backend\DBConnection;

$conn = DBConnection::getConnection();


if(!isset($_GET['id'])){
    die("Invalid Request");
}

$job_id = $_GET['id'];
$job_title = $_GET['job'] ?? '';
$company_name = $_GET['company'] ?? '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $job_id = $_POST['job_id'];
    $job_title = $_POST['job_title'];
    $name = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $qualification = $_POST['qualification'];
    $experience = $_POST['experience'];
    $cover = $_POST['cover_letter'];

    // Resume Upload
    $resume = $_FILES['resume']['name'];
    $tmp = $_FILES['resume']['tmp_name'];

    $path = "../uploads/" . time() . "_" . $resume;
    move_uploaded_file($tmp, $path);

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO job_applications 
(job_id, user_id, name, email, phone, qualification, company_name, position, experience, cover_letter, resume, applied_date) 
VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");

$stmt->execute([
    $job_id,
    $_SESSION['user_id'],
    $name,
    $email,
    $phone,
    $qualification,
    $company_name,
    $job_title,
    $experience,
    $cover,
    $path,
    date("Y-m-d")
]);

    echo "<script>alert('Application Submitted Successfully'); window.location.href='jobs.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Apply for Job - Company</title>
<link rel="stylesheet" href="../Style/navBarStyle.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background: linear-gradient(135deg, #eef2ff, #f8fbff);
}

/* Header */
.header{
    background: linear-gradient(135deg, #0d6efd, #6610f2);
    color:white;
    padding:40px 20px;
    text-align:center;
    border-bottom-left-radius:30px;
    border-bottom-right-radius:30px;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
}

.header h1{
    font-size:30px;
    margin-bottom:5px;
}

.header p{
    opacity:0.9;
}

/* Form Container */
.container{
    max-width:750px;
    margin:40px auto;
    padding:35px;

    background: rgba(255,255,255,0.8);
    backdrop-filter: blur(10px);

    border-radius:20px;
    box-shadow:0 10px 40px rgba(0,0,0,0.08);
}

.container h2{
    text-align:center;
    color:#0d6efd;
    margin-bottom:25px;
}

/* Input Groups */
.input-group{
    margin-bottom:20px;
    position: relative
}

input[readonly]{
    background:#f1f5ff;
    font-weight:600;
    color:#0d6efd;
}

label{
    display:block;
    font-weight:600;
    margin-bottom:6px;
}

input, select, textarea{
    width:100%;
    padding:12px 14px;
    border:1px solid #e0e0e0;
    border-radius:10px;
    font-size:14px;
    transition:0.3s;
}

input:focus, textarea:focus, select:focus{
    border-color:#0d6efd;
    outline:none;
    box-shadow:0 0 0 3px rgba(102,16,242,0.15);
}

/* File Upload */
.file-upload{
    border:2px dashed #cbd5e1;
    padding:20px;
    text-align:center;
    border-radius:12px;
    background:#f9fbff;
    transition:0.3s;
    margin-bottom: 20px;
}

.file-upload:hover{
    border-color:#6610f2;
    background:#f3f0ff;
}

.file-upload input{
    border:1px solid #ccc;
    padding:10px;
    border-radius:8px;
}

/* Button */
.submit-btn{
    width:100%;
    padding:14px;
    background: linear-gradient(135deg, #0d6efd, #6610f2);
    color:white;
    border:none;
    border-radius:30px;
    font-size:16px;
    cursor:pointer;
    transition:0.3s;
    font-weight:600;
    letter-spacing:0.5px;
}

.submit-btn:hover{
    transform: translateY(-2px);
    box-shadow:0 8px 20px rgba(0,0,0,0.15);
}

/* Footer */
.footer{
    text-align:center;
    padding:20px;
    margin-top:40px;
    background:#fff;
    box-shadow:0 -2px 10px rgba(0,0,0,0.05);
}

/* Responsive */
@media(max-width:768px){
    .container{
        margin:20px;
        padding:25px;
    }
}
</style>
</head>
<body>
    <!-- Navbar -->
    <?php include "../navBar.html"; ?>

<div class="header">
    <h1><?= htmlspecialchars($company_name) ?></h1>
    <p>Apply for Open Position</p>
</div>

<div class="container">
    <div style="text-align:center;margin-bottom:20px;">
    <i class="fa fa-briefcase" style="font-size:35px;color:#6610f2;"></i>
</div>
    <h2>Job Application Form</h2>

    <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="job_id" value="<?= $job_id ?>">
<input type="hidden" name="company_name" value="<?= $company_name ?>">

        <div class="input-group">
            <label>Job Title</label>
           <input type="text" name="job_title" id="jobTitle" 
value="<?= htmlspecialchars($job_title) ?>" readonly>
        </div>

        <div class="input-group">
            <label>Full Name</label>
            <input type="text" name="fullname" required>
        </div>

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="input-group">
            <label>Phone</label>
            <input type="tel" name="phone" required>
        </div>

        <div class="input-group">
            <label>Highest Qualification</label>
            <select name="qualification" required>
                <option value="">Select</option>
                <option>BCA</option>
                <option>MCA</option>
                <option>B.Tech</option>
                <option>M.Tech</option>
                <option>Other</option>
            </select>
        </div>

        <div class="input-group">
            <label>Experience</label>
            <select name="experience" required>
                <option value="">Select</option>
                <option>Fresher</option>
                <option>1 Year</option>
                <option>2 Years</option>
                <option>3+ Years</option>
            </select>
        </div>

        <div class="input-group">
            <label>Cover Letter</label>
            <textarea name="cover_letter" rows="4" required></textarea>
        </div>

        <div class="file-upload">
            <label><i class="fa fa-upload"></i> Upload Resume (PDF/DOC)</label>
            <input type="file" name="resume" accept=".pdf,.doc,.docx" required>
        </div>

        <button type="submit" class="submit-btn">
            <i class="fa fa-paper-plane"></i> Submit Application
        </button>

    </form>
</div>

<div class="footer">
    © 2026 TechNova Pvt Ltd | Powered by WorkNexa
</div>

</body>
</html>