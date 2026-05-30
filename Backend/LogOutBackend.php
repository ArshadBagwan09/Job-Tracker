<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Logout - CareerHub</title>

<!-- Auto redirect -->
<meta http-equiv="refresh" content="3;url=../login.php">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #eef3ff, #f8fbff);
}

/* Card */
.logout-card {
    background: white;
    padding: 40px 35px;
    border-radius: 20px;
    text-align: center;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 20px 50px rgba(13, 110, 253, 0.1);
    animation: fadeIn 0.6s ease;
}

/* Heading */
.logout-card h2 {
    color: #0d6efd;
    margin-bottom: 10px;
}

/* Text */
.logout-card p {
    color: #555;
    font-size: 14px;
    margin-bottom: 25px;
}

/* Button */
.logout-card a {
    display: inline-block;
    text-decoration: none;
    padding: 10px 22px;
    border-radius: 10px;
    background: linear-gradient(135deg, #0d6efd, #6610f2);
    color: white;
    font-weight: 500;
    transition: 0.3s;
}

/* Hover */
.logout-card a:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(13, 110, 253, 0.3);
}

/* Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 480px) {
    .logout-card {
        padding: 30px 20px;
    }

    .logout-card h2 {
        font-size: 20px;
    }
}
</style>

</head>

<body>

<div class="logout-card">
    <h2>👋 Logged Out Successfully</h2>
    <p>Thank you for using CareerHub.<br>Redirecting to login page...</p>
    <a href="../login.php">Go to Login</a>
</div>

</body>
</html>