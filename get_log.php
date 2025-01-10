<?php
include 'db.php';

// Get logs
$sql = "SELECT interaction_logs.id, interaction_logs.api_accessed, interaction_logs.action, interaction_logs.date_time, users.username
        FROM interaction_logs
        INNER JOIN users ON interaction_logs.user_id = users.id
        ORDER BY interaction_logs.date_time DESC";

$result = $conn->query($sql);

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

// Return logs as JSON
echo json_encode($logs);
?>
