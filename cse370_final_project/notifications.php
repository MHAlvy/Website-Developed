<?php
session_start();
include 'header.php';
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><p>Please log in to view notifications.</p></div>";
    include 'footer.php';
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $connection->prepare("SELECT notification_id, content, event_id, item_id, admin_id, seen FROM notifications WHERE user_id = ? ORDER BY notification_id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$updateStmt = $connection->prepare("UPDATE notifications SET seen = 1 WHERE user_id = ? AND seen = 0");
$updateStmt->bind_param("i", $user_id);
$updateStmt->execute();
$updateStmt->close();

?>

<div class="container">
    <h2>My Notifications</h2>
    <?php if ($result->num_rows === 0): ?>
        <div class="alert alert-info mt-4">You have no notifications.</div>
    <?php else: ?>
        <div style="max-width: 600px; margin: 0 auto;">
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                    $isUnseen = !$row['seen'];
                    $content = htmlspecialchars($row['content']);
                    $link = "";
                    if (preg_match('/You have a new message from (.+)/i', $content, $matches)) {
                        $otherName = $matches[1];

                        $userStmt = $connection->prepare("SELECT user_id FROM user WHERE name = ?");
                        $userStmt->bind_param("s", $otherName);
                        $userStmt->execute();
                        $userStmt->bind_result($otherIdVal);
                        if ($userStmt->fetch()) {
                            $link = "conversation.php?user_id=" . intval($otherIdVal);
                        }
                        $userStmt->close();
                    }
                ?>
                <div style="
                    background: rgba(40,40,40,0.97);
                    border-radius: 18px;
                    padding: 1.2em 1.4em;
                    margin-bottom: 1.2em;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.14);
                    <?php if ($isUnseen): ?> border-left: 6px solid #28a745; <?php endif; ?>
                ">
                  <?php if ($link): ?>
                    <a href="<?= $link ?>"
                       style="color: #fff; font-size: 1.15rem; text-decoration: none; font-weight: 600; display:block;">
                       <?= $content ?>
                    </a>
                  <?php else: ?>
                    <span style="color: #fff; font-size: 1.15rem; font-weight: 600;"><?= $content ?></span>
                  <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$stmt->close();
include 'footer.php';
?>
