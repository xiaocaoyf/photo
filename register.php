<?php
require_once 'config.php';
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $user_level = '初级用户';
    $is_confirmed = 0;

    $stmt = $conn->prepare("INSERT INTO users (username, password, user_level, is_confirmed) VALUES (?,?,?,?)");
    $stmt->bind_param("sssi", $username, $password, $user_level, $is_confirmed);
    if ($stmt->execute()) {
        echo "用户注册成功，请等待管理员确认";
    } else {
        echo "Error: ". $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>用户注册</title>
</head>
<body>
    <h1>用户注册</h1>
    <form action="register.php" method="post">
        <input type="text" name="username" placeholder="用户名">
        <input type="password" name="password" placeholder="密码">
        <button type="submit">注册</button>
    </form>
</body>
</html>