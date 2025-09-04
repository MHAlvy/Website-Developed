<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['user_id'])) {
    die("You must login to register for events.");
}

if (!isset($_GET['event_id'])) {
    die("No event specified.");
}

$user_id = $_SESSION['user_id'];
$event_id = intval($_GET['event_id']);

$stmtCheck = $connection->prepare("SELECT capacity FROM events WHERE event_id = ?");
$stmtCheck->bind_param("i", $event_id);
$stmtCheck->execute();
$stmtCheck->bind_result($capacity);
if (!$stmtCheck->fetch()) {
    $stmtCheck->close();
    die("Event not found.");
}
$stmtCheck->close();

$stmtCount = $connection->prepare("SELECT COUNT(*) FROM registrations WHERE event_id = ? AND status = 'registered'");
$stmtCount->bind_param("i", $event_id);
$stmtCount->execute();
$stmtCount->bind_result($registeredCount);
$stmtCount->fetch();
$stmtCount->close();

$status = ($registeredCount < $capacity) ? 'registered' : 'waitlisted';

$stmtCheckUser = $connection->prepare("SELECT reg_id FROM registrations WHERE user_id = ? AND event_id = ?");
$stmtCheckUser->bind_param("ii", $user_id, $event_id);
$stmtCheckUser->execute();
$stmtCheckUser->store_result();
if ($stmtCheckUser->num_rows > 0) {
    $stmtCheckUser->close();
    die("You have already registered for this event.");
}
$stmtCheckUser->close();

$stmtInsert = $connection->prepare("INSERT INTO registrations (user_id, event_id, status) VALUES (?, ?, ?)");
$stmtInsert->bind_param("iis", $user_id, $event_id, $status);

if ($stmtInsert->execute()) {
    echo "Registration successful. Your status is: $status";
} else {
    echo "Error: " . $stmtInsert->error;
}
$stmtInsert->close();
?>
