<?php
header('Content-Type: application/json');
include 'db.php'; 

$sql = "SELECT task_id, task_name FROM tasks";
$result = $conn->query($sql);

$tasks = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}

echo json_encode($tasks);
$conn->close();
?>