<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RiceGuard | Access Error</title>
<link rel="icon" type="image/png" href="assets/images/logo_sm2.png">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root {
    --green-dark: #1b4332;
    --green-main: #2d6a4f;
    --green-light: #74c69d;
    --green-bg: #d8f3dc;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
}

body {
    background: linear-gradient(135deg, #f6fff8, #d8f3dc);
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.error-card {
    width: 100%;
    max-width: 900px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.08);
    display: grid;
    grid-template-columns: 1fr 1fr;
    overflow: hidden;
}

.left {
    padding: 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center; /* ✅ horizontal center */
    text-align: center;  /* ✅ text center */
}

.error-code {
    font-size: 70px;
    font-weight: 700;
    color: var(--green-main);
}

.error-title {
    font-size: 22px;
    font-weight: 600;
    margin: 10px 0;
}

.error-desc {
    font-size: 14px;
    color: #555;
    margin-bottom: 25px;
}

.btn-main {
    display: inline-block;
    padding: 12px 20px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--green-main), var(--green-light));
    color: white;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    width: fit-content;
    transition: 0.2s ease;
}

.btn-main:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.08);
}

.right {
    padding: 55px 45px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: linear-gradient(135deg, #f6fff8, #d8f3dc);
}

.right img {
    width: 100%;
    max-width: 300px;
}

.logo {
    width: 80px;
    margin-bottom: 15px;
}

@media (max-width: 768px) {
    .error-card {
        grid-template-columns: 1fr;
    }
    .left {
        text-align: center;
        align-items: center;
    }
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
    height: 260px;
    object-fit: contain;
}

.error-code {
    font-size: 80px;
    font-weight: 700;
    background: linear-gradient(135deg, var(--green-main), var(--green-light));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
</head>
<body>

<?php
    // Detect error type
    $type = $_GET['type'] ?? '401';

    if ($type === 'unauthorized') {
        $code = "401";
        $title = "Unauthorized Access";
        $desc = "You must be logged in to access this page.";
        $icon = "bi-shield-lock";
    } else {
        $code = "404";
        $title = "Page Not Found";
        $desc = "The page you're looking for doesn't exist or was removed.";
        $icon = "bi-exclamation-circle";
    }
?>

<div class="error-card">

    <!-- LEFT -->
    <div class="left">
        <img src="assets/images/logo_lg.png" class="logo">

        <div class="error-code"><?php echo $code; ?></div>

        <div class="error-title">
            <i class="bi <?php echo $icon; ?>"></i>
            <?php echo $title; ?>
        </div>

        <div class="error-desc">
            <?php echo $desc; ?>
        </div>

        <a href="login.php" class="btn-main">
            <i class="bi bi-box-arrow-in-right"></i> Back to Login
        </a>
    </div>

    <div class="right">
        <h2 style="color: var(--green-dark); font-weight:600;">Smart Farm Protection</h2>
        <p style="font-size:14px; color:#444; margin-bottom:20px;">
            Secure your farm with real-time monitoring and intelligent detection.
            Please login to continue accessing the system.
        </p>

        <div class="image-box">
            <img src="assets/images/login_icon2.webp" alt="Error Illustration">
        </div>
    </div>

</div>

</body>
</html>