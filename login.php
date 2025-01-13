<?php
session_start();
header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'user');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$username = $data['username'];
$password = $data['password'];

// Debug log
error_log("Login attempt - Username: $username");

// First, check if user exists and get their password
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Debug log
error_log("User found: " . ($user ? 'Yes' : 'No'));
if ($user) {
    error_log("Stored password: " . $user['password']);
    error_log("Provided password: " . $password);
}

// Simple direct comparison (temporary for testing)
if ($user && $password === $user['password']) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    echo json_encode(['success' => true]);
    error_log("Login successful");
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid username or password'
    ]);
    error_log("Login failed");
}

$stmt->close();
$conn->close();
?>

