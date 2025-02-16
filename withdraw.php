<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $user_id = $_SESSION['user_id'];

    // تحقق من أن الرصيد كافٍ للسحب
    $conn = new mysqli('localhost', 'username', 'password', 'my_project');
    $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($balance);
    $stmt->fetch();

    if ($balance >= $amount) {
        // قم بإضافة السحب إلى قاعدة البيانات
        $stmt = $conn->prepare("INSERT INTO withdrawals (user_id, amount) VALUES (?, ?)");
        $stmt->bind_param("id", $user_id, $amount);
        $stmt->execute();

        // قم بتحديث رصيد المستخدم
        $stmt = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $stmt->bind_param("di", $amount, $user_id);
        $stmt->execute();

        header("Location: home.php");
        exit;
    } else {
        echo "Insufficient balance.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw</title>
</head>
<body>
    <form action="withdraw.php" method="post">
        <label for="amount">Amount:</label>
        <input type="number" name="amount" id="amount" required>
        <button type="submit">Withdraw</button>
    </form>
</body>
</html>