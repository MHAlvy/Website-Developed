<?php
require 'connection.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$my_id = $_SESSION['user_id'];
$my_name = $_SESSION['name'];
$message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id'], $_POST['message_text'])) {
    $receiver_id = intval($_POST['receiver_id']);
    $message_text = trim($_POST['message_text']);

    if ($receiver_id > 0 && $message_text !== "") {
        if ($receiver_id == $my_id) {
            $message = "<p style='color: red;'>You cannot send a message to yourself.</p>";
        } else {
            
            $stmt = $connection->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $my_id, $receiver_id, $message_text);
            
            if($stmt->execute()){
                $stmt->close();

                
                $notification_content = "You have a new message from " . htmlspecialchars($my_name);
                $notifStmt = $connection->prepare("INSERT INTO notifications (user_id, content) VALUES (?, ?)");
                $notifStmt->bind_param("is", $receiver_id, $notification_content);
                $notifStmt->execute();
                $notifStmt->close();
                
               
                header("Location: conversation.php?user_id=" . $receiver_id);
                exit();
            } else {
                $message = "<p style='color: red;'>Could not send message.</p>";
            }
        }
    }
}


$sql = "
SELECT 
    u.user_id, u.name,
    MAX(m.sent_at) AS last_message_time,
    SUM(CASE WHEN m.receiver_id = ? AND m.seen = 0 THEN 1 ELSE 0 END) AS unread_count
FROM messages m
JOIN user u 
    ON (u.user_id = m.sender_id AND m.receiver_id = ?)
    OR (u.user_id = m.receiver_id AND m.sender_id = ?)
WHERE ? IN (m.sender_id, m.receiver_id) AND u.user_id != ?
GROUP BY u.user_id, u.name
ORDER BY last_message_time DESC";

$stmt = $connection->prepare($sql);

$stmt->bind_param("iiiii", $my_id, $my_id, $my_id, $my_id, $my_id);
$stmt->execute();
$result = $stmt->get_result();
$conversations = [];
while ($row = $result->fetch_assoc()) {
    $conversations[] = $row;
}
$stmt->close();
?>

<h2>Messages</h2>

<div class="card">
    <h3>Start a New Conversation</h3>
    <?php echo $message; ?>
    <form method="POST">
        <label for="receiver_id">Receiver's User ID:</label>
        <input type="number" name="receiver_id" id="receiver_id" required>
        
        <label for="message_text">Your Message:</label>
        <textarea name="message_text" id="message_text" placeholder="Type your message..." required></textarea>
        
        <button type="submit">Send Message</button>
    </form>
</div>

<div class="card">
    <h3>Your Conversations</h3>
    <?php if (empty($conversations)): ?>
        <p>You have no conversations yet. Start one above!</p>
    <?php else: ?>
        <ul class="dashboard-menu">
            <?php foreach ($conversations as $c): ?>
                <li>
                    <a href="conversation.php?user_id=<?php echo $c['user_id']; ?>">
                        <?php echo htmlspecialchars($c['name']); ?>
                        <?php if ($c['unread_count'] > 0) echo "<strong style='color:red; margin-left: 10px;'>(" . $c['unread_count'] . " new)</strong>"; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>