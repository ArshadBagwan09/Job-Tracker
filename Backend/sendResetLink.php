<?php
session_start();
include(__DIR__ . "/DBConnection.php");

$email = $_POST['email'];

$result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

if(mysqli_num_rows($result) > 0){
    echo "Email Found";
} else {
    echo "Email Not Found";
}
?>