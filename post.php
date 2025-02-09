<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['error'] = "An account with this email already exists!";
        header("Location: register.php");
        exit;
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        if ($stmt->execute([$email, $hashed_password])) {
            $_SESSION['success'] = "Your account has been created successfully. You can now login.";
            header("Location: login.php");
            exit;
        } else {
            $_SESSION['error'] = "Failed to create your account. Please try again.";
            header("Location: register.php");
            exit;
        }
    }
} else {
    header("Location: register.php");
    exit;
}
?>