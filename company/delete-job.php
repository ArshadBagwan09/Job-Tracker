<?php
require_once "../Backend/DBConnection.php";
use Backend\DBConnection;

$conn = DBConnection::getConnection();

if(isset($_GET['id'])){
    $stmt = $conn->prepare("DELETE FROM jobs WHERE id=?");
    $stmt->execute([$_GET['id']]);
}

header("Location: manageJobs.php");
exit;