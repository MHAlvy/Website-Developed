<?php
require 'connection.php';
include 'header.php';

if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    echo "<div class='card'><p><strong>Access Denied:</strong> Only administrators can view claims.</p></div>";
    include 'footer.php';
    exit();
}

$result = $connection->query("SELECT item_id, title, claimFlag FROM items WHERE claimFlag IS NOT NULL AND claimFlag <> '' ORDER BY item_id DESC");
?>

<h2>Item Claim Requests</h2>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p><strong>Item ID:</strong> <?php echo intval($row['item_id']); ?></p>
            <p><strong>Claim Reason:</strong></p>
            <p style="background-color: #f4f7f6; padding: 1rem; border-radius: 4px;"><?php echo nl2br(htmlspecialchars($row['claimFlag'])); ?></p>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="card">
        <p>There are no pending item claims.</p>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
