<?php
require 'connection.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

$stmt = $connection->prepare("SELECT content, notification_id FROM notifications WHERE user_id = ? ORDER BY notification_id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = [];
while($row = $result->fetch_assoc()){
    $notifications[] = $row;
}
$stmt->close();

// Mark these notifications as read (using your 'seen' column)
$updateStmt = $connection->prepare("UPDATE notifications SET seen = 1 WHERE user_id = ? AND seen = 0");
$updateStmt->bind_param("i", $user_id);
$updateStmt->execute();
$updateStmt->close();
?>

<h2>Your Notifications</h2>

<div class="card">
    <?php if (empty($notifications)): ?>
        <p>You have no notifications.</p>
    <?php else: ?>
        <ul class="dashboard-menu">
            <?php foreach ($notifications as $notif): ?>
                <li>
                    <a>
                        <?php echo htmlspecialchars($notif['content']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>