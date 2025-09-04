<?php
include 'connection.php';
session_start();
$user_id = $_SESSION['user_id'];
$sql = "SELECT r.*, e.title FROM registrations r JOIN events e ON r.event_id = e.event_id WHERE r.user_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Your Event Registrations</h2>";
while ($row = $result->fetch_assoc()) {
    echo "<div>
        <strong>{$row['title']}</strong>: {$row['status']}
        <form method='POST'>
            <input type='hidden' name='reg_id' value='{$row['reg_id']}'>
            <input name='rating' type='number' min='0' max='5' value='{$row['rating']}'>
            <input name='comments' placeholder='Your feedback' value='{$row['comments']}'>
            <button type='submit'>Submit Feedback</button>
        </form>
    </div><hr>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg_id = intval($_POST['reg_id']);
    $rating = intval($_POST['rating']);
    $comments = $_POST['comments'];
    $stmt = $connection->prepare("UPDATE registrations SET rating=?, comments=? WHERE reg_id=? AND user_id=?");
    $stmt->bind_param("isii", $rating, $comments, $reg_id, $user_id);
    $stmt->execute();
    $stmt->close();
    echo "Feedback submitted.";
}
?>
