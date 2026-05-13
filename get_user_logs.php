<?php
header("Content-Type: application/json");
require "db.php";
session_start();

// 1. Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$current_user = $_SESSION['user_id'];

// 2. The Filtered READ
$stmt = $conn->prepare("SELECT * FROM user_sessions WHERE user_id = ? ORDER BY start_time DESC");
$stmt->bind_param("i", $current_user);
$stmt->execute();
$result = $stmt->get_result();

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

// 3. Return the private data to the user's dashboard
echo json_encode($logs);
?>