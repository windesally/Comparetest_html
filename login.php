<?php
session_start();
include("users.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    if (isset($users[$username]) && password_verify($password, $users[$username]["password"])) {
        $_SESSION["username"] = $username;
        $_SESSION["level"] = $users[$username]["level"];

        // ส่งไปหน้าตาม level
        switch ($_SESSION["level"]) {
            case "admin":
                header("Location: admin.php");
                break;
            case "staff":
                header("Location: staff.php");
                break;
            default:
                echo "Level ไม่ถูกต้อง";
        }

        exit();
    } else {
        echo "invalid username or password";
        header("refresh:2;url=index.html");
    }
}
?>