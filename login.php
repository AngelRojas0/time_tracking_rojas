<?php
header("Content-Type: application/json");
include "db.php";
session_start();

$data = json_decode(file_get_contents("php://input"), true);
$username = $data["username"];
$password = $data["password"];

$stmt = $conn->prepare("SELECT user_id, role_id, password_hash FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user["password_hash"])) {
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["role_id"] = $user["role_id"];
        echo json_encode(["status" => "success", "role_id" => $user["role_id"]]);
        exit;
    }
}
echo json_encode(["status" => "error"]);
?>