<?php
require_once "../Backend/DBConnection.php";

use Backend\DBConnection;

$conn = DBConnection::getConnection();

$stmt = $conn->prepare("
    SELECT 
        c.id,
        c.company_name,
        c.website,
        u.full_name,
        u.email
    FROM companies c
    LEFT JOIN users u ON c.user_id = u.id
    ORDER BY c.id DESC
");
$stmt->execute();
$companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Companies</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f4f7fb;
            display: flex;
        }

        /* SIDEBAR HEADER */
        .sidebar-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        /* CLOSE BUTTON */
        .close-btn {
            display: none;
            font-size: 18px;
            cursor: pointer;
            margin-top: 5px;
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

        /* MOBILE ONLY */
        @media(max-width:768px) {
            .close-btn {
                display: block;
            }
        }

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            background: #111827;
            color: #fff;
            height: 100vh;
            position: fixed;
            padding: 20px;
            transition: 0.3s;
        }

        .sidebar h2 {
            color: #3b82f6;
            margin-bottom: 30px;
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

        .sidebar a:hover,
        .sidebar a.active {
            background: #1f2937;
            color: #fff;
        }

        /* MAIN */
        .main {
            margin-left: 240px;
            width: 100%;
            padding: 20px;
        }

        /* TOPBAR */
        .topbar {
            background: #fff;
            padding: 15px 20px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .menu-toggle {
            display: none;
            font-size: 22px;
            background: none;
            border: none;
            cursor: pointer;
        }

        /* HEADER */
        .header {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        /* SEARCH */
        .search-box input {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }

        /* GRID */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        /* CARD */
        .card {
            background: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
            position: relative;
        }

        .card:hover {
            transform: translateY(-6px);
        }

        .card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            height: 5px;
            width: 100%;
            background: linear-gradient(90deg, #0d6efd, #6610f2);
            border-radius: 10px 10px 0 0;
        }

        .company {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }

        .info {
            font-size: 14px;
            color: #6b7280;
            margin-top: 6px;
        }

        .website a {
            color: #0d6efd;
            text-decoration: none;
        }

        .website a:hover {
            text-decoration: underline;
        }

        /* BADGE */
        .badge {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            padding: 4px 10px;
            border-radius: 10px;
            font-size: 12px;
            margin-top: 10px;
        }

        /* EMPTY */
        .empty {
            text-align: center;
            margin-top: 40px;
            color: #777;
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
            <h3>🏢 Companies</h3>
        </div>

        <!-- HEADER -->
        <div class="header">
            <h2>Registered Companies</h2>
            <p>Manage and view all registered companies</p>
        </div>

        <!-- SEARCH -->
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search company name...">
        </div>

        <!-- GRID -->
        <div class="grid" id="companyGrid">

            <?php if (!empty($companies)): ?>

                <?php foreach ($companies as $c): ?>

                    <div class="card" data-name="<?= strtolower($c['company_name']) ?>">

                        <div class="company">
                            <?= htmlspecialchars($c['company_name'] ?? 'N/A') ?>
                        </div>

                        <div class="info">👤 <?= htmlspecialchars($c['full_name'] ?? 'N/A') ?></div>

                        <div class="info">📧 <?= htmlspecialchars($c['email'] ?? 'N/A') ?></div>

                        <div class="info website">
                            🌐
                            <a href="<?= htmlspecialchars($c['website'] ?? '#') ?>" target="_blank">
                                <?= htmlspecialchars($c['website'] ?? 'N/A') ?>
                            </a>
                        </div>

                        <div class="badge">ID: <?= $c['id'] ?></div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="empty">
                    <h3>No Companies Found 😔</h3>
                </div>

            <?php endif; ?>

        </div>

    </div>

    <div class="pdf-btn" onclick="downloadPDF()">
        <i class="fas fa-file-pdf"></i>
    </div>

    <script>
        function downloadPDF() {
            window.print();
        }
    </script>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("active");
        }

        /* SEARCH FIXED */
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let value = this.value.toLowerCase();
            let cards = document.querySelectorAll(".card");

            cards.forEach(card => {
                let name = card.getAttribute("data-name");

                if (name.includes(value)) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });
        });
    </script>

</body>

</html>