<?php

require_once 'db.php';

class Logger {
    private $conn;
    
    public function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
    
    public function logApiCall($userId, $apiName, $functionName, $requestData, $responseStatus) {
        $logId = uniqid('log_', true);
        $stmt = $this->conn->prepare(
            "INSERT INTO api_logs (log_id, user_id, api_name, function_name, request_data, response_status, ip_address) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $stmt->bind_param("sssssss", 
            $logId, 
            $userId, 
            $apiName, 
            $functionName, 
            $requestData, 
            $responseStatus,
            $ipAddress
        );
        
        return $stmt->execute();
    }
}

function logApiCall($api_name, $function_name, $request_data, $response_status) {
    $conn = new mysqli('localhost', 'root', '', 'user');
    
    if ($conn->connect_error) {
        error_log("Logger connection failed: " . $conn->connect_error);
        return;
    }
    
    $stmt = $conn->prepare("INSERT INTO api_logs (api_name, function_name, user_id, request_data, response_status) VALUES (?, ?, ?, ?, ?)");
    
    $user_id = 1; // Default user ID, modify as needed
    $stmt->bind_param("sssss", $api_name, $function_name, $user_id, $request_data, $response_status);
    
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
?>