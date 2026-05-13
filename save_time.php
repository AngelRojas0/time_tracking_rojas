<?php
header('Content-Type: application/json');
include 'db.php';


session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['duration'])) {
        $duration_string = $_POST['duration'];

        
        $parts = explode(':', $duration_string);
        $seconds = ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];

      
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 5; 

        
        $sql = "INSERT INTO time_entries (user_id, duration_seconds) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $seconds);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => $stmt->error]);
        }
        $stmt->close();
    }
}
$conn->close();
?>