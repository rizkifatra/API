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

// Get JSON data from the request body
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'];
$password = $data['password'];

// Check if user exists
$stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id, $password_hash);
$stmt->fetch();
$stmt->close();

// If user exists and password is correct, return a token (here we're using user ID as token)
if ($user_id && password_verify($password, $password_hash)) {
    echo json_encode(["token" => $user_id]); // Return the user ID as a token
} else {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Invalid username or password"]);
}
?>
