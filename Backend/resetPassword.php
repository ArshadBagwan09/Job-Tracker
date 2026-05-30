<?php
include(__DIR__ . "/DBConnection.php");

if(!isset($_GET['token'])){
    die("Invalid request");
}

$token = $_GET['token'];

$result = mysqli_query($conn, "SELECT * FROM users WHERE reset_token='$token' AND token_expiry > NOW()");

if(mysqli_num_rows($result) == 0){
    die("Invalid or expired token.");
}
?>

<form method="POST" action="Backend/updatePassword.php">
  <input type="hidden" name="token" value="<?php echo $token; ?>">
  <input type="password" name="newPassword" required>
  <button type="submit">Reset Password</button>
</form>