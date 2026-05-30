<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../Backend/DBConnection.php";
use Backend\DBConnection;

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company_admin') {
    header("Location: ../login.php");
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

/* Get Total Jobs */
$stmt = $conn->prepare("SELECT COUNT(*) FROM jobs WHERE company_id = ?");
$stmt->execute([$companyId]);
$totalJobs = $stmt->fetchColumn();

/* Get Total Applicants */
$stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM job_applications a
    JOIN jobs j ON a.job_id = j.id
    WHERE j.company_id = ?
");
$stmt->execute([$companyId]);
$totalApplicants = $stmt->fetchColumn();

/* Interviews */
$stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM job_applications a
    JOIN jobs j ON a.job_id = j.id
    WHERE j.company_id = ? AND a.status = 'interview'
");
$stmt->execute([$companyId]);
$totalInterviews = $stmt->fetchColumn();

/* Hired */
$stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM job_applications a
    JOIN jobs j ON a.job_id = j.id
    WHERE j.company_id = ? AND a.status = 'hired'
");
$stmt->execute([$companyId]);
$totalHired = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            background: #f4f6f9;
        }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .close-btn {
            display: none;
            font-size: 20px;
            cursor: pointer;
        }

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

        .sidebar {
            width: 250px;
            background: #111827;
            min-height: 100vh;
            color: white;
            padding: 20px;
            position: fixed;
            transition: left 0.3s ease-in-out;
        }

        .sidebar h2 {
            margin-bottom: 30px;
            font-weight: 600;
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

        .main {
            margin-left: 250px;
            width: 100%;
            padding: 20px;
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
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
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

        @media(max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -260px;
                top: 0;
                transition: 0.3s;
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
                margin-left: 0;
                flex-wrap: wrap;
                gap: 10px;
            }

            .topbar div {
                width: 100%;
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

        <a href="dashboard.php" class="active">🏠 Dashboard</a>
        <a href="jobPost.php">📄 Post Job</a>
        <a href="manageJobs.php">📋 Manage Jobs</a>
        <a href="applicants.php">👥 Applicants</a>
        <a href="../Backend/LogOutBackend.php">🚪 Logout</a>
    </div>

    <div id="overlay" onclick="toggleSidebar()"></div>

    <div class="main">

        <div class="topbar">
            <button class="menu-toggle" onclick="toggleSidebar()">☰</button>

            <div>
                Welcome, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
            </div>

            <div>
                <a class="logout" href="../Backend/LogOutBackend.php">Logout</a>
            </div>
        </div>

        <div class="cards">

            <div class="card">
                <h3>📄 Total Jobs</h3>
                <p><?php echo $totalJobs; ?> Active Jobs</p>
            </div>

            <div class="card">
                <h3>👥 Applicants</h3>
                <p><?php echo $totalApplicants; ?> Applications</p>
            </div>

            <div class="card">
                <h3>📈 Interviews</h3>
                <p><?php echo $totalInterviews; ?> Scheduled</p>
            </div>

            <!-- <div class="card">
                <h3>⭐ Hired</h3>
                <p><?php echo $totalHired; ?> Candidates</p>
            </div> -->

        </div>

    </div>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("active");
            document.getElementById("overlay").classList.toggle("active");
        }
    </script>
</body>

</html>