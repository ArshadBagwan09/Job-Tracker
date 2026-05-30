<?php
session_start();
require_once "../Backend/DBConnection.php";

use Backend\DBConnection;

$conn = DBConnection::getConnection();

if (!isset($_SESSION['company_id'])) {
    header("Location: ../login.php");
    exit;
}

$company_id = $_SESSION['company_id'];

$stmt = $conn->prepare("
    SELECT job_applications.*, 
           users.full_name, 
           users.email
    FROM job_applications
    JOIN users ON job_applications.user_id = users.id
    WHERE job_applications.company_name = (
        SELECT company_name FROM companies WHERE id = ?
    )
    ORDER BY job_applications.created_at DESC
");
$stmt->execute([$company_id]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Applicants Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #eef3ff, #f8fbff);
            margin: 0;
            padding: 40px;
            color: #333;

        }

        h2 {
            text-align: center;
            color: #0d6efd;
            margin-bottom: 30px;
            font-size: clamp(20px, 3vw, 28px);
            font-weight: 600;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(13, 110, 253, 0.08);
        }

        .top-bar {
            display: flex;
            justify-content: end;
            margin-bottom: 20px;
        }

        .search-box {
            padding: 10px 15px;
            border-radius: 10px;
            border: 1px solid #dbeafe;
            font-size: clamp(12px, 1.2vw, 14px);
            width: 250px;
            outline: none;
            transition: 0.3s;
        }

        .search-box:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            border-radius: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: clamp(12px, 1.2vw, 15px);
            background: white;
            border-radius: 12px;
            overflow: hidden;
            min-width: 700px;
        }

        td,
        th {
            white-space: nowrap;
        }

        th {
            background: #111827;
            color: white;
            padding: 15px;
        }

        td {
            padding: 14px;
            text-align: center;
        }

        tr {
            transition: 0.3s ease;
        }

        tr:hover {
            background: #f3f4f6;
            transform: scale(1.01);
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: clamp(11px, 1vw, 13px);
            font-weight: 600;
        }

        .applied {
            background: #d1e7dd;
            color: #0f5132;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .interview {
            background: #cff4fc;
            color: #055160;
        }

        .rejected {
            background: #f8d7da;
            color: #842029;
        }

        .offer {
            background: #e2e3ff;
            color: #383d7a;
        }

        /* View Button */
        .view-btn {
            background: #0d6efd;
            color: white;
            padding: 7px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            font-size: clamp(12px, 1.2vw, 14px);
        }

        .view-btn:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            width: 400px;
            border-radius: 15px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .close {
            float: right;
            cursor: pointer;
            font-size: 18px;
        }

        @media (max-width:768px) {

            body {
                padding: 15px;
                font-size: 14px;
            }

            .container {
                padding: 20px;
            }

            h2 {
                font-size: 22px;
            }

            .top-bar {
                flex-direction: column;
                gap: 10px;
            }

            .search-box {
                width: auto;
            }

            th,
            td {
                padding: 8px;
                font-size: 12px;
            }

            .status {
                font-size: 11px;
                padding: 4px 8px;
            }

            .view-btn {
                padding: 5px 8px;
                font-size: 11px;
            }

            .modal-content {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <h2>🚀 Applicants Dashboard</h2>

    <div class="container">

        <div class="top-bar">
            <input type="text" class="search-box" id="searchInput" placeholder="Search by Position..."
                onkeyup="searchTable()">
        </div>

        <div class="table-wrapper">
            <table id="appTable">
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Experience</th>
                    <th>Applied Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

                <?php foreach ($applications as $row): ?>
                    <tr>
                        <td><?= $row['user_id'] ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['position']) ?></td>
                        <td><?= htmlspecialchars($row['experience']) ?> Years</td>
                        <td><?= $row['applied_date'] ?></td>
                        <td>
                            <?php
                            $status = strtolower($row['status'] ?? 'pending');
                            ?>
                            <span class="status <?= $status ?>">
                                <?= $row['status'] ?? 'Pending' ?>
                            </span>
                        </td>
                        <td>
                            <a href="view-application.php?id=<?= $row['id'] ?>">
                                <button class="view-btn">View Details</button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="detailModal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">✖</span>
            <h3>Application Details</h3>
            <p><strong>User ID:</strong> <span id="d_user"></span></p>
            <p><strong>Company:</strong> <span id="d_company"></span></p>
            <p><strong>Position:</strong> <span id="d_position"></span></p>
            <p><strong>Applied Date:</strong> <span id="d_date"></span></p>
            <p><strong>Status:</strong> <span id="d_status"></span></p>
            <p><strong>Created At:</strong> <span id="d_created"></span></p>
        </div>
    </div>

    <script>
        function viewDetails(user, company, position, date, status, created) {
            document.getElementById("d_user").innerText = user;
            document.getElementById("d_company").innerText = company;
            document.getElementById("d_position").innerText = position;
            document.getElementById("d_date").innerText = date;
            document.getElementById("d_status").innerText = status;
            document.getElementById("d_created").innerText = created;

            document.getElementById("detailModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("detailModal").style.display = "none";
        }

        function searchTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("#appTable tr");

            rows.forEach((row, index) => {
                if (index === 0) return;
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(input) ? "" : "none";
            });
        }
    </script>

</body>

</html>