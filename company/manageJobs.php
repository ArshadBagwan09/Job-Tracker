<?php
session_start();
require_once "../Backend/DBConnection.php";

use Backend\DBConnection;

$conn = DBConnection::getConnection();

if (!$conn) {
    die("Database Connection Failed");
}

// 🔒 Login check
if (!isset($_SESSION['company_id'])) {
    header("Location: ../login.php");
    exit;
}

/* Get Company Info */
$stmt = $conn->prepare("
    SELECT id, company_name 
    FROM companies 
    WHERE user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    die("Company not found");
}

$companyId = $company['id'];
$companyName = $company['company_name'];

$stmt = $conn->prepare("SELECT * FROM jobs WHERE company_id = ?");
$stmt->execute([$_SESSION['company_id']]);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <title>Manage Jobs</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: #f4f8ff;
        }

        /* Sidebar */

        .menu-toggle {
            display: none;
            font-size: 22px;
            background: none;
            border: none;
            cursor: pointer;
        }

        .sidebar {
            width: 250px;
            background: #111827;
            min-height: 100vh;
            color: white;
            padding: 20px;
            position: fixed;
            left: 0;
            top: 0;
            transition: 0.3s;
        }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .close-btn {
            display: none;
            cursor: pointer;
        }

        .sidebar a {
            display: block;
            padding: 12px;
            margin-bottom: 10px;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 8px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #1f2937;
            color: white;
        }

        /* Overlay */
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 900;
        }

        /* Topbar */
        .topbar {
            margin-left: 250px;
            background: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 800;
        }

        .logout {
            color: #ef4444;
            text-decoration: none;
            font-weight: 500;
        }

        /* Main */
        .main {
            margin-left: 250px;
            padding: 30px;
            flex: 1;
        }

        /* Mobile */
        @media(max-width:768px) {

            .sidebar {
                left: -260px;
                z-index: 1000;
            }

            .sidebar.active {
                left: 0;
            }

            .topbar {
                margin-left: 0;
                flex-wrap: wrap;
                gap: 10px;
            }

            .topbar div {
                width: 100%;
                text-align: center;
            }

            .close-btn {
                display: block;
            }

            #overlay.active {
                display: block;
            }

            .menu-toggle {
                display: block;
            }

            .main,
            .topbar {
                margin-left: 0;
            }
        }

        /* Header */
        .header {
            background: white;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .header h1 {
            color: #0d6efd;
        }

        .add-btn {
            background: #0d6efd;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            cursor: pointer;
        }

        .add-btn:hover {
            background: #0b5ed7;
        }

        /* Section */
        .section {
            margin-top: 30px;
        }

        /* Grid */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        /* Job Card */
        .card {
            background: linear-gradient(145deg, #ffffff, #f0f4ff);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(13, 110, 253, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 240px;
            border: 1px solid rgba(13, 110, 253, 0.08);

            backdrop-filter: blur(6px);
        }

        /* Decorative top bar */
        .card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #0d6efd, #6610f2);
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(13, 110, 253, 0.15);
        }

        /* Title */
        .job-title {
            font-size: 20px;
            font-weight: 600;
            color: #0d6efd;
            margin-bottom: 8px;
        }

        /* Location & Type */
        .job-location {
            font-size: 14px;
            color: #777;
            margin-bottom: 12px;
        }

        /* Description */
        .job-desc {
            font-size: 14px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        /* Buttons */
        .card-buttons {
            display: flex;
            gap: 10px;
        }

        .edit-btn {
            background: #fff3cd;
            color: #856404;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
        }

        .edit-btn:hover {
            background: #ffc107;
            color: white;
        }

        .delete-btn {
            background: #f8d7da;
            color: #842029;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background: #dc3545;
            color: white;
        }

        /* PDF Floating Button */
        .pdf-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 55px;
            height: 55px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            z-index: 999;
            transition: 0.3s;
        }

        .pdf-btn:hover {
            transform: scale(1.1);
            background: #dc2626;
        }

        @media print {

            .sidebar,
            .topbar,
            .pdf-btn,
            #overlay,
            .add-btn,
            .edit-btn,
            .delete-btn {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .main {
                margin: 0 !important;
                padding: 10px;
            }

            .card {
                page-break-inside: avoid;
            }
        }

        @media print {

            .card {
                border: 1px solid #000 !important;
                /* 🔥 Border visible */
                box-shadow: none !important;
                /* Shadow remove */
                border-radius: 10px;
                padding: 15px;
            }

            .grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                /* 2 per row (clean layout) */
                gap: 15px;
            }
        }

        @media print {

            .header {
                box-shadow: none !important;
                border-bottom: 2px solid #000;
                padding: 10px 0;
                margin-bottom: 20px;
                text-align: center;
            }

            .header h1 {
                font-size: 20px;
                color: #000 !important;
                margin: 0;
            }
        }

        @media print {

            .topbar {
                display: none !important;
            }

            body::before {
                content: "Manage Job Listings";
                display: block;
                text-align: center;
                font-size: 22px;
                font-weight: bold;
                margin-bottom: 10px;
            }
        }

        /* Footer */
        /* ===== FOOTER ===== */

        .footer {
            background: linear-gradient(90deg, #111827, #1f2937);
            color: #e5e7eb;
            padding: 18px 30px;
            margin-top: 40px;
            border-top: 3px solid #0d6efd;
        }

        /* Footer layout */
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        /* Left + Right text */
        .footer-left,
        .footer-right {
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        /* Highlight username */
        .footer-right strong {
            color: #0d6efd;
        }

        /* Hover effect */
        .footer-left:hover,
        .footer-right:hover {
            color: #ffffff;
            transition: 0.3s;
        }

        /* ===== MOBILE RESPONSIVE ===== */

        @media(max-width:768px) {

            .footer {
                padding: 15px;
                text-align: center;
            }

            .footer-content {
                flex-direction: column;
                gap: 8px;
            }

            .footer-left,
            .footer-right {
                font-size: 13px;
            }
        }

        /* Responsive */
        @media(max-width:600px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2><?php echo htmlspecialchars($companyName); ?></h2>
            <span class="close-btn" onclick="toggleSidebar()">✖</span>
        </div>

        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="jobPost.php">📄 Post Job</a>
        <a href="manageJobs.php" class="active">📋 Manage Jobs</a>
        <a href="applicants.php">👥 Applicants</a>
        <a href="../Backend/LogOutBackend.php">🚪 Logout</a>
    </div>

    <div id="overlay" onclick="toggleSidebar()"></div>

    <div class="topbar">
        <button class="menu-toggle" onclick="toggleSidebar()">☰</button>

        <div>
            Welcome, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
        </div>

        <div>
            <a class="logout" href="../Backend/LogOutBackend.php">Logout</a>
        </div>
    </div>


    <!-- Header -->

    <div class="main">
        <div class="header">
            <h1>Manage Job Listings</h1>
            <button class="add-btn" onclick="window.location.href='jobPost.php'">
                + Add New Job
            </button>
        </div>

        <!-- Jobs Section -->
        <div class="section">
            <div class="grid">

                <?php if (!empty($jobs) && is_array($jobs)) { ?>

                    <?php foreach ($jobs as $job) { ?>

                        <div class="card">

                            <div class="job-title">
                                <?php echo htmlspecialchars($job['job_title'] ?? ''); ?>
                            </div>

                            <div class="job-location">
                                <?php echo htmlspecialchars($job['location'] ?? ''); ?> |
                                <?php echo htmlspecialchars($job['job_type'] ?? ''); ?>

                                <?php
                                $status = $job['status'] ?? 'pending';

                                if ($status == 'approved') {
                                    echo "<span style='background:#d1fae5;color:#065f46;padding:4px 10px;border-radius:12px;font-size:12px;'>✔ Approved</span>";
                                } elseif ($status == 'rejected') {
                                    echo "<span style='background:#fee2e2;color:#991b1b;padding:4px 10px;border-radius:12px;font-size:12px;'>❌ Rejected</span>";
                                } else {
                                    echo "<span style='background:#fef3c7;color:#92400e;padding:4px 10px;border-radius:12px;font-size:12px;'>⏳ Pending</span>";
                                }
                                ?>
                            </div>

                            <div class="job-desc">
                                <?php
                                $description = $job['description'] ?? '';
                                $words = explode(" ", $description);

                                if (count($words) > 15) {
                                    echo htmlspecialchars(implode(" ", array_slice($words, 0, 15))) . "...";
                                } else {
                                    echo htmlspecialchars($description);
                                }
                                ?>
                            </div>

                            <?php if ($job['status'] == 'approved') { ?>
                                <p style="margin:20px 0;color:#16a34a;font-size:13px;">
                                    🔒This job is approved & locked by admin
                                </p>
                            <?php } ?>


                            <div class="card-buttons">

                                <!-- ❌ Edit only when NOT approved -->
                                <?php if ($job['status'] != 'approved') { ?>
                                    <a href="edit-job.php?id=<?php echo $job['id']; ?>">
                                        <button type="button" class="edit-btn">Edit</button>
                                    </a>
                                <?php } ?>

                                <!-- ✅ Delete ALWAYS visible -->
                                <a href="delete-job.php?id=<?php echo $job['id']; ?>"
                                    onclick="return confirm('Are you sure you want to delete this job?')">
                                    <button type="button" class="delete-btn">Delete</button>
                                </a>

                            </div>




                        </div>

                    <?php } ?>

                <?php } else { ?>

                    <div class="no-jobs">
                        <p>No jobs posted yet.</p>
                    </div>

                <?php } ?>

            </div>
        </div>
    </div>

    <div class="pdf-btn" onclick="downloadPDF()">
        <i class="fas fa-file-pdf"></i>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-content">
            <div class="footer-left">
                © <?php echo date("Y"); ?> <?php echo htmlspecialchars($companyName); ?> Admin Panel
            </div>

            <div class="footer-right">
                Logged in as: <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Company User'); ?>
            </div>
        </div>
    </div>

    <script>
        function removeJob(button) {
            if (confirm("Are you sure you want to delete this job?")) {
                button.closest(".card").remove();
            }
        }
    </script>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("active");
            document.getElementById("overlay").classList.toggle("active");
        }
    </script>

    <script>
        function downloadPDF() {
            window.print();
        }
    </script>
</body>

</html>