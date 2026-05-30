<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit();
}

if ($_SESSION['user_role'] !== 'user') {
  header("Location: ../login.php");
  exit();
}

require_once __DIR__ . "/../Backend/DBConnection.php";
use Backend\DBConnection;

$conn = DBConnection::getConnection();
$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

/* ================= FETCH DATA ================= */

// Total Applied
$stmt = $conn->prepare("SELECT COUNT(*) FROM job_applications WHERE user_id = ?");
$stmt->execute([$userId]);
$totalApplied = $stmt->fetchColumn();

// Interviews
$stmt = $conn->prepare("SELECT COUNT(*) FROM job_applications WHERE user_id = ? AND status = 'Interview'");
$stmt->execute([$userId]);
$totalInterview = $stmt->fetchColumn();

// Rejected
$stmt = $conn->prepare("SELECT COUNT(*) FROM job_applications WHERE user_id = ? AND status = 'Rejected'");
$stmt->execute([$userId]);
$totalRejected = $stmt->fetchColumn();

// Offers
$stmt = $conn->prepare("SELECT COUNT(*) FROM job_applications WHERE user_id = ? AND status = 'Offer'");
$stmt->execute([$userId]);
$totalOffer = $stmt->fetchColumn();

// Success Rate
$successRate = $totalApplied > 0 ? round(($totalOffer / $totalApplied) * 100) : 0;

