<?php
// get_logs.php
header('Content-Type: application/json');

// Database connection details
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'user');  // Changed from 'api_dashboard' to 'user'

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Items per page
$offset = ($page - 1) * $limit;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
$api = isset($_GET['api']) ? $_GET['api'] : '';
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build WHERE clause
$where = [];
$params = [];
$types = '';

if ($api) {
    $where[] = "api_name = ?";
    $params[] = $api;
    $types .= 's';
}

if ($dateFrom) {
    $where[] = "DATE(created_at) >= ?";
    $params[] = $dateFrom;
    $types .= 's';
}

if ($dateTo) {
    $where[] = "DATE(created_at) <= ?";
    $params[] = $dateTo;
    $types .= 's';
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM api_logs $whereClause";
$stmt = $conn->prepare($countQuery);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$totalResult = $stmt->get_result()->fetch_assoc();
$total = $totalResult['total'];
$totalPages = ceil($total / $limit);

// Get logs
$allowedColumns = ['created_at', 'api_name', 'function_name', 'user_id', 'response_status'];
$sort = in_array($sort, $allowedColumns) ? $sort : 'created_at';
$order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

$query = "SELECT * FROM api_logs $whereClause ORDER BY $sort $order LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);

$types .= 'ii';
$params[] = $limit;
$params[] = $offset;

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

echo json_encode([
    'logs' => $logs,
    'total_pages' => $totalPages,
    'current_page' => $page,
    'total_records' => $total
]);

$stmt->close();
$conn->close();
?>