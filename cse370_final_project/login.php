<?php
session_start();
require 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    $stmt = $connection->prepare("SELECT user_id, name, password FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {

            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['name'] = $row['name'];

            $adminCheck = $connection->prepare("SELECT user_id FROM admin WHERE user_id = ?");
            $adminCheck->bind_param("i", $row['user_id']);
            $adminCheck->execute();
            $adminCheck->store_result();
            $_SESSION['isAdmin'] = ($adminCheck->num_rows > 0);

            $adminCheck->close();

            header("Location: user.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that ID.";
    }
    $stmt->close();
}
?>
<!-- Simple login form -->
<form method="POST">
    User ID: <input type="number" name="user_id" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Login</button>
</form>
