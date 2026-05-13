<?php
header("Content-Type: application/json");
require "db.php";
session_start();

// --- DIAGNOSTIC BLOCK ---
// If you are getting "Unauthorized", this check will help you see why.
if (!isset($_SESSION['role_id'])) {
    // If the session isn't even set, it might be a session_start() issue in login.php
    echo json_encode(["status" => "error", "message" => "Session missing. Please re-login."]);
    exit;
}

if (intval($_SESSION['role_id']) !== 1) {
    // This tells you what your current role_id is so you can fix it in the DB
    echo json_encode(["status" => "error", "message" => "Unauthorized: Your role_id is " . $_SESSION['role_id']]);
    exit;
}
// ------------------------

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Missing User ID"]);
    exit;
}

$user_id = intval($data['user_id']);
$conn->begin_transaction();

try {
    // 1. Delete the user
    // Note: ON DELETE SET NULL handles the logs in time_entries automatically
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new Exception("User ID not found.");
    }

    $conn->commit();
    echo json_encode(["status" => "success"]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>