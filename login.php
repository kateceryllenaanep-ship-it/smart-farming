<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RiceGuard | Auth</title>
<link rel="icon" type="image/png" href="assets/images/logo_sm2.png">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://accounts.google.com/gsi/client" async defer></script>
<style>
    :root {
        --green-dark: #1b4332;
        --green-main: #2d6a4f;
        --green-light: #74c69d;
        --green-bg: #d8f3dc;
        --border: #e9ecef;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
    }

    body {
        background: whitesmoke;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .wrapper {
        width: 100%;
        max-width: 1100px;
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        background: white;
        border-radius: 20px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .left {
        padding: 20px 50px;
        border-right: 1px solid var(--border);
    }

    .left h2 {
        font-weight: 600;
        margin-bottom: 10px;
    }

    .subtitle {
        font-size: 13px;
        color: #666;
        margin-bottom: 25px;
    }

    .form-group {
        margin-bottom: 18px;
        position: relative;
    }

    .form-group label {
        font-size: 12px;
        margin-bottom: 6px;
        display: block;
        color: #555;
    }

    .input-wrapper {
        position: relative;
    }

    .input-wrapper i {
        position: absolute;
        top: 50%;
        left: 12px;
        transform: translateY(-50%);
        color: #999;
    }

    .form-control {
        width: 100%;
        padding: 12px 12px 12px 38px;
        border-radius: 10px;
        border: 1px solid var(--border);
        font-size: 14px;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--green-main);
        box-shadow: 0 0 0 3px rgba(45,106,79,0.1);
    }

    .helper-text {
        font-size: 11px;
        margin-top: 4px;
        display: none;
    }

    .helper-text.error {
        color: #dc3545;
    }

    .helper-text.success {
        color: #198754;
    }

    .primary-btn {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--green-main), var(--green-light));
        color: white;
        font-weight: 500;
        margin-top: 10px;
        transition: 0.2s ease;
        cursor: pointer;
    }

    .primary-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .primary-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    }

    .switch-link {
        margin-top: 18px;
        font-size: 13px;
        text-align: center;
        color: #666;
    }

    .switch-link a {
        color: var(--green-main);
        font-weight: 500;
        text-decoration: none;
    }

    .switch-link a:hover {
        text-decoration: underline;
    }

    .divider {
        text-align: center;
        margin: 20px 0;
        font-size: 12px;
        color: #999;
    }

    .google-btn {
        width: 100%;
        padding: 11px;
        border-radius: 50px;
        border: 1px solid var(--border);
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 500;
    }

    #googleSignInBtn {
        width: 100%;
        display: flex;
        justify-content: center;
        margin-top: 12px;
    }

    #googleSignInBtn > div {
        margin: 0 auto;
    }

    .google-icon {
        width: 20px;
        height: 20px;
    }

    .right {
        padding: 55px 45px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: linear-gradient(135deg, #f6fff8, #d8f3dc);
    }

    .right h2 {
        font-weight: 600;
        margin-bottom: 15px;
        color: var(--green-dark);
    }

    .right p {
        font-size: 14px;
        color: #444;
        line-height: 1.7;
        margin-bottom: 25px;
    }

    .image-box {
        width: 100%;
        height: 230px;
        border-radius: 14px;
        background: white;
        border: 2px dashed var(--green-light);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .image-box img {
        width: 100%;
        height: 300px;
        object-fit: contain;
    }

    .hidden { display: none; }

    @media (max-width: 768px) {
        .wrapper {
            grid-template-columns: 1fr;
        }
        .right {
            text-align: center;
        }
    }

    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .header-text {
        text-align: left;
    }

    .header-text h2 {
        margin: 0;
        font-size: 25px;
    }

    .header-text .subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: #666;
    }

    .logo {
        width: 100px;
        height: 100px;
        object-fit: contain;
    }

    #googleSignInBtn {
        width: 100%;
    }
