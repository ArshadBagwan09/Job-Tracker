<?php
require_once "../Backend/DBConnection.php";

use Backend\DBConnection;

$conn = DBConnection::getConnection();

if (!$conn) {
    die("Database Connection Failed");
}

$search = $_GET['search'] ?? '';

if ($search != '') {

    $stmt = $conn->prepare("
        SELECT * FROM jobs 
        WHERE status='approved'
        AND job_title LIKE ?
        ORDER BY created_at DESC
    ");

    $stmt->execute(["%$search%"]);
} else {

    $stmt = $conn->prepare("
        SELECT * FROM jobs 
        WHERE status='approved'
        ORDER BY created_at DESC
    ");

    $stmt->execute();
}

$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Jobs - CareerHub</title>
    <link rel="stylesheet" href="../Style/navBarStyle.css">
    <link rel="stylesheet" href="../Style/footerStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
            background: #f4f7fb;
            color: #333;
        }

        /* Header */
        .header {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            color: white;
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 36px;
            font-weight: 600;
        }

        .header p {
            opacity: 0.9;
            margin-top: 8px;
        }

        /* Search & Filter Section */
        .search-section {
            background: #fff;
            margin: 0 60px;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .search-section input,
        .search-section select {
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            flex: 1;
            min-width: 180px;
        }

        .search-section button {
            padding: 10px 20px;
            background: #0077ff;
            border: none;
            color: #fff;
            border-radius: 20px;
            cursor: pointer;
        }

        .search-section button:hover {
            background: #005fd1;
        }

        /* Jobs Section */
        .jobs-section {
            padding: 40px 60px;
        }

        .jobs-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        /* Job Card */
        .job-card {
            background: #fff;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
            transition: 0.3s;

            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 280px;
        }

        .job-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .job-card i {
            font-size: 35px;
            color: #0077ff;
            margin-bottom: 10px;
        }

        .job-card h3 {
            margin-bottom: 5px;
        }

        .job-card p {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            flex-grow: 1;
        }

        .company-badge {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0077ff, #6610f2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;

            position: absolute;
            top: 15px;
            right: 15px;
        }

        .company-badge {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .job-card:hover .company-badge {
            transform: scale(1.1);
        }

        .tag {
            display: inline-block;
            padding: 4px 10px;
            background: #eaf2ff;
            color: #0077ff;
            border-radius: 12px;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .view-btn {
            display: inline-block;
            text-align: center;
            padding: 8px 18px;
            background: #0077ff;
            color: #fff;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .view-btn:hover {
            background: #005fd1;
        }

        /* Tablet */
        @media(max-width:900px) {
            .jobs-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .jobs-section {
                padding: 40px 30px;
            }

            .search-section {
                margin: 0 30px;
            }

            .navbar {
                padding: 15px 30px;
            }
        }

        /* Mobile */
        @media(max-width:600px) {
            .jobs-grid {
                grid-template-columns: 1fr;
            }

            .navbar {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .search-section {
                margin: 0 20px;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <?php include "../navBar.html"; ?>

    <!-- Header -->
    <div class="header">
        <h1>All Job Openings</h1>
        <p>Explore opportunities from top companies</p>
    </div>

    <!-- Search & Filters -->
    <div class="search-section">
        <input type="text" id="searchInput" placeholder="Search by job or company">
        <select id="locationFilter">
            <option value="">All Locations</option>
            <option>Pune</option>
            <option>Bangalore</option>
            <option>Hyderabad</option>
            <option>Remote</option>
        </select>

        <select id="typeFilter">
            <option value="">Job Type</option>
            <option>Full Time</option>
            <option>Part Time</option>
            <option>Internship</option>
        </select>

        <button onclick="filterJobs()">Search</button>
    </div>

    <!-- Jobs -->
    <section class="jobs-section">
        <div class="jobs-grid">

            <?php foreach ($jobs as $row) { ?>

                <div class="job-card" style="cursor: pointer;"
                    onclick="window.location.href='companyProfile.php?id=<?php echo $row['id']; ?>'"
                    data-title="<?php echo strtolower($row['job_title'] . ' ' . $row['company_name'] . ' ' . $row['location'] . ' ' . $row['job_type']); ?>">

                    <div class="company-badge">
                        <?= strtoupper(substr(!empty($row['company_name']) ? $row['company_name'] : 'C', 0, 1)) ?>
                        <!-- <?= $row['company_name'] ?> -->
                    </div>

                    <h3><?php echo $row['job_title']; ?></h3>

                    <p>
                        <strong style="color:#0077ff;">
                            <?php echo $row['company_name']; ?>
                        </strong>
                        • <?php echo $row['location']; ?>
                    </p>

                    <?php
                    $words = explode(" ", $row['description']);
                    if (count($words) > 20) {
                        $shortDesc = implode(" ", array_slice($words, 0, 20));
                        echo "<p>$shortDesc... 
    <a href='companyDetails.php?id=" . $row['id'] . "' style='color:#0077ff;font-weight:600;'>more</a>
    </p>";
                    } else {
                        echo "<p>" . $row['description'] . "</p>";
                    }
                    ?>
                    <span class="tag"><?php echo $row['job_type']; ?></span>

                    <a href="companyProfile.php?id=<?= $row['id'] ?>" class="view-btn">
                        View
                    </a>

                    <p style="font-size:12px;color:#888;margin-top:10px;">
                        Posted on:
                        <?php echo date("d M Y, h:i A", strtotime($row['created_at'])); ?>
                    </p>

                    <?php
                    $today = date("Y-m-d");
                    $lastDate = $row['last_date'];
                    ?>

                    <p style="font-size:12px; font-weight:600;">
                        <?php if ($lastDate < $today): ?>
                            <span style="color:#e63946;">Expired: <?= date("d M Y", strtotime($lastDate)); ?></span>
                        <?php else: ?>
                            <span style="color:#2ecc71;">
                                Last Date: <?= date("d M Y", strtotime($lastDate)); ?>
                            </span>
                        <?php endif; ?>
                    </p>

                </div>

            <?php } ?>

        </div>
    </section>

    <script>
        function apply() {
            alert("Please login to apply");
            window.location.href = "login.php";
        }

        function filterJobs() {
            let search = document.getElementById("searchInput").value.toLowerCase();
            let location = document.getElementById("locationFilter").value.toLowerCase();
            let type = document.getElementById("typeFilter").value.toLowerCase();

            let cards = document.querySelectorAll(".job-card");

            cards.forEach(card => {
                let text = card.getAttribute("data-title").toLowerCase();

                if (text.includes(search) && text.includes(location) && text.includes(type)) {
                    card.style.display = "flex";
                } else {
                    card.style.display = "none";
                }
            });
        }
    </script>
    <?php include "../footer.html"; ?>

</body>

</html>