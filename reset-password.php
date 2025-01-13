<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'user');
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['code']) || !isset($data['newPassword'])) {
        throw new Exception("Invalid input data");
    }

    $code = $data['code'];
    $newPassword = $data['newPassword'];

    // Debug log
    error_log("Attempting password reset with code: " . $code);

    // Verify recovery code without checking expiry first
    $stmt = $conn->prepare("SELECT * FROM users WHERE recovery_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid recovery code'
        ]);
        exit;
    }

    $user = $result->fetch_assoc();
    error_log("Found user with ID: " . $user['id']);

    // Update password and clear recovery code
    $stmt = $conn->prepare("UPDATE users SET password = ?, recovery_code = NULL, recovery_expiry = NULL WHERE id = ?");
    $stmt->bind_param("si", $newPassword, $user['id']);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
        error_log("Password updated successfully for user ID: " . $user['id']);
    } else {
        throw new Exception("Failed to update password: " . $stmt->error);
    }

} catch (Exception $e) {
    error_log("Error in password reset: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during password reset'
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
