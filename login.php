<?php
require_once 'config.php';
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, password, user_level, is_confirmed FROM users WHERE username =?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            if ($row["is_confirmed"] == 1) {
                session_start();
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["user_level"] = $row["user_level"];
                echo "登录成功";
            } else {
                echo "用户尚未被管理员确认，请稍候再试";
            }
        } else {
            echo "密码错误";
        }
    } else {
        echo "用户不存在";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>用户登录</title>
</head>
<body>
    <h1>用户登录</h1>
    <form action="login.php" method="post">
        <input type="text" name="username" placeholder="用户名">
        <input type="password" name="password" placeholder="密码">
        <button type="submit">登录</button>
    </form>
</body>
</html>