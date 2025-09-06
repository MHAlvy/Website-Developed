<?php
require 'connection.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='card'><p>Please <a href='login.php'>login</a> to claim an item.</p></div>";
    include 'footer.php';
    exit();
}

if (!isset($_GET['item_id'])) {
    echo "<div class='card'><p>No item specified. <a href='lost_found.php'>Return to Lost & Found</a>.</p></div>";
    include 'footer.php';
    exit();
}

$item_id = intval($_GET['item_id']);
$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $claimFlag = trim($_POST['claimFlag']);

    if (empty($claimFlag)) {
        $message = "<p style='color: red;'>Please provide a reason for your claim.</p>";
    } else {
        $stmt = $connection->prepare("UPDATE items SET claimFlag = ? WHERE item_id = ?");
        $stmt->bind_param("si", $claimFlag, $item_id);

        if ($stmt->execute()) {
            echo "<div class='card'><p style='color: green;'>Your claim request has been sent successfully!</p><a href='lost_found.php'>Return to Lost & Found</a></div>";
            include 'footer.php';
            exit();
        } else {
            $message = "<p style='color: red;'>Error sending claim request.</p>";
        }
        $stmt->close();
    }
}
?>

<form method="POST">
    <h2>Claim an Item</h2>
    <?php echo $message; ?>
    <p>Please provide a detailed reason for your claim to help us verify your ownership.</p>
    
    <label for="claimFlag">Reason for claim:</label>
    <textarea id="claimFlag" name="claimFlag" required></textarea>
    
    <button type="submit">Submit Claim</button>
</form>

<?php include 'footer.php'; ?>
