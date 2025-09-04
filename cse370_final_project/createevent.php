<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    die("Only admin can create events.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $date_time = $_POST['date_time'] ?? '';
    $description = $_POST['description'] ?? '';
    $capacity = intval($_POST['capacity'] ?? 0);
    $location = $_POST['location'] ?? '';

    $stmt = $connection->prepare("INSERT INTO events (title, category, date_time, description, capacity, creator_userID, location) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $title, $category, $date_time, $description, $capacity, $_SESSION['user_id'], $location);
    $stmt->execute();
    $stmt->close();

    echo "Event created successfully!";
}
?>

<form method="POST">
    <label>Title: <input name="title" required></label><br>
    <label>Category: <input name="category" required></label><br>
    <label>Date & Time: <input type="datetime-local" name="date_time" required></label><br>
    <label>Description: <textarea name="description" required></textarea></label><br>
    <label>Capacity: <input type="number" name="capacity" min="1" required></label><br>
    <label>Location: <input name="location" required></label><br>
    <button type="submit">Create Event</button>
</form>
