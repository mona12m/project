<?php
session_start();
require 'config.php'; // تأكد من وجود ملف الإعدادات الذي يحتوي على الاتصال بقاعدة البيانات

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // التحقق من صحة البيانات المدخلة
    if (empty($password) || strlen($password) < 8 || !preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/", $password)) {
        $_SESSION['error'] = "Your password needs to have a minimum of 8 characters, including at least one number, one uppercase and one lowercase letter.";
        header("Location: reset_password.php?token=$token");
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: reset_password.php?token=$token");
        exit;
    }

    // التحقق من صحة الرمز
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // تشفير كلمة المرور الجديدة
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // تحديث كلمة المرور في قاعدة البيانات
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
        if ($stmt->execute([$hashed_password, $user['id']])) {
            $_SESSION['success'] = "Your password has been reset successfully. You can now login.";
            header("Location: login.php");
            exit;
        } else {
            $_SESSION['error'] = "Failed to reset your password. Please try again.";
            header("Location: reset_password.php?token=$token");
            exit;
        }
    } else {
        $_SESSION['error'] = "Invalid or expired token!";
        header("Location: reset_password.php?token=$token");
        exit;
    }
} else {
    header("Location: reset_password.php?token=$token");
    exit;
}
?>