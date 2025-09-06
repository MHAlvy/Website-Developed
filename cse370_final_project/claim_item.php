<?php
session_start();
include 'header.php';
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container'><p class='error'>Please login to claim an item.</p></div>";
    include 'footer.php';
    exit();
}

if (!isset($_GET['item_id'])) {
    echo "<div class='container'><p class='error'>No item specified. <a href='lost_found.php'>Return to Lost &amp; Found.</a></p></div>";
    include 'footer.php';
    exit();
}

$item_id = intval($_GET['item_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $claim_reason = trim($_POST['claim_reason'] ?? "");
    $claimer_user_id = $_SESSION['user_id'];

    if ($claim_reason === "") {
        echo "<div class='container'><p class='error'>Please provide a reason for your claim.</p></div>";
        include 'footer.php';
        exit();
    }

    $stmt = $connection->prepare("UPDATE items SET claimFlag = ?, claimer_user_id = ? WHERE item_id = ?");
    $stmt->bind_param("sii", $claim_reason, $claimer_user_id, $item_id);

    if ($stmt->execute()) {
        echo "<div class='container'><p class='success'>Your claim request has been sent successfully! <a href='lost_found.php' class='button'>Return to Lost &amp; Found</a></p></div>";
    } else {
        echo "<div class='container'><p class='error'>Error sending claim request, please try again.</p></div>";
    }

    $stmt->close();
    include 'footer.php';
    exit();
}
?>

<div class="container">
    <h2>Claim This Item</h2>
    <form method="POST" style="max-width: 500px; margin:auto;">
        <label for="claim_reason"><strong>Claim Reason:</strong></label>
        <textarea id="claim_reason" name="claim_reason" rows="3" required style="width:100%;padding:0.7rem;margin-top:8px;border-radius:8px;border:1.3px solid #ddd;"></textarea>
        <br><br>
        <button type="submit" class="button" style="width:100%;">Submit Claim</button>
    </form>
</div>

<?php
include 'footer.php';
?>
