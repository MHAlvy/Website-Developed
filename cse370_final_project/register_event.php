<?php
session_start();
include 'header.php';
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><p class='error'>You must be logged in to register for events.</p></div>";
    include 'footer.php';
    exit();
}

if (!isset($_GET['event_id'])) {
    echo "<div class='container'><p class='error'>No event specified. Please <a href='list_events.php'>browse events</a>.</p></div>";
    include 'footer.php';
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = intval($_GET['event_id']);


$stmt = $connection->prepare("SELECT reg_id FROM registrations WHERE user_id = ? AND event_id = ?");
$stmt->bind_param("ii", $user_id, $event_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<div class='container'><p class='success'>You are already registered for this event. <a href='event_registration_status.php'>View my registrations</a>.</p></div>";
    $stmt->close();
    include 'footer.php';
    exit();
}
$stmt->close();

$stmt = $connection->prepare("INSERT INTO registrations (user_id, event_id, status) VALUES (?, ?, 'registered')");
$stmt->bind_param("ii", $user_id, $event_id);

if ($stmt->execute()) {
    echo "<div class='container'><h3>Registration Confirmed!</h3>
        <p>You have successfully registered for the event. Your status has been recorded.</p>
        <a href='list_events.php' class='button'>Back to Events</a>
        </div>";
} else {
    echo "<div class='container'><p class='error'>Error: Could not register for event.</p></div>";
}
$stmt->close();

include 'footer.php';
?>
