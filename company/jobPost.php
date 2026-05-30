<?php
session_start();
require_once "../Backend/DBConnection.php";

use Backend\DBConnection;

if (!isset($_SESSION['company_id'])) {
    header("Location: login.php");
    exit();
}

$conn = DBConnection::getConnection();

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

if (isset($_POST['post_job'])) {

    $title = $_POST['job_title'];
    $location = $_POST['location'];
    $type = $_POST['job_type'];
    $salary = $_POST['salary'];
    $experience = $_POST['experience'];
    $desc = $_POST['description'];
    $last_date = $_POST['last_date'];

    $company_id = $_SESSION['company_id'];

    $stmt = $conn->prepare("INSERT INTO jobs 
(company_id, company_name, job_title, location, job_type, salary, experience, description, last_date, status) 
VALUES (?,?,?,?,?,?,?,?,?,?)");

    $stmt->execute([
        $company_id,
        $companyName,
        $title,
        $location,
        $type,
        $salary,
        $experience,
        $desc,
        $last_date,
        'pending' //admin approve
    ]);

    header("Location: jobPost.php?success=pending");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <title>Post Job</title>

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

        /* Sidebar */
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
            font-size: 18px;
        }

        .sidebar a {
            display: block;
            padding: 12px;
            margin-bottom: 10px;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.3s;
        }

        .sidebar a:hover {
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



        .menu-toggle {
            display: none;
            font-size: 22px;
            background: none;
            border: none;
            cursor: pointer;
        }

        /* Main Content */
        .main {
            margin-left: 250px;
            padding: 30px;
        }

        .menu-toggle {
            display: none;
            font-size: 22px;
            background: none;
            border: none;
            cursor: pointer;
        }

        .sidebar.active {
            left: 0;
        }

        .topbar {
            background: white;
            padding: 15px 20px;
            margin-left: 250px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 800;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            margin-bottom: 10px;
        }

        .logout {
            color: #ef4444;
            text-decoration: none;
            font-weight: 500;
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

            .close-btn {
                display: block;
            }

            #overlay.active {
                display: block;
            }

            .menu-toggle {
                display: block;
            }

            .main {
                margin-left: 0;
            }

            .topbar {
                margin-left: 0 !important;
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
        }

        /* Page Header */
        .header {
            background: white;
            padding: 30px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .header h1 {
            color: #0d6efd;
            margin-bottom: 5px;
        }

        .header p {
            color: #555;
        }

        /* Form Section */
        .section {
            margin-top: 30px;
        }

        .form-container {
            background: white;
            padding: 30px;
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }

        /* Form Fields */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0d6efd;
            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.2);
        }

        .form-group textarea {
            resize: vertical;
        }

        /* Button */
        .post-btn {
            background: #0d6efd;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .post-btn:active {
            transform: scale(0.98);
        }

        .post-btn:hover {
            background: #0b5ed7;
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
            .section {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2><?php echo htmlspecialchars($companyName); ?></h2>
            <span class="close-btn" onclick="toggleSidebar()">✖</span>
        </div>

        <a href="dashboard.php" class="active">🏠 Dashboard</a>
        <a href="jobPost.php">📄 Post Job</a>
        <a href="manageJobs.php">📋 Manage Jobs</a>
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
            <h1>Post a New Job</h1>
            <p>Fill the details below to publish a job opening</p>
        </div>

        <!-- Form -->
        <div class="section">
            <div class="form-container">

                <?php if (isset($_GET['success']) && $_GET['success'] == 'pending'): ?>
                    <div style="background:#fef3c7;color:#92400e;padding:10px;border-radius:8px;margin-bottom:15px;">
                        ⏳ Job submitted for approval (Pending Admin Approval)
                    </div>
                <?php endif; ?>
                <form method="POST">

                    <div class="form-group">
                        <label>Job Title</label>
                        <input type="text" name="job_title" placeholder="e.g. Frontend Developer" required>
                    </div>

                    <div class="form-group">
                        <label>Job Location</label>
                        <input type="text" name="location" placeholder="e.g. Pune / Remote" required>
                    </div>

                    <div class="form-group">
                        <label>Job Type</label>
                        <select name="job_type" required>
                            <option value="">Select Type</option>
                            <option>Full Time</option>
                            <option>Part Time</option>
                            <option>Internship</option>
                            <option>Contract</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Salary (Optional)</label>
                        <input type="text" name="salary" placeholder="e.g. 4 - 6 LPA">
                    </div>

                    <div class="form-group">
                        <label>Experience Required</label>
                        <input type="text" name="experience" placeholder="e.g. 0-2 Years">
                    </div>

                    <div class="form-group">
                        <label>Job Description</label>
                        <textarea rows="4" name="description" placeholder="Enter job responsibilities and requirements"
                            required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Last Date to Apply</label>
                        <input type="date" name="last_date" required>
                    </div>

                    <button type="submit" name="post_job" class="post-btn">Post Job</button>

                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        © 2026 <?php echo htmlspecialchars($companyName); ?> | Connecting Talent with Opportunities
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("active");
            document.getElementById("overlay").classList.toggle("active");
        }
    </script>

</body>

</html>