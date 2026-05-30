<?php
session_start();
require_once "../Backend/DBConnection.php";

use Backend\DBConnection;

$conn = DBConnection::getConnection();

if (!isset($_SESSION['company_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: applicants.php");
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT job_applications.*, 
           users.full_name, 
           users.email, 
           users.resume
    FROM job_applications
    LEFT JOIN users ON job_applications.user_id = users.id
    WHERE job_applications.id = ?
");
$stmt->execute([$id]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$app) {
    die("Application not found");
}

// Handle Status Update
$showPopup = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 🔥 Interview Schedule Form Submit
    if (isset($_POST['scheduleInterview'])) {

        $interviewer = $_POST['interviewer'];
        $date = $_POST['interview_date'];
        $time = $_POST['interview_time'];

        // ✅ Interview table me insert
        $insert = $conn->prepare("
    INSERT INTO interviews 
    (user_id, applicant_name, position, interview_date, interview_time, interviewer, status)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$insert->execute([
    $app['user_id'],   // 🔥 IMPORTANT
    $app['full_name'],
    $app['position'],
    $date,
    $time,
    $interviewer,
    "Scheduled"
]);

        // ✅ Application status update bhi
        $update = $conn->prepare("UPDATE job_applications SET status = ? WHERE id = ?");
        $update->execute(["Interview", $id]);

        header("Location: applicants.php?msg=interview_scheduled");
        exit;
    }

    // 🔥 Normal Status Update
    $newStatus = $_POST['status'];

    if ($newStatus === "Interview") {
        $showPopup = true;
    } else {
        $update = $conn->prepare("UPDATE job_applications SET status = ? WHERE id = ?");
        $update->execute([$newStatus, $id]);

        header("Location: applicants.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Application Details</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
body{
    font-family:'Poppins',sans-serif;
    background:linear-gradient(135deg,#eef3ff,#f8fbff);
    padding:40px;
    margin:0;
}

.card{
    max-width:750px;
    margin:auto;
    background:white;
    padding:35px;
    border-radius:18px;
    box-shadow:0 20px 40px rgba(13,110,253,0.08);
}

h2{
    color:#0d6efd;
    margin-bottom:25px;
    font-size:26px;
    font-weight:600;
}

.info{
    margin-bottom:15px;
    font-size:15px;
}

label{
    font-weight:600;
    display:block;
    margin-bottom:5px;
}

.field{
    margin-left: 5%;
}

select{
    width:100%;
    padding:12px;
    margin-top:10px;
    border-radius:10px;
    border:1px solid #cbd5e1;
    outline:none;
    transition:0.3s;
}

select:focus{
    border-color:#0d6efd;
    box-shadow:0 0 0 3px rgba(13,110,253,0.15);
}

/* Resume Box */
.resume-wrapper{
    margin-top:15px;
    width:100%;
    overflow:hidden;
    border-radius:12px;
}

.resume-wrapper iframe{
    width:100%;
    height:500px;
}

/* Buttons */
.button-group{
    display:flex;
    gap:15px;
    flex-wrap:wrap;
    margin-top:25px;
}

button{
    padding:10px 20px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:500;
    transition:0.3s;
}

.update-btn{
    background:#0d6efd;
    color:white;
}

.update-btn:hover{
    background:#0b5ed7;
}

.back-btn{
    background:#e2e8f0;
}

.back-btn:hover{
    background:#cbd5e1;
}

.download-btn{
    background:#198754;
    color:white;
}

.download-btn:hover{
    background:#157347;
}

/* POPUP BACKGROUND */
.popup {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.55);
    backdrop-filter: blur(5px);
    justify-content: center;
    align-items: center;
    z-index: 999;
}

/* POPUP BOX */
.popup-box {
    background: #fff;
    width: 90%;
    max-width: 420px;
    padding: 30px 25px;
    border-radius: 18px;
    position: relative;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    animation: popupFade 0.3s ease;
}

/* ANIMATION */
@keyframes popupFade {
    from {
        transform: translateY(-20px) scale(0.95);
        opacity: 0;
    }
    to {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
}

/* TITLE */
.popup-box h2 {
    margin-bottom: 20px;
    color: #0d6efd;
    font-weight: 600;
    text-align: center;
}

/* CLOSE BUTTON */
.close-btn {
    position: absolute;
    top: 12px;
    right: 15px;
    font-size: 22px;
    cursor: pointer;
    color: #999;
}

.close-btn:hover {
    color: #000;
}

/* FORM LABEL */
.popup-box label {
    font-size: 14px;
    font-weight: 500;
    margin-top: 10px;
    display: block;
}

/* INPUTS */
.popup-box input {
    width: 95%;
    padding: 10px 12px;
    margin-top: 6px;
    border-radius: 8px;
    border: 1px solid #ddd;
    outline: none;
    transition: 0.3s;
}

/* INPUT FOCUS */
.popup-box input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

/* BUTTON */
.popup-box button {
    width: 100%;
    margin-top: 18px;
    padding: 12px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #0d6efd, #6610f2);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

/* BUTTON HOVER */
.popup-box button:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3);
}

/* Responsive */
@media (max-width:768px){

    body{
        padding:15px;
    }

    .card{
        padding:20px;
    }

    h2{
        font-size:20px;
        text-align:center;
    }

    .info{
        font-size:14px;
    }

    .resume-wrapper iframe{
        height:350px;
    }

    .button-group{
        flex-direction:column;
    }

    button{
        width:100%;
    }
}

@media (max-width:480px){

    .resume-wrapper iframe{
        height:250px;
    }
}
</style>
</head>
<body>

<div class="card">
<h2>Application Details</h2>

<div class="info"><label>User ID:</label> 
    <p class="field">
        <?= $app['user_id'] ?></div>
    </p>

<div class="info">
    <label>Name:</label> 
    <p class="field">
        <?= htmlspecialchars($app['full_name'] ?? 'N/A') ?>
    </p>
</div>

<div class="info">
    <label>Email:</label> 
    <p class="field">
        <?= htmlspecialchars($app['email'] ?? 'N/A') ?>
    </p>
</div>

<div class="info">
    <label>Phone:</label> 
    <p class="field">
        <?= htmlspecialchars($app['phone'] ?? 'N/A') ?>
    </p>
</div>

<div class="info">
    <label>Company:</label> 
    <p class="field">
        <?= htmlspecialchars($app['company_name']) ?>
    </p>
</div>

<div class="info">
    <label>Position:</label> 
    <p class="field">
        <?= htmlspecialchars($app['position']) ?>
    </p>
</div>

<div class="info">
    <label>Experience:</label> 
    <p class="field">
        <?= htmlspecialchars($app['experience'] ?? 'N/A') ?> Years
    </p>
</div>

<div class="info">
    <label>Applied Date:</label> 
    <p class="field">
        <?= $app['applied_date'] ?>
    </p>
</div>

<div class="info">
    <label>Cover Letter:</label> 
    <p class="field">
        <?= $app['cover_letter'] ?>
    </p>
</div>

<form method="POST">

<input type="hidden" name="app_id" value="<?= $app['id'] ?>">

<label>Update Status:</label>

<select name="status" required>
    <option value="Pending" <?= $app['status']=="Pending"?'selected':'' ?>>Pending</option>
    <option value="Applied" <?= $app['status']=="Applied"?'selected':'' ?>>Applied</option>
    <option value="Interview" <?= $app['status']=="Interview"?'selected':'' ?>>Interview</option>
    <option value="Rejected" <?= $app['status']=="Rejected"?'selected':'' ?>>Rejected</option>
    <option value="Offer" <?= $app['status']=="Offer"?'selected':'' ?>>Offer</option>
</select>

<div class="info">
<label>Resume:</label><br><br>

<?php if (!empty($app['resume'])): ?>

    <div class="resume-wrapper">
    <iframe 
        src="../uploads/<?= htmlspecialchars($app['resume']) ?>" 
        width="100%" 
        height="500px"
        style="border:1px solid #ddd;border-radius:8px;">
    </iframe>
</div>

    <br><br>

    <button type="button" class="download-btn"
    onclick="window.location.href='../uploads/<?= htmlspecialchars($app['resume']) ?>'">
    Download Resume
</button>

<?php else: ?>

    <p style="color:red;font-weight:500;">
        ❌ User has not uploaded resume.
    </p>

<?php endif; ?>

</div>

<div class="button-group">

    <button type="submit" class="update-btn">
        Update Status
    </button>

    <button type="button" class="back-btn"
        onclick="window.location.href='applicants.php'">
        Back
    </button>

</div>

</form>

<!-- Interview Popup -->
<div id="interviewPopup" class="popup">
    <div class="popup-box">

        <span class="close-btn" onclick="closePopup()">×</span>

        <h2>Schedule Interview</h2>

        <form method="POST">
            <input type="hidden" name="status" value="Interview">

            <label>Interviewer Name</label>
            <input type="text" name="interviewer" required>

            <label>Interview Date</label>
            <input type="date" name="interview_date" required>

            <label>Interview Time</label>
            <input type="time" name="interview_time" required>

            <button type="submit" name="scheduleInterview">
    Save Interview
</button>
        </form>

    </div>
</div>

</div>

<script>
document.querySelector("form").addEventListener("submit", function(e) {

    let status = document.querySelector("select[name='status']").value;

    if (status === "Interview") {
        e.preventDefault(); // form submit rokna
        document.getElementById("interviewPopup").style.display = "flex";
    }
});

function closePopup(){
    document.getElementById("interviewPopup").style.display = "none";
}
</script>

<?php if($showPopup): ?>
<script>
    window.onload = function() {
        document.getElementById("interviewPopup").style.display = "flex";
    }
</script>
<?php endif; ?>

<script>
function handleStatusChange(val) {
    if (val === "Interview") {
        document.getElementById("interviewPopup").style.display = "flex";
    }
}

function closePopup() {
    document.getElementById("interviewPopup").style.display = "none";
}
</script>

</body>
</html>