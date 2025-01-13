<?php
header('Content-Type: application/json');
require_once 'logger.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    logApiCall(
        $data['api_name'],
        $data['function_name'],
        $data['request_data'],
        $data['response_status']
    );
    
    // Get latest logs after logging
    $conn = new mysqli('localhost', 'root', '', 'user');
    $query = "SELECT * FROM api_logs ORDER BY created_at DESC LIMIT 10";
    $result = $conn->query($query);
    
    $latest_logs = [];
    while ($row = $result->fetch_assoc()) {
        $latest_logs[] = $row;
    }
    
    echo json_encode([
        'status' => 'success',
        'latest_logs' => $latest_logs
    ]);
    
    $conn->close();
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>