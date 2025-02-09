<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <footer class="footer">
        <div class="footer-buttons">
            <a href="home.php" class="icon-button home">
                <i class='bx bx-home'></i>
                <span>Home</span>
            </a>
            <a href="#" class="icon-button withdraw">
                <i class='bx bx-wallet'></i>
                <span>Withdraw</span>
            </a>
            <a href="#" class="icon-button vip">
                <i class='bx bx-crown'></i>
                <span>VIP</span>
            </a>
            <a href="#" class="icon-button account">
                <i class='bx bx-user'></i>
                <span>My Account</span>
            </a>
        </div>
    </footer>
</body>
</html>