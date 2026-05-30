<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="Style/forgotPasswordStyle.css">
</head>
<body>

<div class="login-box">
  <h1>Forgot Password</h1>
  <p>Enter your email to receive reset link</p>

  <form method="POST" action="Backend/sendResetLink.php">
    <div class="input-group">
      <input type="email" class="input" name="email" autocomplete="on" required>
      <label class="placeholder">Email</label>
    </div>

    <div class="actions">
      <input type="submit" value="Send Reset Link">
    </div>
  </form>

  <?php
  if (isset($_SESSION['msg'])) {
      echo "<p>" . $_SESSION['msg'] . "</p>";
      unset($_SESSION['msg']);
  }
  ?>
</div>

<script>
        window.addEventListener('DOMContentLoaded', function () {
            const inputGroups = document.querySelectorAll('.input-group');
            const heading = document.querySelector('.register-box h1');

            function checkInputs() {
                let anyNotEmpty = false;
                inputGroups.forEach(function (group) {
                    const input = group.querySelector('.input');
                    const icon = group.querySelector('.icon');
                    if (input.value.trim() !== "") {
                        input.classList.add('not-empty');
                        icon.classList.add('active');
                        anyNotEmpty = true;
                    } else {
                        input.classList.remove('not-empty');
                        icon.classList.remove('active');
                    }
                });
                if (anyNotEmpty) heading.classList.add('active');
                else heading.classList.remove('active');
            }

            inputGroups.forEach(function (group) {
                const input = group.querySelector('.input');
                input.addEventListener('input', checkInputs);
            });
            checkInputs();
        });
        </script>
</body>
</html>