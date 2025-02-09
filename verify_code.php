<?php
session_start();
require 'config.php'; // تأكد من وجود ملف الإعدادات الذي يحتوي على الاتصال بقاعدة البيانات

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $verification_code = trim($_POST['verification_code']);

    // التحقق من صحة البيانات المدخلة
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($verification_code) || !ctype_digit($verification_code) || strlen($verification_code) !== 6) {
        $_SESSION['error'] = "Invalid verification code or email!";
        header("Location: verify_code.php?email=$email");
        exit;
    }

    // التحقق من كود التحقق في قاعدة البيانات
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND verification_code = ? AND verification_code_expiry > NOW()");
    $stmt->execute([$email, $verification_code]);
    $user = $stmt->fetch();

    if ($user) {
        // إعادة توجيه المستخدم لتعيين كلمة مرور جديدة
        $_SESSION['email'] = $email;
        header("Location: reset_password.php");
        exit;
    } else {
        $_SESSION['error'] = "Invalid or expired verification code!";
        header("Location: verify_code.php?email=$email");
        exit;
    }
} else {
    $email = isset($_GET['email']) ? $_GET['email'] : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code</title>
</head>
<body>
    <h2>Verify Code</h2>
    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-error">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    ?>
    <form action="verify_code.php" method="POST">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <div>
            <label for="verification_code">Verification Code:</label>
            <input type="text" id="verification_code" name="verification_code" required>
        </div>
        <button type="submit">Verify</button>
    </form>
</body>
</html>