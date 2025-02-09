<?php
session_start();
require 'config.php'; // تأكد من وجود ملف الإعدادات الذي يحتوي على الاتصال بقاعدة البيانات
require 'vendor/autoload.php'; // تحميل Composer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // التحقق من صحة البريد الإلكتروني
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email!";
        header("Location: forgot_password.php");
        exit;
    }

    // التحقق من وجود المستخدم في قاعدة البيانات
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // إنشاء كود التحقق
        $verification_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("UPDATE users SET verification_code = ?, verification_code_expiry = DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE email = ?");
        if ($stmt->execute([$verification_code, $email])) {
            // إرسال البريد الإلكتروني بكود التحقق
            $subject = "Verification Code";
            $message = "Your verification code is: $verification_code. This code is valid for 10 minutes.";

            // إعداد PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // استبدل بـ SMTP الخاص بك
                $mail->SMTPAuth = true;
                $mail->Username = 'ffkkii57k@gmail.com'; // استبدل بـ بريدك الإلكتروني
                $mail->Password = 'glraocxlllzlulxq'; // استبدل بـ كلمة مرور بريدك الإلكتروني
                $mail->SMTPSecure =PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('ffkkii57k@gmail.com', '');
                $mail->addAddress($_POST['email']);
                $mail->Subject = $subject;
                $mail->Body = $message;

                $mail->send();
                $_SESSION['success'] = "A verification code has been sent to your email.";
                header("Location: verify_code.php?email=$email");
                exit;
            } catch (Exception $e) {
                error_log("Failed to send the verification code email to $email: " . $mail->ErrorInfo);
                $_SESSION['error'] = "Failed to send the verification code email. Please try again.";
                header("Location: forgot_password.php");
                exit;
            }
        } else {
            error_log("Failed to update verification code for $email: " . print_r($stmt->errorInfo(), true));
            $_SESSION['error'] = "Failed to process your request. Please try again.";
            header("Location: forgot_password.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "No account found with that email.";
        header("Location: forgot_password.php");
        exit;
    }
} else {
    header("Location: forgot_password.php");
    exit;
}
?>