<?php
session_start();
require 'config.php'; // تأكد من وجود ملف الإعدادات الذي يحتوي على الاتصال بقاعدة البيانات

if (!isset($_SESSION['email'])) {
    header("Location: forgot_password.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['email'];
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // التحقق من صحة البيانات المدخلة
    if (empty($password) || strlen($password) < 8 || !preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/", $password)) {
        $_SESSION['error'] = "Your password needs to have a minimum of 8 characters, including at least one number, one uppercase and one lowercase letter.";
        header("Location: reset_password.php");
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: reset_password.php");
        exit;
    }

    // تشفير كلمة المرور الجديدة
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // تحديث كلمة المرور في قاعدة البيانات
    $stmt = $pdo->prepare("UPDATE users SET password = ?, verification_code = NULL, verification_code_expiry = NULL WHERE email = ?");
    if ($stmt->execute([$hashed_password, $email])) {
        unset($_SESSION['email']);
        $_SESSION['success'] = "Your password has been reset successfully. You can now login.";
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['error'] = "Failed to reset your password. Please try again.";
        header("Location: reset_password.php");
        exit;
    }
} else {
    $email = $_SESSION['email'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-error">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    ?>
    <form action="reset_password.php" method="POST">
        <div>
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>