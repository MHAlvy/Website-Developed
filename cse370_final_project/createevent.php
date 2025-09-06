<?php
require 'connection.php';
include 'header.php';

if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    echo "<div class='card'><p><strong>Access Denied:</strong> Only administrators can create events.</p></div>";
    include 'footer.php';
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $date_time = $_POST['date_time'] ?? '';
    $description = $_POST['description'] ?? '';
    $capacity = intval($_POST['capacity'] ?? 0);
    $location = $_POST['location'] ?? '';

    $stmt = $connection->prepare("INSERT INTO events (title, category, date_time, description, capacity, creator_userID, location) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $title, $category, $date_time, $description, $capacity, $_SESSION['user_id'], $location);
    
    if($stmt->execute()){
        $message = "<p style='color: green;'>Event created successfully!</p>";
        $stmt->close();


        $notification_content = "New Event Posted: " . htmlspecialchars($title);
        
     
        $usersResult = $connection->query("SELECT user_id FROM user WHERE user_id != " . $_SESSION['user_id']);
        

        $notifStmt = $connection->prepare("INSERT INTO notifications (user_id, content) VALUES (?, ?)");
        
        while ($user = $usersResult->fetch_assoc()) {
            $notifStmt->bind_param("is", $user['user_id'], $notification_content);
            $notifStmt->execute();
        }
        $notifStmt->close();


    } else {
        $message = "<p style='color: red;'>Error creating event.</p>";
    }
}

?>

<form method="POST">
    <h2>Create a New Event</h2>
    <?php echo $message; ?>
    
    <label for="title">Title:</label>
    <input id="title" name="title" required>
    
    <label for="category">Category:</label>
    <input id="category" name="category" required>
    
    <label for="date_time">Date & Time:</label>
    <input id="date_time" type="datetime-local" name="date_time" required>
    
    <label for="description">Description:</label>
    <textarea id="description" name="description" required></textarea>
    
    <label for="capacity">Capacity:</label>
    <input id="capacity" type="number" name="capacity" min="1" required>
    
    <label for="location">Location:</label>
    <input id="location" name="location" required>
    
    <button type="submit">Create Event</button>
</form>

<?php include 'footer.php'; ?>
