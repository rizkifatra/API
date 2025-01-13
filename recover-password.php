<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enable error logging
error_log("Starting password recovery process");

try {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'user');
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Get POST data
    $input = file_get_contents('php://input');
    error_log("Received input: " . $input);
    
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['username'])) {
        throw new Exception("Invalid input data");
    }

    $username = $data['username'];
    error_log("Processing recovery for username: " . $username);

    // Check if user exists and get their email
    $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }

    error_log("User found with ID: " . $user['id']);

    // Generate recovery code
    $recovery_code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Store recovery code
    $stmt = $conn->prepare("UPDATE users SET recovery_code = ?, recovery_expiry = ? WHERE id = ?");
    $stmt->bind_param("ssi", $recovery_code, $expiry, $user['id']);

    if (!$stmt->execute()) {
        throw new Exception("Failed to update recovery code: " . $stmt->error);
    }

    // For testing purposes, return the code directly
    echo json_encode([
        'success' => true,
        'message' => 'Recovery code generated successfully',
        'code' => $recovery_code // Remove this in production
    ]);

    error_log("Recovery code generated successfully: " . $recovery_code);

} catch (Exception $e) {
    error_log("Error in recovery process: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during recovery'
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>


