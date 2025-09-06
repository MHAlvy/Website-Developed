<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get unread notification count
$unread_notifications = 0;
if (isset($_SESSION['user_id'])) {

    if (!isset($connection) || !$connection) {
        require 'connection.php';
    }
   
    $stmt = $connection->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND seen = 0");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($unread_notifications);
    $stmt->fetch();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusSync Hub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo">CampusSync Hub</a>
            <nav>
                <ul>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="user.php">Dashboard</a></li>
                        <li>
                            <a href="notifications.php">
                                Notifications 
                                <?php if ($unread_notifications > 0): ?>
                                    <strong style="color: #FFC107;">(<?php echo $unread_notifications; ?>)</strong>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="signup.php">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">