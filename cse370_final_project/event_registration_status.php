<?php
session_start();
include 'header.php';
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><p>Please login to view your event registrations.</p></div>";
    include 'footer.php';
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reg_id'])) {
    $reg_id = intval($_POST['reg_id']);
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : null;
    $comments = trim($_POST['comments'] ?? '');

    $stmt = $connection->prepare("UPDATE registrations SET rating=?, comments=? WHERE reg_id=? AND user_id=?");
    $stmt->bind_param("isii", $rating, $comments, $reg_id, $user_id);
    if ($stmt->execute()) {
        $message = "<div class='success'>Feedback submitted successfully!</div>";
    } else {
        $message = "<div class='error'>Could not submit feedback.</div>";
    }
    $stmt->close();
}

$sql = "SELECT r.reg_id, r.status, r.rating, r.comments, e.title, e.date_time, e.location 
        FROM registrations r 
        JOIN events e ON r.event_id = e.event_id 
        WHERE r.user_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h2>My Event Registrations</h2>
    <?php
    if (!empty($message)) {
        echo $message;
    }
    ?>

    <?php if ($result->num_rows === 0): ?>
        <div class="alert alert-info mt-4">You have not registered for any events yet. <a href="list_events.php">Browse events now!</a></div>
    <?php else: ?>
        <form method="POST">
        <table class="table table-bordered mt-3" style="background: white;">
            <thead>
                <tr>
                    <th>Event Title</th>
                    <th>Date & Time</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Your Rating</th>
                    <th>Your Comments</th>
                    <th>Submit</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['date_time']) ?></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <input type="number" name="rating" min="1" max="5" value="<?= htmlspecialchars($row['rating'] ?? "") ?>" style="width: 60px;" required>
                    </td>
                    <td>
                        <textarea name="comments" rows="2" style="width:100%;"><?= htmlspecialchars($row['comments'] ?? "") ?></textarea>
                    </td>
                    <td>
                        <button type="submit" name="reg_id" value="<?= $row['reg_id'] ?>" class="btn btn-success btn-sm">Save</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </form>
    <?php endif; ?>
</div>

<?php
$stmt->close();
include 'footer.php';
?>
