<?php
include(__DIR__ . "/DBConnection.php");

$token = $_POST['token'];
$newPassword = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);

mysqli_query($conn, "UPDATE users 
SET password='$newPassword', reset_token=NULL, token_expiry=NULL 
WHERE reset_token='$token'");

echo "Password updated successfully.";
?>