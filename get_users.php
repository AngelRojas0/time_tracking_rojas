<?php
header("Content-Type: application/json");
require "db.php";
$sql = "SELECT user_id, username, role_id, is_active FROM users WHERE role_id != 1"; 
$result = $conn->query($sql);

$users = [];
while($row = $result->fetch_assoc()) {
    $users[] = $row;
}
echo json_encode($users);
?>