</style>
</head>
<body>
<div class="wrapper">
    <div class="left">
        <div id="loginForm">
            <div class="header-row">
                <div class="header-text">
                    <h2>Welcome Back</h2>
                    <p class="subtitle">Login to your RiceGuard monitoring system</p>
                </div>
                <img src="assets/images/logo_sm.png" alt="AgriGuard Logo" class="logo">
            </div>
            <form id="login-form" method="POST" action="process_login.php">
                <div class="form-group">
                    <label>Email <span class="text-danger">*</span></label> 
                    <div class="input-wrapper">
                        <i class="bi bi-person"></i>
                        <input type="email" class="form-control" id="login-email" placeholder="Enter email" name="email" required>
                    </div>
                    <span class="helper-text" id="login-email-helper"></span>
                </div>
                <div class="form-group">
                    <label>Password <span class="text-danger">*</span></label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock"></i>
                        <input type="password" class="form-control" id="login-password" placeholder="Enter password" name="password" required>
                    </div>
                    <div class="helper-text" id="login-password-helper"></div>
                </div>
                <button class="primary-btn" type="submit" name="login" id="login-btn" disabled>Login</button>
            </form>
            <div class="switch-link">
                Don't have an account? <a href="#" onclick="switchToRegister(event)">Register</a>
            </div>
            <div class="divider">OR</div>
            <div id="googleSignInBtn"></div>
        </div>
        <div id="registerForm" class="hidden">
            <div class="header-row">
                <div class="header-text">
                    <h2>Create Account</h2>
                    <p class="subtitle">Start protecting your farm with RiceGuard</p>
                </div>
                <img src="assets/images/logo_sm.png" alt="AgriGuard Logo" class="logo">
            </div>
            <form id="register-form" method="POST" action="process_register.php">
                <div class="form-group">
                    <label>Full Name</label>
                    <div class="input-wrapper">
                        <i class="bi bi-person"></i>
                        <input type="text" class="form-control" name="name" placeholder="Full name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email <span class="text-danger">*</span></label>
                    <div class="input-wrapper">
                        <i class="bi bi-envelope"></i>
                        <input type="email" class="form-control" id="register-email" name="email" placeholder="Email" required>
                    </div>
                    <div class="helper-text" id="register-email-helper"></div>
                </div>
                <div class="form-group">
                    <label>Password <span class="text-danger">*</span></label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock"></i>
                        <input type="password" class="form-control" id="register-password" name="password" placeholder="Password" required>
                    </div>
                    <div class="helper-text" id="register-password-helper"></div>
                </div>
                <button class="primary-btn" type="submit" id="register-btn" name="register" disabled>Register</button>
            </form>
            <div class="switch-link">
                Already have an account? <a href="#" onclick="switchToLogin(event)">Login</a>
            </div>
        </div>
    </div>
    <div class="right">
        <h2>Smart Farm Protection</h2>
        <p>
            RiceGuard helps you monitor your farm in real-time using intelligent detection systems.
            Stay informed, prevent threats, and manage your agricultural operations with ease.
        </p>
        <div class="image-box">
            <img src="assets/images/login_icon2.webp">
        </div>
    </div>
