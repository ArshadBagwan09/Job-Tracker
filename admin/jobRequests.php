<?php
require_once "../Backend/DBConnection.php";

use Backend\DBConnection;

$conn = DBConnection::getConnection();

// APPROVE
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->prepare("UPDATE jobs SET status='approved' WHERE id=?")->execute([$id]);
    header("Location: jobRequests.php");
    exit();
}

// REJECT
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $conn->prepare("UPDATE jobs SET status='rejected' WHERE id=?")->execute([$id]);
    header("Location: jobRequests.php");
    exit();
}

// FETCH JOBS
$stmt = $conn->prepare("SELECT * FROM jobs WHERE status='pending' ORDER BY created_at DESC");
$stmt->execute();
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Requests</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f4f7fb;
        }

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            height: 100vh;
            background: #111827;
            color: #fff;
            position: fixed;
            left: 0;
            top: 0;
            padding: 20px;
            transition: 0.3s;
            z-index: 1000;
        }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
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

        /* CLOSE BTN */
        .close-btn {
            display: none;
            cursor: pointer;
        }

        /* MAIN */
        .main {
            margin-left: 240px;
            padding: 20px;
        }

        /* TOPBAR */
        .topbar {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .menu-toggle {
            display: none;
            font-size: 22px;
            background: none;
            border: none;
        }

        /* HEADER */
        .header {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin-bottom: 5px;
        }

        /* FILTER */
        .filter-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-box input,
        .filter-box select {
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        /* GRID */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        /* CARD */
        .card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .company {
            font-size: 14px;
            color: #6b7280;
        }

        .title {
            font-size: 18px;
            font-weight: 600;
            margin: 5px 0;
        }

        .location {
            font-size: 14px;
            color: #555;
        }

        .date {
            font-size: 12px;
            color: #999;
            margin-bottom: 10px;
        }

        /* BADGE */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 10px;
            font-size: 12px;
            background: #fef3c7;
            color: #92400e;
            margin-bottom: 10px;
        }

        /* BUTTONS */
        .actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            border: none;
            padding: 8px;
            border-radius: 6px;
            cursor: pointer;
        }

        .approve {
            background: #22c55e;
            color: white;
        }

        .reject {
            background: #ef4444;
            color: white;
        }

        /* OVERLAY */
        #overlay {
            display: none;
            position: fixed;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            top: 0;
            left: 0;
            z-index: 900;
        }

        /* PDF Button */
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

        /* PAGE SETUP */
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #ffffff;
        }

        /* HEADER */
        .header {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .header h2 {
            margin: 0;
            font-size: 20px;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 12px;
        }

        /* GRID (IMPORTANT 🔥) */
        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            /* 👉 2 columns */
            gap: 15px;
        }

        /* CARD */
        .card {
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 12px;
            background: #fff;
            page-break-inside: avoid;
            /* 🔥 important */
        }

        /* BADGE */
        .badge {
            display: inline-block;
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 10px;
            background: #fef3c7;
            color: #92400e;
            margin-bottom: 8px;
        }

        /* TEXT */
        .company {
            font-size: 12px;
            color: #6b7280;
        }

        .title {
            font-size: 14px;
            font-weight: 600;
            margin: 3px 0;
        }

        .location {
            font-size: 12px;
            color: #444;
        }

        .date {
            font-size: 11px;
            color: #777;
        }

        /* PRINT FIX */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }

            .grid {
                grid-template-columns: repeat(2, 1fr);
                /* ensure 2 columns */
            }

            .sidebar,
            .topbar,
            .filter-box,
            .pdf-btn,
            #overlay,
            .actions {
                display: none !important;
            }
        }


        @media print {
            .card:nth-child(2n) {
                page-break-after: auto;
            }
        }

        /* MOBILE */
        @media(max-width:768px) {

            .sidebar {
                left: -260px;
            }

            .sidebar.active {
                left: 0;
            }

            .main {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }

            .close-btn {
                display: block;
            }

            #overlay.active {
                display: block;
            }
        }
    </style>
</head>

<body>

   <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">

        <!-- NEW HEADER -->
        <div class="sidebar-header">
            <h2>Admin Panel</h2>
            <span class="close-btn" onclick="toggleSidebar()">✖</span>
        </div>

        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="jobRequests.php">📄 Job Requests</a>
        <a href="companies.php">🏢 Companies</a>
        <a href="users.php" class="active">👤 Users</a>
        <a href="../Backend/LogOutBackend.php">🚪 Logout</a>
    </div>

<!-- <div id="overlay" onclick="toggleSidebar()"></div> -->

    <!-- MAIN -->
    <div class="main">

        <!-- TOPBAR -->
        <div class="topbar">
            <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
            <h3>Admin - Job Requests</h3>
        </div>

        <!-- HEADER -->
        <div class="header">
            <h2>📄 Job Approval Requests</h2>
            <p>Approve or reject job postings</p>
        </div>

        <!-- FILTER -->
        <div class="filter-box">
            <input type="text" id="searchInput" placeholder="Search job or company...">

            <select id="sortFilter">
                <option value="">Sort By</option>
                <option value="date">Latest</option>
                <option value="title">Position (A-Z)</option>
            </select>
        </div>

        <!-- GRID -->
        <div class="grid" id="jobGrid">

            <?php if (!empty($jobs)): ?>
                <?php foreach ($jobs as $job): ?>

                    <div class="card"
                        data-title="<?= strtolower($job['job_title']) ?>"
                        data-company="<?= strtolower($job['company_name']) ?>"
                        data-date="<?= $job['created_at'] ?>">

                        <div class="badge">⏳ Pending</div>

                        <div class="company"><?= htmlspecialchars($job['company_name']) ?></div>
                        <div class="title"><?= htmlspecialchars($job['job_title']) ?></div>
                        <div class="location">📍 <?= htmlspecialchars($job['location']) ?></div>
                        <div class="date">📅 <?= date("d M Y", strtotime($job['created_at'])) ?></div>

                        <div class="actions">
                            <a href="?approve=<?= $job['id'] ?>">
                                <button class="btn approve">Approve</button>
                            </a>
                            <a href="?reject=<?= $job['id'] ?>">
                                <button class="btn reject">Reject</button>
                            </a>
                        </div>

                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <p>No Jobs Found</p>
            <?php endif; ?>

        </div>
    </div>

    <div class="pdf-btn" onclick="downloadPDF()">
        <i class="fas fa-file-pdf"></i>
    </div>
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

    <script>
        const searchInput = document.getElementById("searchInput");
        const sortFilter = document.getElementById("sortFilter");
        const grid = document.getElementById("jobGrid");

        searchInput.addEventListener("keyup", filterJobs);
        sortFilter.addEventListener("change", filterJobs);

        function filterJobs() {
            let search = searchInput.value.toLowerCase();
            let cards = Array.from(document.querySelectorAll(".card"));

            cards.forEach(card => {
                let title = card.dataset.title;
                let company = card.dataset.company;

                card.style.display =
                    (title.includes(search) || company.includes(search)) ? "block" : "none";
            });

            let sorted = cards.sort((a, b) => {
                if (sortFilter.value === "title") {
                    return a.dataset.title.localeCompare(b.dataset.title);
                }
                if (sortFilter.value === "date") {
                    return new Date(b.dataset.date) - new Date(a.dataset.date);
                }
                return 0;
            });

            sorted.forEach(card => grid.appendChild(card));
        }
    </script>

</body>

</html>