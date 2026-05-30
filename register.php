<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – Create Account | Job Application Tracker</title>
    <meta name="description"
        content="Create your XpenStore account to start tracking daily expenses, manage budgets, and simplify your personal finance journey.">
    <meta name="keywords"
        content="xpenstore register, create account, sign up expense tracker, personal finance app signup">

    <!-- External CSS -->
    <link rel="stylesheet" href="Style/registerStyle.css">

    <meta name="google-signin-client_id"
        content="907971481870-t1md6ctkr1fcdmv9bdmqnu3s2qasp1ss.apps.googleusercontent.com">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=MuseoModerno:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Audiowide&family=Fira+Sans:wght@300;400;500;600;700&family=Jost:wght@300;400;500;600;700&family=Lato:wght@300;400;700;900&family=Poppins:wght@300;400;500;600;700&family=Raleway:wght@300;400;500;600;700&family=Syncopate:wght@400;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <!-- REGISTER BOX -->
    <div class="register-box" id="registerBox">
        <div class="txtHeading">
            <h1>Create Account</h1>
        </div>

        <form id="registerForm">
            <div class="input-group">
                <i class="fa fa-user icon"></i>
                <input type="text" class="input" name="name" required>
                <label class="placeholder">Name</label>
            </div>

            <div class="input-group">
                <i class="fa fa-envelope icon"></i>
                <input type="email" class="input" name="email" id="regEmail" autocomplete="on" required>
                <label class="placeholder">Email</label>
            </div>

            <div class="input-group">
                <i class="fa fa-user-tie icon"></i>
                <select name="role" class="input" required>
                    <option value="">Select Account Type</option>
                    <option value="user">Job Seeker</option>
                    <option value="company_admin">Company</option>
                </select>
            </div>

            <div class="input-group" id="companyField" style="display:none;">
                <i class="fa fa-building icon"></i>
                <input type="text" class="input" name="company_name">
                <label class="placeholder">Company Name</label>
            </div>

            <div class="input-group">
                <i class="fa fa-lock icon"></i>
                <input type="password" class="input" name="password" id="password" autocomplete="new-password" required>
                <label class="placeholder">Password</label>
                <i class="fa-solid fa-eye toggle-password" id="togglePassword" style="display: none;"></i>
            </div>

            <input type="submit" value="Register">
        </form>

        <p>Already have an account ? <a href="login.php">&nbsp;Login</a></p>
    </div> <!-- ✅ Properly closed registerBox -->


    <!-- THANK YOU BOX (Now Separate) -->
    <div class="register-box" id="thankYouBox" style="display:none; text-align:center; font-family: poppins;">
        <div class="txtHeading">
            <h1 style="font-family: poppins;"><span style="-webkit-text-fill-color: green;">🎉</span> Thank You!</h1>
        </div>
        <p>Your account has been created successfully.</p>
        <p><a href="login.php" class="btn" style="font-family:poppins; margin-top: 10px">Click here to Login</a></p>
    </div>

    <!-- Input animations -->
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

        const passwordInput = document.getElementById("password");
        const togglePassword = document.getElementById("togglePassword");

        passwordInput.addEventListener("focus", () => togglePassword.style.display = "block");
        passwordInput.addEventListener("blur", () => {
            if (passwordInput.value === "") togglePassword.style.display = "none";
        });

        togglePassword.addEventListener("click", function () {
            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const form = document.querySelector("#registerForm");
            const roleSelect = document.querySelector("select[name='role']");
            const companyField = document.getElementById("companyField");

            // 🔥 Show / Hide Company Name Field
            roleSelect.addEventListener("change", function () {
                if (this.value === "company_admin") {
                    companyField.style.display = "block";
                } else {
                    companyField.style.display = "none";
                }
            });

            // 🔥 Form Submit
            form.addEventListener("submit", function (e) {
                e.preventDefault();

                const name = document.querySelector("input[name='name']").value.trim();
                const email = document.querySelector("input[name='email']").value.trim();
                const password = document.querySelector("input[name='password']").value.trim();
                const role = roleSelect.value;
                const companyNameInput = document.querySelector("input[name='company_name']");
                const companyName = companyNameInput ? companyNameInput.value.trim() : "";

                // Validation
                if (name === "" || email === "" || password === "" || role === "") {
                    alert("All fields are required!");
                    return;
                }

                if (role === "company_admin" && companyName === "") {
                    alert("Company name is required for company account!");
                    return;
                }

                fetch('Backend/RegisterBackend.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body:
                        'name=' + encodeURIComponent(name) +
                        '&email=' + encodeURIComponent(email) +
                        '&password=' + encodeURIComponent(password) +
                        '&role=' + encodeURIComponent(role) +
                        '&company_name=' + encodeURIComponent(companyName)
                })
                    .then(res => res.text())
                    .then(resp => {

                        console.log("Server Response:", resp);
                        resp = resp.trim().toLowerCase();

                        if (resp.includes('success')) {
                            showThankYou();
                        }
                        else if (resp.includes('already_exists')) {
                            showAlreadyExists();
                        }
                        else if (resp.includes('company_name_required')) {
                            alert("Please enter company name.");
                        }
                        else {
                            alert("Registration failed. Try again.");
                        }
                    })
                    .catch(error => {
                        console.error("Fetch Error:", error);
                        alert("Something went wrong!");
                    });

            });

        });

        function showThankYou() {
            document.getElementById("registerBox").style.display = "none";
            document.getElementById("thankYouBox").style.display = "block";
            setTimeout(() => {
                window.location.href = "login.php";
            }, 4000);
        }

        function showAlreadyExists() {
            const existing = document.querySelector('.error-message');
            if (!existing) {
                const msg = document.createElement('div');
                msg.className = 'error-message';
                msg.innerHTML = "⚠️ This email is already registered.<br> <a href='login.php'>Click here to Login</a>";
                document.getElementById("registerBox").appendChild(msg);
            }
        }
    </script>
</body>

</html>