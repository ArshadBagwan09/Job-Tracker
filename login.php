<?php
use Backend\DBConnection;
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

require_once __DIR__ . "/Backend/DBConnection.php";
$db = new DBConnection();
$conn = $db::getConnection();  // static call

$error = null;

// Agar koi error message session me set hai to use fetch karo
if (isset($_SESSION['error'])) {
	$error = $_SESSION['error'];
	unset($_SESSION['error']); // ek bar show karne ke baad hata do
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login – Job Application Tracker</title>
	<meta name="description"
		content="Securely log in to your XpenStore Daily Expense Tracker account to manage and track your expenses.">
	<meta name="robots" content="noindex, nofollow">

	<link rel="stylesheet" href="Style/loginStyle.css">

	<meta name="google-signin-client_id"
		content="907971481870-t1md6ctkr1fcdmv9bdmqnu3s2qasp1ss.apps.googleusercontent.com">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

	<!-- google fonts link -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=MuseoModerno:ital,wght@0,100..900;1,100..900&display=swap"
		rel="stylesheet">
	<link
		href="https://fonts.googleapis.com/css2?family=Audiowide&family=Fira+Sans:wght@100..900&family=Jost:wght@100..900&family=Lato:wght@100..900&family=Poppins:wght@100..900&family=Raleway:wght@100..900&family=Syncopate:wght@400;700&display=swap"
		rel="stylesheet">
</head>

<body>
	<div class="login-box">
		<div class="txtHeading">
			<h1>Login</h1>
		</div>

		<form method="POST" action="Backend/LoginBackend.php">
			<div class="input-group">
				<i class="fa fa-envelope icon"></i>
				<input type="email" class="input" name="email" autocomplete="on" required>
				<label class="placeholder">Email</label>
			</div>

			<div class="input-group">
				<i class="fa fa-lock icon"></i>
				<input type="password" class="input" name="password" id="password" autocomplete="new-password" required>
				<label class="placeholder">Password</label>
				<i class="fa-solid fa-eye toggle-password" id="togglePassword" style="display: none;"></i>
			</div>

			<?php if ($error): ?>
				<div class="errorDiv" id="errorDiv">
					<p class="error-message"><?php echo htmlspecialchars($error); ?></p>
				</div>
			<?php endif; ?>

			<div class="input-group">
				<input type="submit" value="Login">
			</div>

			<div class="forgot-password">
				<a href="forgotPassword.php">Forgot Password?</a>
			</div>
		</form>

		<p>Don't have an account ? <a href="register.php">&nbsp;Register</a></p>


		<!-- Error Box -->
		<div id="error-box" class="error-box" style="display:none;">
			<strong id="errorHeader">Account not found !</strong>
			Please <a href="register.php">register here</a> to continue.
		</div>
	</div>

	<script>
		document.addEventListener("DOMContentLoaded", function () {
			const emailInput = document.querySelector('input[name="email"]');
			const passwordInput = document.querySelector('input[name="password"]');
			const errorDiv = document.getElementById("errorDiv");

			function hideError() {
				if (errorDiv) {
					errorDiv.style.display = "none";
				}
			}

			emailInput.addEventListener("input", hideError);
			passwordInput.addEventListener("input", hideError);
		});
	</script>

	<script>
		function showError(message) {
			const errorP = document.getElementById("googleError");
			errorP.textContent = message;
			errorP.style.display = "block";
		}
	</script>

	<script>
		window.addEventListener('DOMContentLoaded', function () {
			const inputGroups = document.querySelectorAll('.input-group');
			const heading = document.querySelector('.login-box h1');

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

				if (anyNotEmpty) {
					heading.classList.add('active');
				} else {
					heading.classList.remove('active');
				}
			}

			inputGroups.forEach(function (group) {
				const input = group.querySelector('.input');
				input.addEventListener('input', checkInputs);
			});

			checkInputs();
		});

		document.addEventListener("DOMContentLoaded", function () {

			const passwordInput = document.getElementById("password");
			const togglePassword = document.getElementById("togglePassword");

			if (passwordInput && togglePassword) {

				passwordInput.addEventListener("focus", function () {
					togglePassword.style.display = "block";
				});

				passwordInput.addEventListener("blur", function () {
					if (passwordInput.value === "") {
						togglePassword.style.display = "none";
					}
				});

				togglePassword.addEventListener("click", function () {
					const isPassword = passwordInput.type === "password";
					passwordInput.type = isPassword ? "text" : "password";
					this.classList.toggle("fa-eye");
					this.classList.toggle("fa-eye-slash");
				});

			}

		});
	</script>
</body>

</html>