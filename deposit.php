<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $user_id = $_SESSION['user_id'];

    // قم بإضافة الإيداع إلى قاعدة البيانات
    $conn = new mysqli('localhost', 'username', 'password', 'my_project');

    // تحقق من الاتصال بقاعدة البيانات
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO deposits (user_id, amount) VALUES (?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("id", $user_id, $amount);
    if ($stmt->execute() === false) {
        die("Execute failed: " . $stmt->error);
    }

    // قم بتحديث رصيد المستخدم
    $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("di", $amount, $user_id);
    if ($stmt->execute() === false) {
        die("Execute failed: " . $stmt->error);
    }

    header("Location: home.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit</title>
</head>
<body>
    <form action="deposit.php" method="post">
        <label for="amount">Amount:</label>
        <input type="number" name="amount" id="amount" required>
        <button type="submit">Deposit</button>
    </form>
</body>
</html>