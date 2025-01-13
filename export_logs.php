<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="api_logs_' . date('Y-m-d_H-i-s') . '.csv"');

// Database connection
$conn = new mysqli('localhost', 'root', '', 'user');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Build WHERE clause for filters
$where = [];
if (!empty($_GET['api'])) {
    $where[] = "api_name = '" . $conn->real_escape_string($_GET['api']) . "'";
}
if (!empty($_GET['date_from'])) {
    $where[] = "DATE(created_at) >= '" . $conn->real_escape_string($_GET['date_from']) . "'";
}
if (!empty($_GET['date_to'])) {
    $where[] = "DATE(created_at) <= '" . $conn->real_escape_string($_GET['date_to']) . "'";
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Create output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Write headers
fputcsv($output, ['Date/Time', 'API', 'Function', 'User ID', 'Request Data', 'Status']);

// Get and write data
$query = "SELECT created_at, api_name, function_name, user_id, request_data, response_status 
          FROM api_logs 
          $whereClause 
          ORDER BY created_at DESC";

$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    // Format date for better readability
    $row['created_at'] = date('Y-m-d H:i:s', strtotime($row['created_at']));
    fputcsv($output, $row);
}

fclose($output);
$conn->close();
?>