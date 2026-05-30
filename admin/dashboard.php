<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            background: #f4f7fb;
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

        /* MOBILE ONLY */
        @media(max-width:768px) {
            .close-btn {
                display: block;
            }
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background: #111827;
            color: #fff;
            position: fixed;
            padding: 20px;
            transition: 0.3s;
        }

        .sidebar h2 {
            margin-bottom: 30px;
            color: #3b82f6;
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
            color: #fff;
        }

        /* MAIN */
        .main {
            margin-left: 240px;
            padding: 30px;
            width: 100%;
        }

        /* TOPBAR */
        .topbar {
            background: #fff;
            padding: 15px 20px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .menu-toggle {
            display: none;
            font-size: 22px;
            background: none;
            border: none;
        }

        /* CARDS */
        .cards {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            background: #fff;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            margin-bottom: 10px;
            color: #111827;
        }

        .card p {
            color: #6b7280;
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
            <h3>Welcome Admin 👋</h3>
        </div>

        <!-- CARDS -->
        <div class="cards">

            <div class="card" onclick="location.href='jobRequests.php'">
                <h3>📄 Job Requests</h3>
                <p>Approve or reject job postings</p>
            </div>

            <div class="card" onclick="location.href='companies.php'">
                <h3>🏢 Companies</h3>
                <p>View all registered companies</p>
            </div>

            <div class="card" onclick="location.href='../login.php'">
                <h3>🚪 Logout</h3>
                <p>Securely logout from admin panel</p>
            </div>

        </div>

    </div>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("active");
        }
    </script>

</body>

</html>