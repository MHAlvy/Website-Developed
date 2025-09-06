<?php
session_start();
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><p class='error'>Please log in to access your dashboard.</p></div>";
    include 'footer.php';
    exit();
}

$isAdmin = !empty($_SESSION['isAdmin']);

$options = [
    "Messages" => "messages.php",
    "Notifications" => "notifications.php",
    "My Event Registrations" => "event_registration_status.php",
    "Browse Events" => "list_events.php",
    "Lost and Found" => "lost_found.php"
];

if ($isAdmin) {
    $options["Create New Event"] = "createevent.php";
    $options["View Item Claims"] = "view_claims.php";
}
?>

<div class="container">
    <h2>CampusSync Hub Dashboard</h2>
    <ul class="dashboard-menu">
        <?php foreach ($options as $label => $url): ?>
            <li>
                <a href="<?= htmlspecialchars($url) ?>" class="button"><?= htmlspecialchars($label) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="card">
        This is your dashboard. From here, you can access all the features of the CampusSync Hub.
    </div>
</div>

<?php
include 'footer.php';
?>
