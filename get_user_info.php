<?php
header('Content-Type: application/json');
require 'db.php'; // Ensure this matches your database connection filename
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Not authenticated"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($user_id_data = $result->fetch_assoc()) {
    echo json_encode([
        "username" => $user_id_data['username'],
        "email" => $user_id_data['email'] ? $user_id_data['email'] : "No email set"
    ]);
} else {
    echo json_encode(["error" => "User not found"]);
}

$stmt->close();
$conn->close();
?>