<?php
session_start();
include 'header.php';
include 'connection.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['isAdmin'])) {
    echo "<p><strong>Access Denied:</strong> Only administrators can view claims.</p>";
    include 'footer.php';
    exit();
}

if (isset($_GET['action'], $_GET['item_id'])) {
    $action = $_GET['action'];
    $item_id = intval($_GET['item_id']);

    if ($action === 'accept') {
        $stmt = $connection->prepare("UPDATE items SET claimFlag = 'accepted' WHERE item_id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $stmt->close();
        header("Location: view_claims.php");
        exit();
    } elseif ($action === 'reject') {
        $stmt = $connection->prepare("UPDATE items SET claimFlag = NULL, claimer_user_id = NULL WHERE item_id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $stmt->close();
        header("Location: view_claims.php");
        exit();
    }
}

$result = $connection->query("SELECT item_id, title, claimFlag, claimer_user_id FROM items WHERE claimFlag IS NOT NULL AND claimFlag <> '' AND claimFlag <> 'accepted' ORDER BY item_id DESC");
?>

<div class="container">
    <h2>Pending Item Claims</h2>
    <?php if ($result && $result->num_rows > 0): ?>
        <table class="table" border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th>Item ID</th>
                    <th>Title</th>
                    <th>Claim Reason</th>
                    <th>Claimer ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['item_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['claimFlag']); ?></td>
                        <td><?php echo htmlspecialchars($row['claimer_user_id']); ?></td>
                        <td>
                            <a href="view_claims.php?action=accept&item_id=<?php echo $row['item_id']; ?>" class="button">Accept</a> |
                            <a href="view_claims.php?action=reject&item_id=<?php echo $row['item_id']; ?>" class="button" onclick="return confirm('Are you sure you want to reject this claim?');">Reject</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>There are no pending item claims.</p>
    <?php endif; ?>
</div>
<?php
include 'footer.php';
?>
