<?php
include 'connection.php';
$sql = "SELECT * FROM events ORDER BY date_time ASC";
$result = $connection->query($sql);

echo "<h2>Event List</h2>";
while ($row = $result->fetch_assoc()) {
    echo "<div>
        <h3>{$row['title']}</h3>
        Category: {$row['category']}<br>
        Date: {$row['date_time']}<br>
        Location: {$row['location']}<br>
        <a href='register_event.php?event_id={$row['event_id']}'>Register</a>
    </div><hr>";
}
?>
