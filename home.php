<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$conn = new mysqli('localhost', 'root', '', 'my_project'); // تحديث بيانات الاعتماد

// تحقق من الاتصال بقاعدة البيانات
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// جلب الأرباح اليومية
$daily_profit_query = "SELECT SUM(amount) as daily_profit FROM profits WHERE user_id = ? AND DATE(created_at) = CURDATE()";
$stmt = $conn->prepare($daily_profit_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($daily_profit);
$stmt->fetch();
$stmt->close();

// جلب الأرباح الكاملة
$total_profit_query = "SELECT SUM(amount) as total_profit FROM profits WHERE user_id = ?";
$stmt = $conn->prepare($total_profit_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($total_profit);
$stmt->fetch();
$stmt->close();

$conn->close();
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