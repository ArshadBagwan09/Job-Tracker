<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/DBConnection.php";
use Backend\DBConnection;

header('Content-Type: text/html; charset=UTF-8');

// Sanitize input
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Basic validation
if (empty($email) || empty($password)) {
    $_SESSION['error'] = "Please enter both email and password.";
    header("Location: ../login.php");
    exit();
}

try {

    // 🔥 STATIC ADMIN LOGIN (NO DB REQUIRED)
if ($email === "admin@gmail.com" && $password === "admin") {

    session_regenerate_id(true);

    $_SESSION['user_id']    = 0;
    $_SESSION['user_name']  = "Admin";
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role']  = "super_admin";

    header("Location: ../admin/dashboard.php");
    exit();
}

    $conn = DBConnection::getConnection();
    if (!$conn) {
        throw new Exception("Database connection failed.");
    }

    // 🔎 Fetch user
    $sql = "SELECT id, full_name, email, password, role 
            FROM users 
            WHERE email = ? 
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: ../login.php");
        exit();
    }

    // 🔐 Prevent session fixation
    session_regenerate_id(true);

    // ✅ Store common session data
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role']  = $user['role'];

    // 🔥 ROLE BASED LOGIC
    switch ($user['role']) {

        case 'super_admin':
            header("Location: ../admin/dashboard.php");
            break;

        case 'company_admin':

            // 🔎 Fetch company linked to this user
            $stmt = $conn->prepare("SELECT id, company_name 
                                    FROM companies 
                                    WHERE user_id = ? 
                                    LIMIT 1");
            $stmt->execute([$user['id']]);
            $company = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$company) {
                $_SESSION['error'] = "Company profile not found.";
                header("Location: ../login.php");
                exit();
            }

            // ✅ Store company session
            $_SESSION['company_id']   = $company['id'];
            $_SESSION['company_name'] = $company['company_name'];

            header("Location: ../company/dashboard.php");
            break;

        default:
            header("Location: ../user/dashboard.php");
            break;
    }

    exit();

} catch (Exception $e) {

    error_log("Login Error: " . $e->getMessage());

    $_SESSION['error'] = "Something went wrong. Please try again.";
    header("Location: ../login.php");
    exit();
}
?>