// Recent Applications
$stmt = $conn->prepare("SELECT company_name, position, applied_date, status 
                        FROM job_applications 
                        WHERE user_id = ?
                        ORDER BY applied_date DESC
                        LIMIT 5");
$stmt->execute([$userId]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard - CareerHub</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../Style/navBarStyle.css">
  <link rel="stylesheet" href="../Style/footerStyle.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');    

    :root {
      --primary: #2563eb;
      --secondary: #7c3aed;
      --gradient: linear-gradient(135deg, #2563eb, #7c3aed);
      --bg: #f1f5f9;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: linear-gradient(135deg, #e0e7ff, #f8fafc);
      color: #1e293b;
    }

    /* HEADER */
    .header {
      padding: clamp(25px, 5vw, 60px);
      text-align: center;
    }

    .header h1 {
      font-size: clamp(24px, 4vw, 34px);
      font-weight: 600;
      background: var(--gradient);
      -webkit-background-clip: text;
      color: transparent;
    }

    .header p {
      margin-top: 8px;
      color: #64748b;
      font-size: clamp(13px, 2vw, 16px);
    }

    .welcome-icon {
      font-size: 22px;
      margin-left: 8px;
      color: #2563eb;
      vertical-align: middle;
      transition: 0.3s;
    }

    .welcome-icon:hover {
      color: #7c3aed;
      transform: scale(1.1);
    }

    /* SECTION */
    .section {
      padding: clamp(20px, 5vw, 60px);
    }

    .section h2 {
      font-size: clamp(18px, 3vw, 24px);
      margin-bottom: 25px;
    }

    /* ================= STATS ================= */

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }

    .stat-card {
      background: linear-gradient(135deg, #ffffff, #f1f5ff);
      border: 1px solid rgba(0, 0, 0, 0.05);
      backdrop-filter: blur(12px);
      border-radius: 18px;
      padding: 25px;
      text-align: center;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
      transition: 0.3s;
      position: relative;
    }

    .stat-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
    }

    .stat-card i {
      font-size: 30px;
      margin-bottom: 10px;
      background: linear-gradient(135deg, #2563eb, #7c3aed);
      color: #fff;
      padding: 12px;
      border-radius: 50%;
    }

    .stat-card h2 {
      font-size: 26px;
      color: var(--primary);
    }

    .stat-card p {
      color: #555;
      font-size: 14px;
    }

    /* ================= ACTION CARDS ================= */

    .action-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 25px;
    }

    .action-card {
      background: #fff;
      border-radius: 20px;
      padding: 28px 22px;
      text-align: center;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
      transition: 0.4s;
      position: relative;
      overflow: hidden;
      z-index: 1;
    }

    /* 🔵 Circle (initial state) */
    .action-card::before {
      content: "";
      position: absolute;
      bottom: -80px;
      right: -80px;
      width: 160px;
      height: 160px;
      background: linear-gradient(135deg, #2563eb, #7c3aed);
      border-radius: 50%;
      transition: 0.6s ease;
      z-index: -1;
    }

    /* ✨ Hover → circle expands to full */
    .action-card:hover::before {
      width: 400%;
      height: 400%;
      bottom: -150%;
      right: -150%;
    }

    /* Text color change on hover */
    .action-card:hover {
      color: #fff;
      transform: translateY(-10px) scale(1.03);
    }

    /* Icon */
    .action-card i {
      font-size: 30px;
      background: #eef2ff;
      padding: 14px;
      border-radius: 50%;
      margin-bottom: 14px;
      color: #2563eb;
      transition: 0.4s;
    }

    /* Icon hover */
    .action-card:hover i {
      background: #fff;
      color: #2563eb;
    }

    /* Text fix */

    .action-card h3 {
      font-size: clamp(16px, 2vw, 20px);
      margin-bottom: 10px;
      line-height: 1.3;
    }

    .action-card p {
      font-size: clamp(13px, 1.5vw, 15px);
      line-height: 1.5;
      margin-bottom: 18px;

      display: -webkit-box;
      -webkit-line-clamp: 3;
      /* 👈 max 3 lines */
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .action-card h3,
    .action-card p {
      transition: 0.3s;
    }

    /* Button */
    .action-card button {
      padding: 10px 20px;
      border: none;
      border-radius: 25px;
      background: linear-gradient(135deg, #2563eb, #7c3aed);
      color: #fff;
      cursor: pointer;
      transition: 0.3s;
    }

    /* Button hover */
    .action-card:hover button {
      background: #fff;
      color: #2563eb;
    }

    /* ================= TABLE ================= */

    .table-box {
      background: #fff;
      padding: 20px;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
    }

    .table-responsive {
      overflow-x: auto;
    }

    table {
      width: 100%;
      min-width: 600px;
      border-collapse: collapse;
    }

    th,
    td {
      padding: 12px;
      font-size: clamp(12px, 1.5vw, 14px);
    }

    th {
      background: #eef2ff;
      position: sticky;
      top: 0;
    }

    tr {
      transition: 0.2s;
      text-align: center;
    }

    tr:hover {
      background: #f8fbff;
    }

    /* BADGES */

    .badge {
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
    }

    .Interview {
      background: #fff3cd;
      color: #856404;
    }

    .Rejected {
      background: #f8d7da;
      color: #721c24;
    }

    .Offer {
      background: #d4edda;
      color: #155724;
    }

    .Applied {
      background: #dbeafe;
      color: #1e40af;
    }


    /* ================= ANIMATIONS ================= */

    .stat-card,
    .action-card {
      animation: fadeIn 0.5s ease forwards;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Tablet */
    @media(max-width:992px) {
      .action-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    /* ================= MOBILE ================= */

    @media(max-width:600px) {

      .header {
        padding: 20px;
      }

      .section {
        padding: 20px;
      }

      .action-card {
        padding: 18px;
      }

      .action-grid {
        grid-template-columns: 1fr;
      }

      .stat-card {
        padding: 18px;
      }
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <?php include "../navBar.html"; ?>

  <!-- Header -->
  <div class="header">
    <h1>Welcome, <?php echo htmlspecialchars($userName); ?> <span class="welcome-icon">👋</span></h1>
    <p>Track your job applications and career progress.</p>
  </div>

  <section class="section">
    <h2>Application Overview</h2>

    <div class="stats-grid">
      <div class="stat-card">
        <i class="fas fa-paper-plane"></i>
        <h2><?php echo $totalApplied; ?></h2>
        <p>Applied Jobs</p>
      </div>

      <div class="stat-card">
        <i class="fas fa-calendar-check"></i>
        <h2><?php echo $totalInterview; ?></h2>
        <p>Interviews</p>
      </div>

      <div class="stat-card">
        <i class="fas fa-times-circle"></i>
        <h2><?php echo $totalRejected; ?></h2>
        <p>Rejected</p>
      </div>

      <div class="stat-card">
        <i class="fas fa-handshake"></i>
        <h2><?php echo $totalOffer; ?></h2>
        <p>Offers</p>
      </div>

      <!-- <div class="stat-card">
                <i class="fas fa-chart-line"></i>
                <h2><?php echo $successRate; ?>%</h2>
                <p>Success Rate</p>
            </div> -->
    </div>
  </section>

  <!-- Quick Actions -->
  <section class="section">
    <h2 class="section-title">Quick Actions</h2>

    <div class="action-grid">

      <!-- Applications -->
      <div class="action-card">
        <i class="fas fa-briefcase"></i>
        <h3>My Applications</h3>
        <p>View, track and manage all the jobs you have applied for.</p>
        <button onclick="goPage('applications.php')">View Application</button>
      </div>

      <!-- Company Card -->
      <div class="action-card">
        <i class="fas fa-building"></i>
        <h3>Company Details</h3>
        <p>Explore company information and hiring details.</p>
        <button onclick="goPage('../user/companyDetails.php')">View Details</button>
      </div>

      <!-- Browse Jobs -->
      <div class="action-card">
        <i class="fas fa-search"></i>
        <h3>Browse Jobs</h3>
        <p>Explore new job opportunities and apply instantly.</p>
        <button onclick="goPage('../user/jobs.php')">Explore Jobs</button>
      </div>

      <!-- Interviews -->
      <div class="action-card">
        <i class="fas fa-calendar-check"></i>
        <h3>Interviews</h3>
        <p>Check your upcoming interviews and schedule details.</p>
        <button onclick="goPage('interviews.php')">View Interviews</button>
      </div>

      <!-- Resume -->
      <div class="action-card">
        <i class="fas fa-file-alt"></i>
        <h3>My Resume</h3>
        <p>Upload, update or manage your resume anytime.</p>
        <button onclick="goPage('resume.php')">Manage Resume</button>
      </div>

      <!-- Logout -->
      <div class="action-card">
        <i class="fas fa-sign-out-alt"></i>
        <h3>Logout</h3>
        <p>Securely logout from your account.</p>
        <button onclick="logout()">Logout</button>
      </div>
    </div>
  </section>

  <section class="section">
    <h2>Recent Applications</h2>

    <div class="table-box">
      <div class="table-responsive">
        <table>
          <tr>
            <th>Company</th>
            <th>Position</th>
            <th>Date</th>
            <th>Status</th>
          </tr>

          <?php if ($applications): ?>
            <?php foreach ($applications as $app): ?>
              <tr>
                <td><?php echo htmlspecialchars($app['company_name']); ?></td>
                <td><?php echo htmlspecialchars($app['position']); ?></td>
                <td><?php echo date("d M Y", strtotime($app['applied_date'])); ?></td>
                <td>
                  <span class="badge <?php echo $app['status']; ?>">
                    <?php echo htmlspecialchars($app['status']); ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4">No applications found.</td>
            </tr>
          <?php endif; ?>

        </table>
      </div>
    </div>
  </section>

    <?php include "../footer.html"; ?>

  <script>
    function goPage(page) {
      window.location.href = page;
    }

    function logout() {
      if (confirm("Are you sure you want to logout?")) {
        window.location.href = "../Backend/LogOutBackend.php";
      }
    }
  </script>

</body>

</html>