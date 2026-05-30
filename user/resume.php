<?php
session_start();
require_once "../Backend/DBConnection.php";

use Backend\DBConnection;

$conn = DBConnection::getConnection();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

/* ---------------- UPLOAD ---------------- */
if (isset($_POST['upload'])) {

  if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {

    // Check PDF only
    $fileType = mime_content_type($_FILES['resume']['tmp_name']);
    if ($fileType !== "application/pdf") {
      die("Only PDF files allowed!");
    }

    $fileName = time() . "_" . $_FILES['resume']['name'];
    $targetPath = "../uploads/" . $fileName;

    // Get old resume
    $stmt = $conn->prepare("SELECT resume FROM users WHERE id=?");
    $stmt->execute([$user_id]);
    $oldResume = $stmt->fetchColumn();

    // Delete old file if exists
    if ($oldResume && file_exists("uploads/" . $oldResume)) {
      unlink("uploads/" . $oldResume);
    }

    move_uploaded_file($_FILES['resume']['tmp_name'], $targetPath);

    // Update user table
    $update = $conn->prepare("UPDATE users SET resume=? WHERE id=?");
    $update->execute([$fileName, $user_id]);
  }
}

/* ---------------- DELETE ---------------- */
if (isset($_POST['delete'])) {

  $stmt = $conn->prepare("SELECT resume FROM users WHERE id=?");
  $stmt->execute([$user_id]);
  $file = $stmt->fetchColumn();

  if ($file && file_exists("../uploads/" . $file)) {
    unlink("../uploads/" . $file);
  }

  $update = $conn->prepare("UPDATE users SET resume=NULL WHERE id=?");
  $update->execute([$user_id]);

  // Page refresh after delete
  header("Location: resume.php");
  exit;
}

/* ---------------- FETCH CURRENT RESUME ---------------- */
$stmt = $conn->prepare("SELECT resume FROM users WHERE id=?");
$stmt->execute([$user_id]);
$currentResume = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CareerHub - My Resume</title>
  <link rel="stylesheet" href="../Style/navBarStyle.css">
  <link rel="stylesheet" href="../Style/footerStyle.css">
  <link href="https://fonts.googleapis.com/css2?family=MuseoModerno:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #eef2f7, #f4f6f9);
    }


    /* Header */
    .header {
      text-align: center;
      margin: 40px 15px 20px;
    }

    .header h1 {
      margin: 0;
      font-size: 28px;
    }

    .header p {
      color: gray;
      font-size: 14px;
    }

    /* Container */
    .container {
      width: 90%;
      max-width: 900px;
      margin: auto;
    }

    /* Card */
    .resume-card {
      background: white;
      border-radius: 15px;
      padding: 30px 20px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
      text-align: center;
    }

    /* Upload Section */
    .upload-section {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: center;
      gap: 12px;
      background: #f1f6ff;
      padding: 20px;
      border-radius: 12px;
    }

    .upload-section span {
      font-size: 16px;
      font-weight: 500;
      color: #333;
      width: 100%;
    }

    input[type="file"] {
      display: none;
    }

    .upload-btn {
      padding: 10px 18px;
      border-radius: 20px;
      border: none;
      cursor: pointer;
      background: linear-gradient(to right, #007bff, #0056d2);
      color: white;
      font-weight: 600;
      transition: 0.3s;
    }

    .upload-btn:hover {
      transform: scale(1.05);
    }

    /* Delete Button */
    .delete-btn {
      margin-top: 15px;
      padding: 10px 18px;
      border-radius: 20px;
      border: none;
      cursor: pointer;
      background: linear-gradient(to right, #ff4d4d, #cc0000);
      color: white;
      font-weight: 600;
      transition: 0.3s;
    }

    .delete-btn:hover {
      transform: scale(1.05);
    }

    /* Preview */
    iframe {
      width: 100%;
      height: 500px;
      margin-top: 25px;
      border-radius: 10px;
      border: 1px solid #ddd;
    }

    /* Empty Box */
    .empty-box {
      margin-top: 30px;
      padding: 40px 20px;
      border: 2px dashed #ccc;
      border-radius: 10px;
      color: #888;
      font-size: 15px;
    }

    /* 📱 MOBILE RESPONSIVE */
    @media (max-width: 768px) {

      .header h1 {
        font-size: 22px;
      }

      .resume-card {
        padding: 20px 15px;
      }

      iframe {
        height: 350px;
      }

      .upload-btn,
      .delete-btn {
        width: 100%;
      }
    }

    @media (max-width: 480px) {

      iframe {
        height: 280px;
      }

      .header h1 {
        font-size: 20px;
      }

      .upload-section {
        padding: 15px;
      }
    }
  </style>
</head>

<body>

  <?php include "../navBar.html"; ?>

  <div class="header">
    <h1>My Resume</h1>
    <p>Upload, Preview or Delete your resume</p>
  </div>

  <div class="container">
    <div class="resume-card">

      <form method="POST" enctype="multipart/form-data">

        <div class="upload-section">
          <span>📄 Upload your Resume (PDF)</span>

          <button type="button" class="upload-btn" onclick="document.getElementById('resumeInput').click();">
            Choose File
          </button>

          <button type="submit" name="upload" class="upload-btn">
            Upload Resume
          </button>

          <input type="file" id="resumeInput" name="resume" accept=".pdf" style="display:none;">
        </div>

        <br>



        <?php if (!empty($currentResume)): ?>

          <br><br>
          <h3>Your Current Resume</h3>
          <iframe src="../uploads/<?= htmlspecialchars($currentResume) ?>"></iframe>

          <br><br>
          <button type="submit" name="delete" class="delete-btn" onclick="return confirmDelete()">
            Delete Resume
          </button>

        <?php else: ?>

          <div class="empty-box">
            No Resume Uploaded Yet
          </div>

        <?php endif; ?>

      </form>

    </div>
  </div>

  <script>
    function showFileName() {
      const file = document.getElementById("resumeInput").files[0];
      if (file) {
        document.getElementById("fileName").innerText = "Selected: " + file.name;
      }
    }

    function uploadResume() {
      const file = document.getElementById("resumeInput").files[0];
      if (!file) {
        alert("Please select a file first!");
        return;
      }

      const fileURL = URL.createObjectURL(file);

      document.getElementById("previewContainer").innerHTML =
        `<h3>Preview: ${file.name}</h3>
         <iframe src="${fileURL}"></iframe>`;
    }

    function deleteResume() {
      document.getElementById("previewContainer").innerHTML = "";
      document.getElementById("fileName").innerText = "";
      document.getElementById("resumeInput").value = "";
    }
  </script>

  <script>
    function confirmDelete() {
      return confirm("Are you sure you want to delete your resume?");
    }
  </script>

    <?php include "../footer.html"; ?>

</body>

</html>