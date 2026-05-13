<?php
header("Content-Type: application/json");
require "db.php";

$data = json_decode(file_get_contents("php://input"), true);
$secret_key = $data["secret_key"] ?? "";
$master_admin_key = "AngelAdmin2026"; 

// Assign 1 for Admin, 2 for User
$role_id = ($secret_key === $master_admin_key) ? 1 : 2;

$hash = password_hash($data["password"], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (role_id, username, password_hash, is_active) VALUES (?, ?, ?, 1)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $role_id, $data["username"], $hash);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
}
?>