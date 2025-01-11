<?php
header("Access-Control-Allow-Origin: http://localhost/api"); // Replace with the origin of your frontend
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Allow POST and OPTIONS methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow these headers

// Handle preflight request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

include 'db.php'; // Make sure your db connection is correct

try {
    // Get JSON data from the request body
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        throw new Exception('Invalid JSON');
    }

    $username = $data['username'];
    $password = $data['password'];

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ? AND password = ?");
    if (!$stmt) {
        throw new Exception('Prepare statement failed: ' . $conn->error);
    }

    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // User exists
        echo json_encode(['message' => 'Login successful']);
    } else {
        // User does not exist
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
