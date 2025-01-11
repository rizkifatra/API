<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/API/api.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$action = $_GET['action'];

if ($action == 'login') {
    // Your login logic here
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'];
    $password = $input['password'];

    // Validate username and password
    if ($username == 'admin' && $password == 'password') {
        echo json_encode(['token' => 'your-token']);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
} elseif ($action == 'data') {
    // Your data fetching logic here
    $headers = getallheaders();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

    if ($authHeader == 'Bearer your-token') {
        echo json_encode([
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2'],
        ]);
    } else {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
}
?>