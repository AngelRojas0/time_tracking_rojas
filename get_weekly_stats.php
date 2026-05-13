<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "time_tracking"); // Use the DB name from your SQL file

if ($conn->connect_error) { die(json_encode(["error" => "Failed"])); }

$sql = "SELECT DATE(login_time) as session_date, COUNT(*) as count 
        FROM sessions 
        GROUP BY DATE(login_time) 
        ORDER BY session_date ASC";

$result = $conn->query($sql);
$data = [];
while($row = $result->fetch_assoc()) { $data[] = $row; }
echo json_encode($data);
$conn->close();
?>