<?php
session_start();
require 'connection.php';

if (!isset($_SESSION['user_id'])) {
    die("Please login first.");
}

if (!isset($_GET['item_id'])) {
    die("No item specified.");
}

$item_id = intval($_GET['item_id']);
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $claimFlag = trim($_POST['claimFlag']);

    if ($claimFlag === "") {
        echo "Please provide a reason for your claim.";
    } else {
        $stmt = $connection->prepare("UPDATE items SET claimFlag = ? WHERE item_id = ?");
        $stmt->bind_param("si", $claimFlag, $item_id);

        if ($stmt->execute()) {
            echo "Claim request sent!";
        } else {
            echo "Error sending claim: " . $stmt->error;
        }
        $stmt->close();
        exit();
    }
}
?>

<h2>Claim Item</h2>
<form method="POST">
    Reason for claim:<br>
    <textarea name="claimFlag" required></textarea><br>
    <button type="submit">Submit Claim</button>
</form>
