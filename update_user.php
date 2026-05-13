<?php

session_start();
header("Content-Type: application/json");
require "db.php";


$current_role = isset($_SESSION['role_id']) ? intval($_SESSION['role_id']) : null;

if ($current_role == 1) {
    
    echo json_encode([
        "status" => "error", 
        "message" => "Unauthorized. Your session role is: " . ($current_role ?? 'Empty/None')
    ]);
    exit;
}


$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['user_id']) && isset($data['is_active'])) {
    $uId = intval($data['user_id']);
    $newStatus = intval($data['is_active']);

    $stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE user_id = ?");
    $stmt->bind_param("ii", $newStatus, $uId);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database update failed"]);
    }
}
?>