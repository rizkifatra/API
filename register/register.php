<?php
header("Access-Control-Allow-Origin: http://localhost/api");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Handle preflight request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

include '../db.php'; // Make sure your db connection is correct

// Get JSON data from the request body
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'];
$password = $data['password'];

// Insert new user into the database
$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $password);

if ($stmt->execute()) {
    echo json_encode(["message" => "Registration successful"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Registration failed"]);
}

$stmt->close();
$conn->close();
?>