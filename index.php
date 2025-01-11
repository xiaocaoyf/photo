<?php
require_once 'config.php';
require_once 'db.php';

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// 检查用户权限
$user_level = $_SESSION["user_level"];
$is_admin = ($user_level == '管理员');
$can_reply = ($user_level == '高级用户' || $user_level == '管理员');
$can_manage = $is_admin;

// 添加任务（仅管理员）
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["task"]) && $can_manage) {
    $task = $_POST["task"];
    $user_id = $_SESSION["user_id"];
    $stmt = $conn->prepare("INSERT INTO tasks (user_id, task) VALUES (?,?)");
    $stmt->bind_param("is", $user_id, $task);
    if ($stmt->execute()) {
        echo "任务添加成功";
    } else {
        echo "Error: ". $stmt->error;
    }
    $stmt->close();
}

// 更新任务状态（仅管理员）
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["task_id"]) && isset($_POST["status"]) && $can_manage) {
    $task_id = $_POST["task_id"];
    $status = $_POST["status"];
    $user_id = $_SESSION["user_id"];
    $stmt = $conn->prepare("UPDATE tasks SET status =? WHERE id =? AND user_id =?");
    $stmt->bind_param("sii", $status, $task_id, $user_id);
    if ($stmt->execute()) {
        echo "任务更新成功";
    } else {
        echo "Error: ". $stmt->error;
    }
    $stmt->close();
}

// 删除任务（仅管理员）
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["task_id"]) && $can_manage) {
    $task_id = $_POST["task_id"];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id =?");
    $stmt->bind_param("i", $task_id);
    if ($stmt->execute()) {
        echo "任务删除成功";
    } else {
        echo "Error: ". $stmt->error;
    }
    $stmt->close();
}

// 获取任务列表
$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM tasks WHERE user_id = $user_id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>随手记</title>
</head>
<body>
    <h1>随手记</h1>
    <?php if ($can_manage):?>
    <form action="index.php" method="post">
        <input type="text" name="task" placeholder="输入任务">
        <button type="submit">添加任务</button>
    </form>
    <?php endif;?>
    <ul>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<li>". $row["task"]. " - ". $row["status"]. " ";
                if ($can_reply || $can_manage) {
                    // 显示更新状态的表单
                    echo "<form action='index.php' method='post'>";
                    if ($can_manage) {
                        echo "<input type='hidden' name='task_id' value='". $row["id"]. "'>";
                        echo "<input type='text' name='status' placeholder='更新状态'>";
                        echo "<button type='submit'>更新</button>";
                        echo "<button type='submit' name='delete_task' value='". $row["id"]. "' formaction='index.php'>删除</button>";
                    }
                    echo "</form>";
                }
                echo "</li>";
            }
        } else {
            echo "<li>无任务</li>";
        }
  ?>
    </ul>
</body>
</html>