<?php
session_start();
header('Content-Type: application/json');

// Make sure this matches your file name (db.php or db_connection.php)
require 'db.php'; 

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Session expired. Please log in again."]);
    exit;
}

$userId = $_SESSION['user_id'];
$currentPw = $data['current_pw'];
$newPw = $data['new_pw'];

// FIX 1: Change 'password' to 'password_hash' to match your DB image
$stmt = $conn->prepare("SELECT password_hash FROM users WHERE user_id = ?");
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "DB Error: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(["status" => "error", "message" => "User not found."]);
    exit;
}

// FIX 2: Use $user['password_hash'] instead of $user['password']
if (password_verify($currentPw, $user['password_hash'])) {
    
    $hashedPw = password_hash($newPw, PASSWORD_DEFAULT);
    
    // FIX 3: Also update the correct column name here
    $updateStmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
    $updateStmt->bind_param("si", $hashedPw, $userId);
    
    if ($updateStmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update database."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Current password is incorrect."]);
}
?>