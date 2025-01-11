<?php
require_once 'config.php';
require_once 'db.php';

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["user_level"]!= '管理员') {
    header("Location: login.php");
    exit;
}

// 确认用户
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["user_id"])) {
    $user_id = $_POST["user_id"];
    $stmt = $conn->prepare("UPDATE users SET is_confirmed = 1 WHERE id =?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        echo "用户已确认";
    } else {
        echo "Error: ". $stmt->error;
    }
    $stmt->close();
}

// 修改管理员信息
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["new_username"]) && isset($_POST["new_password"])) {
    $new_username = $_POST["new_username"];
    $new_password = password_hash($_POST["new_password"], PASSWORD_DEFAULT);
    $admin_id = $_SESSION["user_id"];
    $stmt = $conn->prepare("UPDATE users SET username =?, password =? WHERE id =?");
    $stmt->bind_param("ssi", $new_username, $new_password, $admin_id);
    if ($stmt->execute()) {
        echo "管理员信息修改成功";
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
    <title>管理员面板</title>
</head>
<body>
    <h1>管理员面板</h1>
    <h2>用户确认</h2>
    <?php
    $sql = "SELECT * FROM users WHERE is_confirmed = 0";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<form action='admin.php' method='post'>
                    <input type='hidden' name='user_id' value='". $row["id"]. "'>
                    <button type='submit'>确认用户: ". $row["username"]. "</button>
                  </form>";
        }
    } else {
        echo "无待确认用户";
    }
?>
    <h2>修改管理员信息</h2>
    <form action="admin.php" method="post">
        <input type="text" name="new_username" placeholder="新用户名">
        <input type="password" name="new_password" placeholder="新密码">
        <button type="submit">修改</button>
    </form>
</body>
</html>