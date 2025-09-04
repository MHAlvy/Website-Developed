<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['user_id'])) {
    die("Please login first.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $foundFlag = isset($_POST['found']) ? 1 : 0;
    $lostFlag = isset($_POST['lost']) ? 1 : 0;

    $stmt = $connection->prepare("INSERT INTO items (title, description, category, user_id, foundFlag, lostFlag) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiii", $title, $description, $category, $user_id, $foundFlag, $lostFlag);
    $stmt->execute();
    $stmt->close();

    echo "Item posted successfully.<br>";
}
?>
<h2>Report Lost/Found Item</h2>
<form method="POST">
    Title: <input type="text" name="title" required><br>
    Description: <textarea name="description" required></textarea><br>
    Category: <input type="text" name="category" required><br>
    Lost: <input type="checkbox" name="lost"><br>
    Found: <input type="checkbox" name="found"><br>
    <button type="submit">Submit Item</button>
</form>

<hr>
<h2>All Lost & Found Items</h2>
<?php
$result = $connection->query("SELECT * FROM items");
while ($row = $result->fetch_assoc()) {
    echo "<div>
        <strong>Item ID: " . intval($row['item_id']) . "</strong><br>
        <strong>" . htmlspecialchars($row['title']) . "</strong><br>
        " . nl2br(htmlspecialchars($row['description'])) . "<br>
        Category: " . htmlspecialchars($row['category']) . "<br>
        Lost: " . ($row['lostFlag'] ? "Yes" : "No") . " / Found: " . ($row['foundFlag'] ? "Yes" : "No") . "<br>";

    if ($row['foundFlag'] && empty($row['claimFlag'])) {
        echo "<a href='claim_item.php?item_id=" . intval($row['item_id']) . "'>Claim</a>";
    } elseif (!empty($row['claimFlag'])) {
        echo "<em>Claim requested: " . htmlspecialchars($row['claimFlag']) . "</em>";
    }

    echo "</div><hr>";
}
?>
