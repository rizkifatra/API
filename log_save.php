<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $data['user_id']; // Assume token is user ID for simplicity
$api_accessed = $data['api_accessed'];
$action = $data['action'];

// Insert interaction log
$stmt = $conn->prepare("INSERT INTO interaction_logs (user_id, api_accessed, action) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $api_accessed, $action);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Log saved"]);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Failed to save log"]);
}

$stmt->close();
?>
