<?php
require 'connection.php'; 
include 'header.php';     


if (!isset($_SESSION['user_id'])) {
    echo "<div class='card'><p>Please <a href='login.php'>login</a> to view events.</p></div>";
    include 'footer.php';
    exit();
}

$sql = "SELECT * FROM events ORDER BY date_time DESC"; 
$result = $connection->query($sql);
?>

<h2>Upcoming Events</h2>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>
            <p><strong>Date & Time:</strong> <?php echo date("F j, Y, g:i a", strtotime($row['date_time'])); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
            <a href="register_event.php?event_id=<?php echo $row['event_id']; ?>" class="button">Register for this Event</a>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="card">
        <p>There are no upcoming events at this time.</p>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
