<?php
session_start();
include 'header.php';
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "Please login to claim an item.";
    include 'footer.php';
    exit();
}

if (!isset($_GET['item_id'])) {
    echo "No item specified. Return to Lost &amp; Found.";
    include 'footer.php';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = intval($_GET['item_id']);
    $claimFlag = trim($_POST['claimFlag'] ?? '');
    if ($claimFlag === '') {
        echo "Please provide a reason for your claim.";
        include 'footer.php';
        exit();
    }

    $stmt = $connection->prepare("UPDATE items SET claimFlag = ? WHERE item_id = ?");
    $stmt->bind_param("si", $claimFlag, $item_id);

    if ($stmt->execute()) {
        $adminQuery = $connection->query("SELECT user_id FROM admin");
        $notification_content = "An item (ID $item_id) has been claimed: " . htmlspecialchars($claimFlag);
        while ($adminRow = $adminQuery->fetch_assoc()) {
            $admin_id = $adminRow['user_id'];
            $notifStmt = $connection->prepare("INSERT INTO notifications (user_id, content, item_id, admin_id) VALUES (?, ?, ?, ?)");
            $notifStmt->bind_param("isii", $admin_id, $notification_content, $item_id, $admin_id);
            $notifStmt->execute();
            $notifStmt->close();
        }

        echo "<div class='alert alert-success'>Your claim request has been submitted successfully.</div>";
        echo "<a href='lost_found.php'>Return to Lost &amp; Found</a>";
    } else {
        echo "<div class='alert alert-danger'>Error submitting claim, please try again.</div>";
    }
    $stmt->close();
    include 'footer.php';
    exit(); 
}

?>
<div class="container">
<h2>Claim Item</h2>
<form method="post">
    <label for="claimFlag">Please provide a reason for your claim:</label>
    <textarea id="claimFlag" name="claimFlag" required style="width:100%; height:100px;"></textarea>
    <button type="submit" class="btn btn-primary mt-2">Submit Claim</button>
</form>
</div>
<?php include 'footer.php'; ?>
