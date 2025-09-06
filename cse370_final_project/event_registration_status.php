<?php
require 'connection.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg_id = intval($_POST['reg_id']);
    $rating = intval($_POST['rating']);
    $comments = trim($_POST['comments']);
    
    $stmt = $connection->prepare("UPDATE registrations SET rating=?, comments=? WHERE reg_id=? AND user_id=?");
    $stmt->bind_param("isii", $rating, $comments, $reg_id, $user_id);
    if ($stmt->execute()) {
        $message = "<p style='color: green;'>Feedback submitted successfully!</p>";
    }
    $stmt->close();
}


$sql = "SELECT r.reg_id, r.status, r.rating, r.comments, e.title 
        FROM registrations r 
        JOIN events e ON r.event_id = e.event_id 
        WHERE r.user_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Your Event Registrations</h2>
<?php echo $message; ?>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($row['status'])); ?></p>
            
            <form method="POST" class="feedback-form">
                <input type="hidden" name="reg_id" value="<?php echo $row['reg_id']; ?>">
                
                <label for="rating-<?php echo $row['reg_id']; ?>">Your Rating (1-5):</label>
                <input id="rating-<?php echo $row['reg_id']; ?>" name="rating" type="number" min="1" max="5" value="<?php echo htmlspecialchars($row['rating']); ?>">
                
                <label for="comments-<?php echo $row['reg_id']; ?>">Your Feedback:</label>
                <textarea id="comments-<?php echo $row['reg_id']; ?>" name="comments" placeholder="Your feedback helps us improve!"><?php echo htmlspecialchars($row['comments']); ?></textarea>
                
                <button type="submit">Submit Feedback</button>
            </form>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="card">
        <p>You have not registered for any events yet. <a href="list_events.php">Browse events now!</a></p>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
