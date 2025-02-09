<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// افتراضياً، قم بتحديث هذه القيم بناءً على الأرباح اليومية والأرباح الكاملة للمستخدم
$daily_profit = 0.0; // أرباح يومية افتراضية
$total_profit = 0.0; // أرباح كاملة افتراضية

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="hom.css">
</head>
<body>
    <div class="container">
        <img src="1739039667844.png" alt="صورة" class="home-image" style="width:1500px;height:400px;">
        <div class="profit-info">
            <p class="daily-profit">Daily profit balance: <span id="daily-profit"><?php echo $daily_profit; ?></span> USD</p>
            <p class="total-profit">The general total of profits: <span id="total-profit"><?php echo $total_profit; ?></span> USD</p>
        </div>
        <div class="button-container">
        <a href="#" class="button">تنزيل التطبيق</a>
        <a href="#" class="button">تواصل مع خدمة العملاء</a>
    </div>
</div>
</body>
</html>