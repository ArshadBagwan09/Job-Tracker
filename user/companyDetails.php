<?php

require_once "../Backend/DBConnection.php";
use Backend\DBConnection;

$conn = DBConnection::getConnection();

if (!$conn) {
    die("Database connection failed");
}

// Recently Updated Jobs (Latest 3)
$recentStmt = $conn->prepare("SELECT * FROM jobs ORDER BY created_at DESC LIMIT 3");
$recentStmt->execute();
$recentJobs = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

// All Jobs (Latest 6)
$allStmt = $conn->prepare("SELECT * FROM jobs ORDER BY created_at DESC LIMIT 6");
$allStmt->execute();
$allJobs = $allStmt->fetchAll(PDO::FETCH_ASSOC);


$companyStmt = $conn->prepare("
    SELECT company_name, COUNT(*) as total_jobs 
    FROM jobs 
    GROUP BY company_name 
    ORDER BY total_jobs DESC 
    LIMIT 6
");
$companyStmt->execute();
$companies = $companyStmt->fetchAll(PDO::FETCH_ASSOC);

$categoryStmt = $conn->prepare("
    SELECT job_type, COUNT(*) as total 
    FROM jobs 
    GROUP BY job_type
");
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareerHub - Jobs & Companies</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../Style/navBarStyle.css">
    <link rel="stylesheet" href="../Style/footerStyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: linear-gradient(135deg, #eef2ff, #f8fbff);
            color: #333;
        }

        /* Header */
        .page-header{
    text-align:center;
    padding:60px 20px;
    background: linear-gradient(135deg,#0d6efd,#6610f2);
    color:white;
    border-bottom-left-radius:40px;
    border-bottom-right-radius:40px;
}

        .page-header h1{
    font-size:36px;
    font-weight:600;
}

.page-header p{
    opacity:0.9;
    margin-top:8px;
}

        /* Sections */
        .section {
            padding: 50px 60px;
        }

        .section-title{
    font-size:26px;
    margin-bottom:25px;
    font-weight:600;
    color:#0d6efd;
}

        /* Grid */
        .grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:25px;
}

        /* Cards */
        .card{
    background: rgba(255,255,255,0.8);
    backdrop-filter: blur(10px);
    padding:25px;
    border-radius:18px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
    transition:0.3s;
    position:relative;
    overflow:hidden;
}

        .card:hover{
    transform:translateY(-8px) scale(1.02);
    box-shadow:0 15px 40px rgba(0,0,0,0.12);
}

.card::after{
    content:"";
    position:absolute;
    width:100%;
    height:4px;
    background:linear-gradient(135deg,#0d6efd,#6610f2);
    bottom:0;
    left:0;
}



        .card i {
            font-size: 35px;
            color: #0077ff;
            margin-bottom: 10px;
        }

        .card h3{
    font-size:18px;
    margin-bottom:6px;
}

        .card p{
    font-size:13px;
    color:#666;
}

        .card button{
    margin-top:15px;
    padding:8px 18px;
    border:none;
    background:linear-gradient(135deg,#0d6efd,#6610f2);
    color:#fff;
    border-radius:25px;
    cursor:pointer;
    font-size:13px;
    transition:0.3s;
}

        .card button:hover{
    transform:scale(1.05);
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}

        /* View All Button */
        .view-all{
    text-align:center;
    margin-top:30px;
}

.view-all a{
    padding:10px 30px;
    background:linear-gradient(135deg,#0d6efd,#6610f2);
    color:white;
    border-radius:30px;
    text-decoration:none;
    transition:0.3s;
}

.view-all a:hover{
    transform:translateY(-2px);
}

        .company-logo{
    width:60px;
    height:60px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
    font-weight:600;
    color:white;
    margin-bottom:12px;
    background:linear-gradient(135deg,#0d6efd,#6610f2);
}

 .card i{
    font-size:30px;
    color:#6610f2;
    margin-bottom:10px;
}

        /* Responsive */
        @media(max-width:900px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width:768px){
    .section{
        padding:30px 20px;
    }

    .page-header h1{
        font-size:26px;
    }
}

        @media(max-width:600px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .navbar {
                flex-direction: column;
                gap: 10px;
                padding: 15px 20px;
            }
        }

        /* POPUP */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-content {
            background: #fff;
            width: 90%;
            max-width: 500px;
            padding: 25px;
            border-radius: 12px;
            position: relative;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 22px;
            cursor: pointer;
        }

        .popup-desc {
            margin: 15px 0;
            color: #555;
            line-height: 1.6;
            max-height: 150px;
            overflow: auto;
        }

        /* Apply Button */
        .apply-now-btn {
            background: #0077ff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            margin-bottom: 15px;
        }

        .apply-now-btn:hover {
            background: #005fd1;
        }

        /* Form */
        #applyForm input,
        #applyForm textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        #applyForm button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        #applyForm button:hover {
            background: #218838;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <?php include "../navBar.html"; ?>

    <!-- Header -->
    <div class="page-header">
        <h1>Find Your Dream Job</h1>
        <p>Latest openings from top companies</p>
    </div>

    <!-- Recently Updated Jobs -->
    <section class="section">
        <h2 class="section-title">Recently Updated Jobs</h2>
        <div class="grid">

            <?php foreach ($recentJobs as $job): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($job['job_title']) ?></h3>
<p><?= htmlspecialchars($job['company_name']) ?> • <?= htmlspecialchars($job['location']) ?></p>

                    <button onclick="window.location.href='companyProfile.php?id=<?= $job['id'] ?>'">
                        View Details
                    </button>
                </div>
            <?php endforeach; ?>

        </div>

        <div class="view-all">
            <a href="jobs.php">View All Jobs</a>
        </div>
    </section>

    <section class="section">
        <h2 class="section-title">Popular Job Categories</h2>

        <div class="grid">
            <?php foreach ($categories as $cat): ?>
                <div class="card">
                    <i class="fa fa-layer-group"></i>

                    <h3><?= htmlspecialchars($cat['job_type']) ?></h3>

                    <p><?= $cat['total'] ?> Jobs Available</p>

                    <button onclick="window.location.href='jobs.php?type=<?= urlencode($cat['job_type']) ?>'">
                        Explore
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    

    <!-- Companies Section -->
    <section class="section" id="companies">
        <h2 class="section-title">Top Hiring Companies</h2>

        <div class="grid">

            <?php foreach ($companies as $c): ?>
                <div class="card">

                    <?php
$colors = ['#0d6efd','#6610f2','#20c997','#fd7e14','#dc3545'];
$bg = $colors[array_rand($colors)];
?>

<div class="company-logo" style="background: <?= $bg ?>;">
                        <?= strtoupper(substr($c['company_name'], 0, 1)) ?>
                    </div>

                    <h3><?= htmlspecialchars($c['company_name']) ?></h3>

                    <p><?= $c['total_jobs'] ?> Open Jobs</p>

                    <button onclick="window.location.href='jobs.php?company=<?= urlencode($c['company_name']) ?>'">
                        View Jobs
                    </button>

                </div>
            <?php endforeach; ?>

        </div>
    </section>


    <div id="jobPopup" class="popup">
        <div class="popup-content">

            <span class="close-btn" onclick="closePopup()">×</span>

            <h2 id="popupTitle"></h2>
            <p id="popupCompany"></p>

            <div class="popup-desc" id="popupDesc"></div>

            <button class="apply-now-btn" onclick="showApplyForm()">Apply Now</button>

            <!-- Apply Form -->
            <div id="applyForm" style="display:none;">
                <h3>Apply for Job</h3>

                <form method="POST" action="applyJob.php" enctype="multipart/form-data">

                    <input type="hidden" name="job_id" id="jobId">
                    <input type="hidden" name="company_name" id="companyName">
                    <input type="hidden" name="position" id="position">

                    <input type="text" name="name" placeholder="Your Name" required>
                    <input type="email" name="email" placeholder="Your Email" required>
                    <input type="text" name="experience" placeholder="Your Experience" required>

                    <input type="file" name="resume" required>

                    <textarea name="message" placeholder="Why should we hire you?" required></textarea>

                    <button type="submit">Submit Application</button>
                </form>
            </div>

        </div>
    </div>

    <script>
        function openJobPopup(id, title, company, location, desc) {
            document.getElementById("jobPopup").style.display = "flex";

            document.getElementById("popupTitle").innerText = title;
            document.getElementById("popupCompany").innerText = company + " • " + location;
            document.getElementById("popupDesc").innerText = desc;

            // 🔥 IMPORTANT
            document.getElementById("jobId").value = id;
            document.getElementById("companyName").value = company;
            document.getElementById("position").value = title;

            document.getElementById("applyForm").style.display = "none";
        }

        function closePopup() {
            document.getElementById("jobPopup").style.display = "none";
        }

        function showApplyForm() {
            let form = document.getElementById("applyForm");
            form.style.display = "block";
            form.scrollIntoView({ behavior: "smooth" });
        }
    </script>

      <?php include "../footer.html"; ?>
</body>

</html>