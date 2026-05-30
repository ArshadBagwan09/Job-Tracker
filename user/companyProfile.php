<?php
require_once __DIR__ . "/../Backend/DBConnection.php";

use Backend\DBConnection;

$conn = DBConnection::getConnection();

if (!$conn) {
    die("Database connection failed");
}

// GET job id
if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$job_id = $_GET['id'];

// Fetch job details
$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->execute([$job_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    die("Job not found");
}


$stmtC = $conn->prepare("SELECT * FROM companies WHERE company_name = ?");
$stmtC->execute([$job['company_name']]);
$companyData = $stmtC->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    die("Job not found");
}

// Fetch same company jobs
$stmt2 = $conn->prepare("SELECT * FROM jobs WHERE company_id = ? LIMIT 4");
$stmt2->execute([$job['company_id']]);
$companyJobs = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Style/navBarStyle.css">
    <link rel="stylesheet" href="../Style/footerStyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Company Profile</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f4f8ff;
        }

        /* Navbar */
        .navbar {
            background: #0d6efd;
            color: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            font-size: 22px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 500;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        /* Company Header */
        .company-header {
            background: white;
            padding: 40px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .company-logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            font-weight: bold;
            color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-transform: uppercase;
        }

        .company-info h1 {
            color: #0d6efd;
            margin-bottom: 5px;
        }

        .company-info p {
            color: #555;
        }

        /* Section */
        .section {
            padding: 40px;
        }

        .section h2 {
            color: #0d6efd;
            margin-bottom: 20px;
        }

        /* Cards Grid */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        /* Card */
        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        /* Stats */
        .stat {
            text-align: center;
        }

        .stat h3 {
            color: #0d6efd;
            font-size: 28px;
        }

        /* Job Card */
        .job-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .job-location {
            color: #666;
            margin-bottom: 10px;
        }

        .apply-btn {
            background: #0d6efd;
            font-weight: 600;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
        }

        .apply-btn {
            display: inline-block !important;
        }

        .apply-btn:hover {
            background: #0b5ed7;
        }

        .apply-btn:disabled {
            opacity: 0.7;
            pointer-events: none;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .active {
            background: #e6f9f0;
            color: #2ecc71;
        }

        .expired {
            background: #ffe6e6;
            color: #e63946;
        }

        /* Contact */
        .contact p {
            margin: 6px 0;
            color: #555;
        }

        /* Footer */
        .footer {
            background: #0d6efd;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 30px;
        }

        /* Responsive */
        @media(max-width:600px) {
            .company-header {
                text-align: center;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <?php include "../navBar.html"; ?>
    <?php
    $colors = ['#0d6efd', '#6610f2', '#20c997', '#fd7e14', '#dc3545'];
    $bg = $colors[crc32($job['company_name']) % count($colors)];
    ?>

    <!-- Company Header -->
    <div class="company-header">
        <div class="company-logo" style="background: <?= $bg ?>;">
            <?= strtoupper(substr($job['company_name'], 0, 1)) ?>
        </div>
        <div class="company-info">
            <h1><?= htmlspecialchars($job['company_name']) ?></h1>
            <p>Innovating the Future of Technology</p>
            <p>
                <?= htmlspecialchars($job['job_title']) ?>
            </p>
            <p>📍 <?= htmlspecialchars($job['location']) ?></p>
        </div>

        <?php
        $today = date("Y-m-d");
        $isExpired = ($job['last_date'] < $today);
        ?>

        <?php if ($isExpired): ?>
            <span class="status-badge expired">Expired: <?= date("d M Y", strtotime($job['last_date'])) ?></span>
        <?php else: ?>
            <span class="status-badge active">
                Last Date: <?= date("d M Y", strtotime($job['last_date'])) ?>
            </span>
        <?php endif; ?>

        <div style="padding:20px 40px;">
            <button class="apply-btn"
                <?= $isExpired ? 'disabled style="background:#ccc;cursor:not-allowed;"' :
                    'onclick="window.location.href=\'applyJob.php?id=' . $job['id'] . '&job=' . urlencode($job['job_title']) . '&company=' . urlencode($job['company_name']) . '\'"' ?>>

                <?= $isExpired ? '❌ Expired' : '🚀 Apply Now' ?>

            </button>
        </div>
    </div>


    <!-- About Company -->
    <div class="section">
        <h2>About Company</h2>
        <div class="card">
            <p>
                <?= htmlspecialchars($job['description']) ?>
            </p>
        </div>
    </div>

    <!-- Company Stats -->
    <div class="section">
        <h2>Company Overview</h2>
        <div class="grid">
            <div class="card stat">
                <h3><?= rand(50, 500) ?>+</h3>
                <p>Employees</p>
            </div>

            <div class="card stat">
                <h3><?= rand(1, 10) ?>+</h3>
                <p>Years Experience</p>
            </div>

            <div class="card stat">
                <h3><?= rand(10, 100) ?>+</h3>
                <p>Projects</p>
            </div>

            <div class="card stat">
                <h3><?= count($companyJobs) ?>+</h3>
                <p>Open Jobs</p>
            </div>
        </div>
    </div>

    <!-- Open Jobs -->

    <div class="section">
        <h2>Open Positions</h2>
        <div class="grid">

            <?php foreach ($companyJobs as $j): ?>
                <div class="card">
                    <div class="job-title"><?= htmlspecialchars($j['job_title']) ?></div>
                    <div class="job-location">
                        <?= htmlspecialchars($j['location']) ?> | <?= htmlspecialchars($j['job_type']) ?>
                    </div>

                    <button class="apply-btn"
                        onclick="window.location.href='companyProfile.php?id=<?= $j['id'] ?>'">
                        View Details
                    </button>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
    <!-- Contact Section -->
    <div class="section">
        <h2>Contact Information</h2>
        <div class="card contact">
            <p>📧 Email: <?= $companyData['email'] ?? 'Not Available' ?></p>
            <p>📞 Phone: <?= $companyData['phone'] ?? 'Not Available' ?></p>
            <p>📍 Address: <?= $companyData['address'] ?? $job['location'] ?></p>
        </div>
    </div>

    <!-- Footer -->
    <?php include "../footer.html"; ?>

</body>

</html>