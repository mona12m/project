<?php
$conn = new mysqli('localhost', 'username', 'password', 'my_project');

// تحقق من الاتصال بقاعدة البيانات
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// احصل على جميع المستخدمين
$result = $conn->query("SELECT id FROM users");

while ($row = $result->fetch_assoc()) {
    $user_id = $row['id'];
    $profit_amount = 100; // قيمة الأرباح الافتراضية

    // قم بإضافة الأرباح إلى قاعدة البيانات
    $stmt = $conn->prepare("INSERT INTO profits (user_id, amount) VALUES (?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("id", $user_id, $profit_amount);
    if ($stmt->execute() === false) {
        die("Execute failed: " . $stmt->error);
    }

    // قم بتحديث رصيد المستخدم
    $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("di", $profit_amount, $user_id);
    if ($stmt->execute() === false) {
        die("Execute failed: " . $stmt->error);
    }
}

$conn->close();
?>