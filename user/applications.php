<?php
session_start();
require_once "../Backend/DBConnection.php";

use Backend\DBConnection;

$conn = DBConnection::getConnection();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// DELETE
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $delete = $conn->prepare("DELETE FROM job_applications WHERE id=? AND user_id=?");
    $delete->execute([$delete_id, $user_id]);

    header("Location: applications.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM job_applications WHERE user_id=? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>My Applications</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Style/navBarStyle.css">
    <link rel="stylesheet" href="../Style/footerStyle.css">
    <style>
        /* RESET */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #eef2ff, #f8fbff);
        }

        /* CONTAINER */
        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        /* TITLE */
        .container h2 {
            text-align: center;
            color: #0d6efd;
            margin-bottom: 25px;
        }

        /* TABLE */
        .table-box {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        /* DESKTOP TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #111827;
            color: #fff;
            padding: 14px;
        }

        td {
            padding: 12px;
            text-align: center;
        }

        tr {
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f9f9f9;
        }

        /* STATUS */
        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .applied {
            background: #d1e7dd;
            color: #0f5132;
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

        /* BUTTONS */
        .actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .edit-btn {
            background: #0d6efd;
            color: #fff;
            padding: 6px 10px;
            border-radius: 6px;
            text-decoration: none;
        }

        .delete-btn {
            background: #dc3545;
            color: #fff;
            padding: 6px 10px;
            border-radius: 6px;
            text-decoration: none;
        }

        /* EMPTY STATE */
        .empty {
            text-align: center;
            padding: 40px;
            color: #777;
        }

        /* MOBILE VIEW */
        @media(max-width:768px) {

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            thead {
                display: none;
            }

            tr {
                background: #fff;
                margin-bottom: 15px;
                border-radius: 12px;
                padding: 10px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            }

            td {
                text-align: left;
                padding: 10px;
                position: relative;
            }

            td::before {
                content: attr(data-label);
                font-weight: 600;
                display: block;
                color: #555;
                margin-bottom: 3px;
            }

            .actions {
                justify-content: flex-start;
            }

        }
    </style>
</head>

<body>

    <?php include "../navBar.html"; ?>

    <div class="container">
        <h2>📄 My Applications</h2>

        <?php if (empty($applications)): ?>
            <div class="table-box">
                <div class="empty">
                    🚀 No applications yet<br><br>
                    <a href="addJob.php" class="edit-btn">+ Add Job</a>
                </div>
            </div>
        <?php else: ?>

            <div class="table-box">
                <table>
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Position</th>
                            <th>Experience</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($applications as $row): ?>
                            <tr>
                                <td data-label="Company"><?= htmlspecialchars($row['company_name']) ?></td>
                                <td data-label="Position"><?= htmlspecialchars($row['position']) ?></td>
                                <td data-label="Experience"><?= htmlspecialchars($row['experience'] ?? 'N/A') ?></td>
                                <td data-label="Date"><?= $row['applied_date'] ?></td>

                                <td data-label="Status">
                                    <?php $status = strtolower($row['status'] ?? 'pending'); ?>
                                    <span class="status <?= $status ?>">
                                        <?= $row['status'] ?? 'Pending' ?>
                                    </span>
                                </td>

                                <td data-label="Action" class="actions">
                                    <a href="edit-application.php?id=<?= $row['id'] ?>" class="edit-btn">Edit</a>
                                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this?')"
                                        class="delete-btn">Delete</a>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

        <?php endif; ?>

    </div>

    <!-- FOOTER WRAPPER (ISOLATION FIX) -->
    <div class="footer-wrapper">
        <?php include "../footer.html"; ?>
    </div>

</body>

</html>