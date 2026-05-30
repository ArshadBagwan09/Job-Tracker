<?php
require_once "../Backend/DBConnection.php";
use Backend\DBConnection;

$conn = DBConnection::getConnection();

// FETCH USERS
$stmt = $conn->prepare("
    SELECT id, full_name, email, created_at 
    FROM users 
    ORDER BY id DESC
");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Users</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#f4f7fb;
}

/* SIDEBAR */
.sidebar{
    width:240px;
    height:100vh;
    background:#111827;
    color:#fff;
    position:fixed;
    left:0;
    top:0;
    padding:20px;
    transition:0.3s;
    z-index:1000;
}

.sidebar-header{
    display:flex;
    justify-content:space-between;
    margin-bottom:25px;
}

.sidebar a{
    display:block;
    padding:12px;
    margin-bottom:10px;
    color:#cbd5e1;
    text-decoration:none;
    border-radius:8px;
}

.sidebar a:hover,
.sidebar a.active{
    background:#1f2937;
    color:white;
}

.close-btn{
    display:none;
    cursor:pointer;
}

/* MAIN */
.main{
    margin-left:240px;
    padding:20px;
}

/* TOPBAR */
.topbar{
    background:white;
    padding:15px 20px;
    border-radius:10px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
    margin-bottom:20px;
}

.menu-toggle{
    display:none;
    font-size:22px;
    background:none;
    border:none;
}

/* HEADER */
.header{
    background:linear-gradient(135deg,#0d6efd,#6610f2);
    color:white;
    padding:25px;
    border-radius:15px;
    margin-bottom:20px;
}

.header h2{
    margin-bottom:5px;
}

/* SEARCH */
.search-box{
    margin-bottom:20px;
}

.search-box input{
    width:100%;
    padding:10px;
    border-radius:8px;
    border:1px solid #ddd;
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:20px;
}

/* CARD */
.card{
    background:white;
    padding:20px;
    border-radius:15px;
    box-shadow:0 8px 20px rgba(0,0,0,0.05);
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px);
}

.name{
    font-size:18px;
    font-weight:600;
    color:#111827;
}

.email{
    font-size:14px;
    color:#555;
    margin-top:5px;
}

.date{
    font-size:12px;
    color:#999;
    margin-top:10px;
}

.badge{
    display:inline-block;
    background:#dbeafe;
    color:#1e40af;
    padding:4px 10px;
    border-radius:10px;
    font-size:12px;
    margin-top:10px;
}

/* OVERLAY */
#overlay{
    display:none;
    position:fixed;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.4);
    top:0;
    left:0;
    z-index:900;
}

/* PDF BUTTON */
.pdf-btn{
    position:fixed;
    bottom:25px;
    right:25px;
    background:#ef4444;
    color:white;
    border:none;
    width:55px;
    height:55px;
    border-radius:50%;
    font-size:22px;
    cursor:pointer;
    box-shadow:0 8px 20px rgba(0,0,0,0.2);
    transition:0.3s;
    z-index:1000;
}

.pdf-btn:hover{
    transform:scale(1.1);
    background:#dc2626;
}

@media print{

    .sidebar,
    .topbar,
    .search-box,
    .pdf-btn{
        display:none;
    }

    body{
        background:white;
    }

    .header{
        background: linear-gradient(135deg,#0d6efd,#6610f2) !important;
        color: white !important;
        padding:15px;
        text-align:center;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .main{
        margin:0;
        padding:0;
    }

    .card{
        box-shadow:none;
        border:1px solid #ddd;
        margin-bottom:10px;
    }
}

/* MOBILE */
@media(max-width:768px){

    .sidebar{
        left:-260px;
    }

    .sidebar.active{
        left:0;
    }

    .main{
        margin-left:0;
    }

    .menu-toggle{
        display:block;
    }

    .close-btn{
        display:block;
    }

    #overlay.active{
        display:block;
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
        <h3>👤 Users</h3>
    </div>

    <!-- HEADER -->
    <div class="header">
        <h2>Registered Users</h2>
        <p>All users who signed up on platform</p>
    </div>

    <!-- SEARCH -->
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search by name or email...">
    </div>

    <!-- GRID -->
    <div class="grid" id="userGrid">

    <?php if(!empty($users)): ?>

        <?php foreach($users as $u): ?>

        <div class="card"
            data-name="<?= strtolower($u['full_name']) ?>"
            data-email="<?= strtolower($u['email']) ?>">

            <div class="name"><?= htmlspecialchars($u['full_name']) ?></div>

            <div class="email">📧 <?= htmlspecialchars($u['email']) ?></div>

            <div class="date">📅 Joined: <?= date("d M Y", strtotime($u['created_at'])) ?></div>

            <div class="badge">User ID: <?= $u['id'] ?></div>

        </div>

        <?php endforeach; ?>

    <?php else: ?>

        <p>No Users Found</p>

    <?php endif; ?>

    </div>

</div>

<!-- PDF BUTTON -->
<button class="pdf-btn" onclick="generatePDF()">📄</button>

<script>
function toggleSidebar(){
    document.getElementById("sidebar").classList.toggle("active");
    document.getElementById("overlay").classList.toggle("active");
}
</script>

<script>
const searchInput = document.getElementById("searchInput");

searchInput.addEventListener("keyup", function(){
    let value = this.value.toLowerCase();
    let cards = document.querySelectorAll(".card");

    cards.forEach(card=>{
        let name = card.dataset.name;
        let email = card.dataset.email;

        if(name.includes(value) || email.includes(value)){
            card.style.display="block";
        } else {
            card.style.display="none";
        }
    });
});
</script>

<script>
function generatePDF(){
    window.print();
}
</script>

</body>
</html>