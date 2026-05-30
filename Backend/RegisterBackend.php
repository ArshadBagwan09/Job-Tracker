<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/DBConnection.php";
use Backend\DBConnection;

header('Content-Type: text/plain');

$conn = DBConnection::getConnection();

if (!$conn) {
    echo "db_error";
    exit();
}

// Form Inputs
$name        = trim($_POST['name'] ?? '');
$email       = trim($_POST['email'] ?? '');
$password    = trim($_POST['password'] ?? '');
$role        = $_POST['role'] ?? 'user'; // default user
$companyName = trim($_POST['company_name'] ?? '');

// Validation
if (empty($name) || empty($email) || empty($password)) {
    echo "invalid_input";
    exit();
}

// Prevent super_admin from public registration
if ($role === 'super_admin') {
    echo "invalid_role";
    exit();
}

try {

    $conn->beginTransaction();

    // 🔍 Check if email already exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->execute([$email]);

    if ($checkStmt->fetch()) {
        $conn->rollBack();
        echo "already_exists";
        exit();
    }

    // 🔐 Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 👤 Insert into users table (with role column)
    $insertUser = $conn->prepare(
        "INSERT INTO users (full_name, email, password, role) 
         VALUES (?, ?, ?, ?)"
    );

    $insertUser->execute([$name, $email, $hashedPassword, $role]);

    $userId = $conn->lastInsertId();

    // 🏢 If Company Admin → Insert into companies table
    if ($role === 'company_admin') {

        if (empty($companyName)) {
            $conn->rollBack();
            echo "company_name_required";
            exit();
        }

        $insertCompany = $conn->prepare(
            "INSERT INTO companies (user_id, company_name) 
             VALUES (?, ?)"
        );

        $insertCompany->execute([$userId, $companyName]);
    }

    $conn->commit();

    echo "success";

} catch (Exception $e) {

    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    error_log("Register Error: " . $e->getMessage());
    echo "server_error";
}