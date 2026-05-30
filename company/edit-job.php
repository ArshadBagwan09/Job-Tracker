<?php
session_start();
require_once "../Backend/DBConnection.php";

use Backend\DBConnection;

$conn = DBConnection::getConnection();

if (!$conn) {
    die("Database Connection Failed");
}

// 🔒 Company Login Check
if (!isset($_SESSION['company_id'])) {
    header("Location: ../login.php");
    exit;
}

// 🔎 Job ID Check
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manageJobs.php");
    exit;
}

$job_id = intval($_GET['id']);
$company_id = $_SESSION['company_id'];

// 🔎 Fetch job (only if belongs to logged-in company)
$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ? AND company_id = ?");
$stmt->execute([$job_id, $company_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    die("Job not found or unauthorized access.");
}

// 📝 Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $job_title  = trim($_POST['job_title']);
    $location   = trim($_POST['location']);
    $job_type   = trim($_POST['job_type']);
    $salary     = trim($_POST['salary']);
    $experience = trim($_POST['experience']);
    $description= trim($_POST['description']);
    $last_date  = trim($_POST['last_date']);

    $update = $conn->prepare("
        UPDATE jobs SET 
            job_title = ?, 
            location = ?, 
            job_type = ?, 
            salary = ?, 
            experience = ?, 
            description = ?, 
            last_date = ?
        WHERE id = ? AND company_id = ?
    ");

    $update->execute([
        $job_title,
        $location,
        $job_type,
        $salary,
        $experience,
        $description,
        $last_date,
        $job_id,
        $company_id
    ]);

    header("Location: manageJobs.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Job</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *{
            box-sizing:border-box;
            font-family:'Poppins',sans-serif;
        }

        body{
            background:linear-gradient(135deg,#eef3ff,#f8fbff);
            padding:40px;
        }

        .form-box{
            background:white;
            padding:35px;
            max-width:650px;
            margin:auto;
            border-radius:18px;
            box-shadow:0 15px 35px rgba(13,110,253,0.12);
            animation:fadeIn 0.4s ease-in-out;
        }

        @keyframes fadeIn{
            from{opacity:0; transform:translateY(15px);}
            to{opacity:1; transform:translateY(0);}
        }

        h2{
            margin-bottom:25px;
            color:#0d6efd;
            text-align:center;
        }

        .input-group{
            position:relative;
            margin-bottom:22px;
        }

        .input-group input,
        .input-group textarea{
            width:100%;
            padding:14px 12px;
            border-radius:10px;
            border:1px solid #ccc;
            outline:none;
            font-size:14px;
            background:#f9fbff;
            transition:0.3s;
        }

        .input-group label{
            position:absolute;
            left:12px;
            top:14px;
            font-size:14px;
            color:#777;
            pointer-events:none;
            transition:0.3s ease;
            background:white;
            padding:0 5px;
        }

        .input-group input:focus,
        .input-group textarea:focus{
            border-color:#0d6efd;
            background:white;
            box-shadow:0 0 0 3px rgba(13,110,253,0.1);
        }

        .input-group input:focus + label,
        .input-group textarea:focus + label,
        .input-group input:not(:placeholder-shown) + label,
        .input-group textarea:not(:placeholder-shown) + label{
            top:-8px;
            font-size:12px;
            color:#0d6efd;
        }

        textarea{
            resize:none;
        }

        .btn-group{
            display:flex;
            justify-content:space-between;
            margin-top:20px;
        }

        button{
            padding:10px 20px;
            border:none;
            border-radius:8px;
            cursor:pointer;
            font-weight:500;
            transition:0.3s;
        }

        .update-btn{
            background:#0d6efd;
            color:white;
        }

        .update-btn:hover{
            background:#0b5ed7;
            transform:translateY(-2px);
        }

        .back-btn{
            background:#e2e6ea;
        }

        .back-btn:hover{
            background:#ced4da;
        }

        @media(max-width:600px){
            .btn-group{
                flex-direction:column;
                gap:10px;
            }
        }
    </style>
</head>
<body>

<div class="form-box">
    <h2>Edit Job Details</h2>

    <form method="POST">

        <div class="input-group">
            <input type="text" name="job_title" value="<?php echo htmlspecialchars($job['job_title']); ?>" placeholder=" " required>
            <label>Job Title</label>
        </div>

        <div class="input-group">
            <input type="text" name="location" value="<?php echo htmlspecialchars($job['location']); ?>" placeholder=" " required>
            <label>Location</label>
        </div>

        <div class="input-group">
            <input type="text" name="job_type" value="<?php echo htmlspecialchars($job['job_type']); ?>" placeholder=" " required>
            <label>Job Type</label>
        </div>

        <div class="input-group">
            <input type="text" name="salary" value="<?php echo htmlspecialchars($job['salary']); ?>" placeholder=" ">
            <label>Salary</label>
        </div>

        <div class="input-group">
            <input type="text" name="experience" value="<?php echo htmlspecialchars($job['experience']); ?>" placeholder=" ">
            <label>Experience Required</label>
        </div>

        <div class="input-group">
            <textarea name="description" rows="4" placeholder=" " required><?php echo htmlspecialchars($job['description']); ?></textarea>
            <label>Job Description</label>
        </div>

        <div class="input-group">
            <input type="date" name="last_date" value="<?php echo htmlspecialchars($job['last_date']); ?>" placeholder=" ">
            <label>Last Date to Apply</label>
        </div>

        <div class="btn-group">
            <button type="button" class="back-btn" onclick="window.location.href='manageJobs.php'">
                Back
            </button>

            <button type="submit" class="update-btn">
                Update Job
            </button>
        </div>

    </form>
</div>

</body>
</html>