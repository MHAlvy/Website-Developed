<?php

require 'connection.php';
include 'header.php'; 


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$name = $_SESSION['name'] ?? 'User';
$isAdmin = $_SESSION['isAdmin'] ?? false;


$options = [
    "Messages" => "messages.php",
    "My Event Registrations" => "event_registration_status.php",
    "Browse Events" => "list_events.php",
    "Lost and Found" => "lost_found.php"
];


if ($isAdmin) {
    $options["Create New Event"] = "createevent.php";
    $options["View Item Claims"] = "view_claims.php";
}
?>

<div class="card">
    <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
    <p>This is your dashboard. From here, you can access all the features of the CampusSync Hub .</p>
</div>

<div class="card">
    <h3>Dashboard Menu</h3>
    <ul class="dashboard-menu">
        <?php foreach ($options as $label => $file): ?>
            <li><a href="<?php echo htmlspecialchars($file); ?>"><?php echo htmlspecialchars($label); ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>


<?php include 'footer.php'; ?>
