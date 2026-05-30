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

/* FETCH ONLY USER INTERVIEWS (DYNAMIC) */
$stmt = $conn->prepare("
    SELECT * FROM interviews 
    WHERE user_id = ?
    ORDER BY interview_date ASC
");

$stmt->execute([$user_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
  <title>Interview Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../Style/navBarStyle.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../Style/footerStyle.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f3f4f6;
      margin: 0;
    }

    /* NAVBAR */
    .navbar {
      background: white;
      padding: 18px 60px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    .navbar h2 {
      color: #2563eb;
      margin: 0;
    }

    .nav-links {
      display: flex;
      gap: 25px;
    }

    .nav-links a {
      text-decoration: none;
      color: #333;
      font-weight: 500;
    }

    /* CONTAINER */
    .container {
      padding: 40px 80px;
    }

    h1 {
      margin-bottom: 25px;
    }

    /* SUCCESS MESSAGE */
    .success-msg {
      background: #d1fae5;
      color: #065f46;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: bold;
    }

    /* TABLE CARD */
    .table-box {
    background: white;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
}

    .table-wrapper {
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 750px;
    }

    th {
      background: #2563eb;
      color: white;
      padding: 14px;
      text-align: left;
    }

    td {
      padding: 14px;
      border-bottom: 1px solid #eee;
    }

    tr:hover {
    background: #f9fbff;
    transition: 0.3s;
}

    /* STATUS BADGES */
    .status {
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 14px;
    }

    .scheduled {
      background: #dbeafe;
      color: #1e40af;
    }

    .completed {
      background: #d1fae5;
      color: #065f46;
    }

   .today-badge {
    background: #facc15;
    color: #78350f;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 14px;
}

    .cancelled {
      background: #fee2e2;
      color: #991b1b;
    }

    /* BUTTONS */
    .btn {
      padding: 6px 14px;
      text-decoration: none;
      border-radius: 20px;
      color: white;
      font-size: 13px;
      display: inline-block;
      transition: 0.3s;
    }

.btn:hover {
    transform: scale(1.05);
}

    /* RESPONSIVE */
    @media (max-width: 992px) {
      .container {
        padding: 30px 40px;
      }
    }

    @media (max-width: 768px) {

      .navbar {
        padding: 15px 20px;
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }

      .nav-links {
        flex-direction: column;
        width: 100%;
        gap: 10px;
      }

      .container {
        padding: 20px;
      }

      h1 {
        font-size: 22px;
      }
    }
  </style>
</head>

<body>

  <?php include "../navBar.html"; ?>

  <div class="container">
    <h1>Interview Details</h1>

    <div class="table-box">
      <div class="table-wrapper">
        <table>
          <tr>
            <th>ID</th>
            <th>Applicant Name</th>
            <th>Position</th>
            <th>Date</th>
            <th>Time</th>
            <th>Interviewer</th>
            <th>Status</th>
          </tr>

          <?php foreach ($result as $row) { ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><?php echo $row['applicant_name']; ?></td>
              <td><?php echo $row['position']; ?></td>
              <td><?php echo $row['interview_date']; ?></td>
              <td><?php echo $row['interview_time']; ?></td>
              <td><?php echo $row['interviewer']; ?></td>
              <td>
                <?php
$today = date('Y-m-d');

$statusText = "";
$statusClass = "";

// Default status
if ($row['status'] == "Scheduled") {
    $statusText = "Upcoming";
    $statusClass = "scheduled";
} elseif ($row['status'] == "Completed") {
    $statusText = "Completed";
    $statusClass = "completed";
} else {
    $statusText = "Cancelled";
    $statusClass = "cancelled";
}

// ✅ If TODAY → override text
if ($row['interview_date'] == $today) {
    // $statusText = "<span class='today-badge'>🔥 Today</span>";
    $statusText = "🔥 Today";
    $statusClass = "today-badge";
}



if ($row['interview_date'] < $today && $row['status'] == "Scheduled") {
    $statusText = "Missed ❌";
    $statusClass = "cancelled";
}

echo "<span class='status $statusClass'>$statusText</span>";
?>
              </td>
            </tr>
          <?php } ?>
        </table>
        <?php if (empty($result)) { ?>
    <p style="text-align:center;padding:20px;color:#888;">
        No interviews scheduled yet 😔
    </p>
<?php } ?>
      </div>
    </div>
  </div>

  <?php include "../footer.html"; ?>

  <script>
    // Auto hide success message
    setTimeout(function () {
      var msg = document.getElementById("successMsg");
      if (msg) {
        msg.style.display = "none";
      }
    }, 3000);
  </script>

</body>

</html>