</div>
<script>
    function switchToRegister(e) {
        e.preventDefault();
        document.getElementById('loginForm').classList.add('hidden');
        document.getElementById('registerForm').classList.remove('hidden');
    }
    function switchToLogin(e) {
        e.preventDefault();
        document.getElementById('registerForm').classList.add('hidden');
        document.getElementById('loginForm').classList.remove('hidden');
    }

    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function checkLoginInputs() {
        const email = document.getElementById('login-email');
        const password = document.getElementById('login-password');
        const emailHelper = document.getElementById('login-email-helper');
        const passwordHelper = document.getElementById('login-password-helper');
        const btn = document.getElementById('login-btn');

        let valid = true;

        if (email.value.trim() === "") {
            emailHelper.textContent = "";
            emailHelper.className = "helper-text error";
            emailHelper.style.display = "block";
            valid = false;
        } else if (!validateEmail(email.value)) {
            emailHelper.textContent = "Invalid email format";
            emailHelper.className = "helper-text error";
            emailHelper.style.display = "block";
            valid = false;
        } else {
            emailHelper.textContent = "Looks good!";
            emailHelper.className = "helper-text success";
            emailHelper.style.display = "block";
        }

        if (password.value.trim() === "") {
            passwordHelper.textContent = "";
            passwordHelper.className = "helper-text error";
            passwordHelper.style.display = "block";
            valid = false;
        } else if (password.value.length < 6) {
            passwordHelper.textContent = "Password must be at least 6 characters";
            passwordHelper.className = "helper-text error";
            passwordHelper.style.display = "block";
            valid = false;
        } else {
            passwordHelper.textContent = "Looks good!";
            passwordHelper.className = "helper-text success";
            passwordHelper.style.display = "block";
        }

        btn.disabled = !valid;
    }

    function checkRegisterInputs() {
        const email = document.getElementById('register-email');
        const password = document.getElementById('register-password');
        const emailHelper = document.getElementById('register-email-helper');
        const passwordHelper = document.getElementById('register-password-helper');
        const btn = document.getElementById('register-btn');

        let valid = true;

        if (email.value.trim() === "") {
            emailHelper.textContent = "";
            emailHelper.className = "helper-text error";
            emailHelper.style.display = "block";
            valid = false;
        } else if (!validateEmail(email.value)) {
            emailHelper.textContent = "Invalid email format";
            emailHelper.className = "helper-text error";
            emailHelper.style.display = "block";
            valid = false;
        } else {
            emailHelper.textContent = "Looks good!";
            emailHelper.className = "helper-text success";
            emailHelper.style.display = "block";
        }

        if (password.value.trim() === "") {
            passwordHelper.textContent = "";
            passwordHelper.className = "helper-text error";
            passwordHelper.style.display = "block";
            valid = false;
        } else if (password.value.length < 6) {
            passwordHelper.textContent = "Password must be at least 6 characters";
            passwordHelper.className = "helper-text error";
            passwordHelper.style.display = "block";
            valid = false;
        } else {
            passwordHelper.textContent = "Looks good!";
            passwordHelper.className = "helper-text success";
            passwordHelper.style.display = "block";
        }

        btn.disabled = !valid;
    }

    document.getElementById('login-email').addEventListener('input', checkLoginInputs);
    document.getElementById('login-password').addEventListener('input', checkLoginInputs);
    document.getElementById('register-email').addEventListener('input', checkRegisterInputs);
    document.getElementById('register-password').addEventListener('input', checkRegisterInputs);

    document.addEventListener('DOMContentLoaded', () => {
        checkLoginInputs();
        checkRegisterInputs();
        initGoogleSignIn();
    });

    function handleGoogleCredentialResponse(response) {
        if (!response?.credential) {
            Swal.fire({toast:true, position:'top-end', icon:'error', title:'Google authentication failed', showConfirmButton:false, timer:3000});
            return;
        }

        fetch('process_google.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ credential: response.credential })
        })
        .then(async res => {
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch (parseErr) {
                throw new Error(text || 'Invalid server response');
            }
        })
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({toast:true, position:'top-end', icon:'success', title:'Login successful!', showConfirmButton:false, timer:3000});
                setTimeout(() => { window.location.href = 'dashboard.php'; }, 1500);
            } else {
                Swal.fire({toast:true, position:'top-end', icon:'error', title:data.message || 'Google login failed', showConfirmButton:false, timer:3000});
            }
        })
        .catch(err => {
            console.error('Google login error:', err);
            Swal.fire({toast:true, position:'top-end', icon:'error', title:err.message || 'Google login failed', showConfirmButton:false, timer:3000});
        });
    }

    function initGoogleSignIn(retries = 0) {
        if (window.google?.accounts?.id) {
            google.accounts.id.initialize({
                client_id: '731537082526-d2o9tff74ep446buij7l1fdqt76m3mqb.apps.googleusercontent.com',
                callback: handleGoogleCredentialResponse
            });

            google.accounts.id.renderButton(
                document.getElementById('googleSignInBtn'),
                { theme: 'outline', size: 'large' }
            );

            google.accounts.id.prompt();
            return;
        }

        if (retries < 20) {
            setTimeout(() => initGoogleSignIn(retries + 1), 100);
        } else {
            console.error('Google Identity Services failed to load');
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
<script>
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: 'Logged out successfully!',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});
</script>
<?php endif; ?>

<?php if (isset($_GET['login'])): ?>
<script>
const status = "<?= $_GET['login'] ?>";
let icon = "error";
let title = "Login failed";

if (status === "success") {
    icon = "success";
    title = "Login successful!";
} else if (status === "notfound") {
    title = "Email not found!";
} else if (status === "wrongpass") {
    title = "Invalid credentials!";
} else if (status === "unauthorized") {
    title = "Access denied!";
}

Swal.fire({
    toast: true,
    position: 'top-end',
    icon: icon,
    title: title,
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});
</script>
<?php endif; ?>

<?php if(isset($_GET['register'])): ?>
<script>
const regStatus = "<?= $_GET['register'] ?>";
let icon="error",title="Registration failed";
if(regStatus==="registered"){icon="success";title="Account created successfully!";}
else if(regStatus==="exists"){icon="warning";title="Email already exists!";}
else if(regStatus==="empty"){title="All fields are required!";}
else if(regStatus==="invalidemail"){title="Invalid email format!";}
else if(regStatus==="weakpass"){title="Password must be at least 6 characters!";}
else if(regStatus==="error"){title="Something went wrong!";}
Swal.fire({toast:true,position:'top-end',icon:icon,title:title,showConfirmButton:false,timer:3000,timerProgressBar:true});
</script>
<?php endif; ?>
</body>
</html>