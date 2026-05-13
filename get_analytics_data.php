<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "time_tracking");

if ($conn->connect_error) {
    die(json_encode(["error" => "Conn Failed: " . $conn->connect_error]));
}

$response = ["tasks" => [], "users" => [], "hours" => []];

// Get Hours - Matching your user_sessions table
$hourSql = "SELECT HOUR(login_time) as hr, COUNT(*) as qty 
            FROM user_sessions 
            GROUP BY HOUR(login_time) 
            ORDER BY hr ASC";
$res = $conn->query($hourSql);
if($res) {
    while($row = $res->fetch_assoc()) {
        $response["hours"][] = ["hour" => $row['hr'], "session_count" => $row['qty']];
    }
}

// Get Tasks
$taskSql = "SELECT t.task_name, SUM(te.duration_seconds) as total FROM time_entries te JOIN tasks t ON te.task_id = t.task_id GROUP BY t.task_name";
$resTask = $conn->query($taskSql);
if($resTask) while($r = $resTask->fetch_assoc()) $response["tasks"][] = ["task_name" => $r['task_name'], "total_seconds" => $r['total']];

// Get Users
$userSql = "SELECT u.username, SUM(te.duration_seconds) as total FROM time_entries te JOIN users u ON te.user_id = u.user_id GROUP BY u.username";
$resUser = $conn->query($userSql);
if($resUser) while($r = $resUser->fetch_assoc()) $response["users"][] = ["username" => $r['username'], "total_seconds" => $r['total']];

echo json_encode($response);
$conn->close();
?>