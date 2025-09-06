<?php
session_start();
require 'connection.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><p>Please login to report or view items.</p></div>";
    include 'footer.php';
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $status = $_POST['status'] ?? '';
    $foundFlag = ($status === 'found') ? 1 : 0;
    $lostFlag = ($status === 'lost') ? 1 : 0;

    $stmt = $connection->prepare("INSERT INTO items (title, description, category, user_id, foundFlag, lostFlag) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiii", $title, $description, $category, $user_id, $foundFlag, $lostFlag);

    if($stmt->execute()){
        $message = "<p class='success'>Item posted successfully.</p>";
        $stmt->close();
    } else {
        $message = "<p class='error'>Error: Could not post item.</p>";
    }
}
?>

<div class="container">
    <h2>Report Lost/Found Item</h2>
    <?php if ($message) echo $message; ?>
    <form method="POST">
        <label>Title: <input type="text" name="title" required></label><br>
        <label>Description: <textarea name="description" required></textarea></label><br>
        <label>Category: <input type="text" name="category" required></label><br>
        Status:
        <label><input type="radio" name="status" value="lost" required> Lost</label>
        <label><input type="radio" name="status" value="found"> Found</label><br>
        <button type="submit">Submit Item</button>
    </form>

    <hr>
    <h2>All Lost & Found Items</h2>
    <?php
    $result = $connection->query("SELECT * FROM items ORDER BY item_id DESC");
    if ($result->num_rows === 0) {
        echo "<p>No items reported yet.</p>";
    }
    while ($row = $result->fetch_assoc()) {
        echo "<div class='card'>";
        echo "<strong>Item ID: " . intval($row['item_id']) . "</strong><br>";
        echo "<strong>Title: " . htmlspecialchars($row['title']) . "</strong><br>";
        echo "Description: " . nl2br(htmlspecialchars($row['description'])) . "<br>";
        echo "Category: " . htmlspecialchars($row['category']) . "<br>";
        echo "Status: ";
        echo $row['lostFlag'] ? "<span style='color:orange;'>Lost</span>" : "";
        echo $row['foundFlag'] ? "<span style='color:green;'>Found</span>" : "";
        echo "<br>";

        if ($row['foundFlag'] && empty($row['claimFlag'])) {
            echo "<a href='claim_item.php?item_id=" . intval($row['item_id']) . "' class='button'>Claim This Item</a>";
        } elseif (!empty($row['claimFlag'])) {
            echo "<em>Claim requested: " . htmlspecialchars($row['claimFlag']) . "</em>";
        }
        echo "</div><hr>";
    }
    ?>
</div>
<?php include 'footer.php'; ?>
