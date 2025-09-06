<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isset($_SESSION['user_id'])) {
    header("Location: user.php");
    exit();
}

require 'connection.php';
$error_message = '';

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
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No user found with that ID.";
    }
    $stmt->close();
}


include 'header.php';
?>

<form method="POST">
    <h2>User Login</h2>

    <?php

    if (!empty($error_message)) {
        echo '<p style="color: red; text-align: center;">' . htmlspecialchars($error_message) . '</p>';
    }
    ?>

    <label for="user_id">User ID:</label>
    <input type="number" id="user_id" name="user_id" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Login</button>
</form>

<?php
include 'footer.php';
