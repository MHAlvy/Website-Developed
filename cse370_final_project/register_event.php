<?php
require 'connection.php';
include 'header.php';


if (!isset($_SESSION['user_id'])) {
    echo "<div class='card'><p>You must be <a href='login.php'>logged in</a> to register for events.</p></div>";
    include 'footer.php';
    exit();
}


if (!isset($_GET['event_id'])) {
    echo "<div class='card'><p>No event specified. Please <a href='list_events.php'>browse events</a>.</p></div>";
    include 'footer.php';
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = intval($_GET['event_id']);


echo "<div class='card'>";
echo "<h2>Registration Confirmed!</h2>";
echo "<p>You have successfully registered for the event. Your status has been recorded.</p>";
echo "<a href='list_events.php' class='button'>Back to Events</a>";
echo "</div>";

include 'footer.php';
?>
