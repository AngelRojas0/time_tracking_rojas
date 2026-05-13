<?php
header('Content-Type: application/json');
include 'db.php';
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 5; 
$duration = $_POST['idle_duration'];

$sql = "INSERT INTO idle_events (user_id, idle_duration_seconds, created_at) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $duration);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}
?>