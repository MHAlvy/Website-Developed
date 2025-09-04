<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    die("Only admins can view claims.");
}

$result = $connection->query("SELECT item_id, title, claimFlag FROM items WHERE claimFlag IS NOT NULL AND claimFlag <> ''");

echo "<h2>Claim Requests</h2>";

if ($result->num_rows === 0) {
    echo "No claim requests found.";
} else {
    while ($row = $result->fetch_assoc()) {
        echo "<div>
            <strong>" . htmlspecialchars($row['title']) . "</strong><br>
            Claim reason: " . nl2br(htmlspecialchars($row['claimFlag'])) . "<br>
            Item ID: " . intval($row['item_id']) . "
        </div><hr>";
    }
}
?>
