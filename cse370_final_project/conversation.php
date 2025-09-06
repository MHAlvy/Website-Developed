<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$my_id = $_SESSION['user_id'];
$my_name = $_SESSION['name'];

if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    header("Location: messages.php"); 
    exit();
}
$other_id = intval($_GET['user_id']);

/* ------------------ ENCRYPTION HELPERS ------------------ */
define("ENCRYPTION_KEY", "your-32-char-secret-key-change-this"); // put in config/env
define("CIPHER_METHOD", "AES-256-CBC");

function encryptMessage($plaintext) {
    $ivlen = openssl_cipher_iv_length(CIPHER_METHOD);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext = openssl_encrypt($plaintext, CIPHER_METHOD, ENCRYPTION_KEY, 0, $iv);
    return base64_encode($iv . $ciphertext); // store IV + ciphertext together
}

function decryptMessage($encrypted) {
    $data = base64_decode($encrypted);
    $ivlen = openssl_cipher_iv_length(CIPHER_METHOD);
    $iv = substr($data, 0, $ivlen);
    $ciphertext = substr($data, $ivlen);
    return openssl_decrypt($ciphertext, CIPHER_METHOD, ENCRYPTION_KEY, 0, $iv);
}
/* ------------------------------------------------------- */


// Handle new message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_text'])) {
    $message_text = trim($_POST['message_text']);
    if ($message_text !== "") {
        // Encrypt before storing
        $encrypted_message = encryptMessage($message_text);

        $stmt = $connection->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $my_id, $other_id, $encrypted_message);
        $stmt->execute();
        $stmt->close();

        // Add notification
        $notification_content = "You have a new message from " . htmlspecialchars($my_name);
        $notifStmt = $connection->prepare("INSERT INTO notifications (user_id, content) VALUES (?, ?)");
        $notifStmt->bind_param("is", $other_id, $notification_content);
        $notifStmt->execute();
        $notifStmt->close();
    }

    header("Location: conversation.php?user_id=" . $other_id);
    exit();
}

include 'header.php';

// Get other user's name
$stmt = $connection->prepare("SELECT name FROM user WHERE user_id = ?");
$stmt->bind_param("i", $other_id);
$stmt->execute();
$stmt->bind_result($other_name);
if (!$stmt->fetch()) {
    echo "<div class='card'><p>User not found.</p></div>";
    include 'footer.php';
    exit();
}
$stmt->close();

// Mark messages from other user as seen
$update = $connection->prepare("UPDATE messages SET seen = 1 WHERE sender_id = ? AND receiver_id = ? AND seen = 0");
$update->bind_param("ii", $other_id, $my_id);
$update->execute();
$update->close();

// Fetch conversation
$sql = "
SELECT m.sender_id, u.name AS sender_name, m.message_text, m.sent_at
FROM messages m 
JOIN user u ON u.user_id = m.sender_id
WHERE (m.sender_id = ? AND m.receiver_id = ?) 
   OR (m.sender_id = ? AND m.receiver_id = ?)
ORDER BY m.sent_at ASC";
$stmt = $connection->prepare($sql);
$stmt->bind_param("iiii", $my_id, $other_id, $other_id, $my_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = [];
while ($row = $result->fetch_assoc()) {
    // Decrypt before storing in array
    $row['message_text'] = decryptMessage($row['message_text']);
    $messages[] = $row;
}
$stmt->close();
?>

<div class="card">
    <h2>Conversation with <?php echo htmlspecialchars($other_name); ?></h2>

    <div class="chat-window">
        <div>
            <?php if (empty($messages)): ?>
                <p style="text-align: center;">No messages yet. Say hello!</p>
            <?php else: ?>
                <?php foreach ($messages as $m): ?>
                    <?php $messageClass = ($m['sender_id'] == $my_id) ? 'sender' : 'receiver'; ?>
                    <div class="chat-message <?php echo $messageClass; ?>">
                        <p><?php echo htmlspecialchars($m['message_text']); ?></p>
                        <small>
                            <?php echo htmlspecialchars($m['sender_name']); ?> 
                            at <?php echo date("g:i a, M j", strtotime($m['sent_at'])); ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <form method="POST">
        <label for="message_text">Reply:</label>
        <textarea name="message_text" id="message_text" placeholder="Type your message..." required></textarea>
        <button type="submit">Send</button>
    </form>
</div>

<?php include 'footer.php'; ?>
