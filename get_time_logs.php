<?php
header('Content-Type: application/json');
include 'db.php';

// Removed the hardcoded 'DESC' so the frontend JavaScript has full control over the display order
$sql = "SELECT t.*, u.username, u.email
        FROM time_entries t 
        JOIN users u ON t.user_id = u.user_id 
        ORDER BY t.created_at ASC"; // Defaulting to ASC (Oldest first) is usually standard for data fetching

$result = $conn->query($sql);
$logs = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
}

echo json_encode($logs);
$conn->close();
?>