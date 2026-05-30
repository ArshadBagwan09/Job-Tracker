<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "jobapptracker";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <title>Applicants - CareerHub Admin</title>

  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f5f7fb;
      color: #333;
    }

    /* Navbar */
    .navbar {
      background: #ffffff;
      padding: 18px 8%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .logo {
      font-size: 22px;
      font-weight: bold;
      color: #2563eb;
    }

    .nav-links a {
      margin-left: 20px;
      text-decoration: none;
      color: #444;
      font-weight: 500;
    }

    .nav-links a:hover {
      color: #2563eb;
    }

    /* Page Header */
    .page-header {
      padding: 40px 8% 20px;
    }

    .page-header h1 {
      margin: 0;
    }

    /* Table Section */
    .table-section {
      padding: 20px 8% 60px;
    }

    .table-container {
      background: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    thead {
      background: #2563eb;
      color: white;
    }

    th,
    td {
      padding: 14px;
      text-align: left;
    }

    th {
      font-weight: 600;
    }

    tbody tr {
      border-bottom: 1px solid #eee;
    }

    tbody tr:hover {
      background-color: #f1f5ff;
    }

    /* Status Badges */
    .status {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 500;
    }

    .selected {
      background-color: #d1fae5;
      color: #065f46;
    }

    .pending {
      background-color: #fef3c7;
      color: #92400e;
    }

    .rejected {
      background-color: #fee2e2;
      color: #991b1b;
    }

    /* Action Buttons */
    .action-btn {
      padding: 6px 12px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
      font-size: 13px;
    }

    .view-btn {
      background: #2563eb;
      color: white;
    }

    .delete-btn {
      background: #ef4444;
      color: white;
    }

    .footer {
      text-align: center;
      padding: 20px;
      background: white;
      color: #777;
      font-size: 14px;
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <div class="navbar">
    <div class="logo">CareerHub Admin</div>
    <div class="nav-links">
      <a href="#">Dashboard</a>
      <a href="#">Post Job</a>
      <a href="#">Applicants</a>
      <a href="#">Logout</a>
    </div>
  </div>

  <!-- Page Header -->
  <div class="page-header">
    <h1>Applicants Details</h1>
  </div>

  <!-- Table Section -->
  <div class="table-section">
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Position</th>
            <th>Experience</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>

        <tbody>

          <?php
          $sql = "
SELECT job_applications.*, users.full_name, users.email 
FROM job_applications
LEFT JOIN users ON job_applications.user_id = users.id
";
          $result = mysqli_query($conn, $sql);

          if (mysqli_num_rows($result) > 0) {
           while ($row = mysqli_fetch_assoc($result)) {

  $statusClass = "";
  if ($row['status'] == "Selected") {
    $statusClass = "selected";
  } elseif ($row['status'] == "Pending") {
    $statusClass = "pending";
  } else {
    $statusClass = "rejected";
  }

  echo "<tr>
    <td>{$row['id']}</td>
    <td>{$row['full_name']}</td>
    <td>{$row['email']}</td>
    <td>{$row['phone']}</td>
    <td>{$row['position']}</td>
    <td>{$row['experience']}</td>
    <td><span class='status $statusClass'>{$row['status']}</span></td>
    <td>
        <button class='action-btn view-btn'>View</button>
        <button class='action-btn delete-btn'>Delete</button>
    </td>
  </tr>";
}
          } else {
            echo "<tr><td colspan='8'>No Applicants Found</td></tr>";
          }

          mysqli_close($conn);
          ?>

        </tbody>
      </table>
    </div>
  </div>

  <div class="footer">
    © 2026 CareerHub Admin Panel
  </div>

</body>

</html>