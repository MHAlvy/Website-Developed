<?php
session_start();
require 'connection.php';

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: homepage.php");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'] ?? '';
$isAdmin = $_SESSION['isAdmin'] ?? false;

$options = [
    "Messages" => "messages.php",
    "Notifications" => "notifications.php",
    "Event Registration" => "event_registration_status.php",
    "Browse Events" => "list_events.php",
    "Lost and Found" => "lost_found.php"
];

if ($isAdmin) {
    $options["Create Event"] = "createevent.php";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard - CampusSync</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
    <h3>Your options:</h3>
    <ul>
        <?php foreach ($options as $label => $file): ?>
            <li><a href="<?php echo htmlspecialchars($file); ?>"><?php echo htmlspecialchars($label); ?></a></li>
        <?php endforeach; ?>
    </ul>
    <br>
    <a href="user.php?logout=true">Logout</a>
</body>
</html>
