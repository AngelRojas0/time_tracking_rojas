<?php
header('Content-Type: application/json');
include 'db.php';

/**
 * 1. CTE (Common Table Expression) - Pre-calculates average duration
 * 2. JOIN - Joins 3 tables: users, time_entries, and tasks
 * 3. SUBQUERY - Filters entries greater than the overall average
 * 4. AGGREGATION - Uses AVG, SUM, COUNT, MAX, MIN
 */

$sql = "WITH AverageStats AS (
            SELECT AVG(duration_seconds) as global_avg FROM time_entries
        )
        SELECT 
            u.username, 
            u.email,
            t.task_name, 
            e.duration_seconds, 
            e.created_at,
            (SELECT COUNT(*) FROM time_entries WHERE user_id = u.user_id) as user_total_logs
        FROM time_entries e
        JOIN users u ON e.user_id = u.user_id
        LEFT JOIN tasks t ON e.task_id = t.task_id
        WHERE e.duration_seconds >= (SELECT global_avg FROM AverageStats)
        ORDER BY e.created_at DESC";

$result = $conn->query($sql);

// Aggregation for Dashboard Stats
$statsQuery = "SELECT 
                COUNT(*) as total_records, 
                SUM(duration_seconds) as total_time, 
                AVG(duration_seconds) as avg_time, 
                MAX(duration_seconds) as longest_session, 
                MIN(duration_seconds) as shortest_session 
               FROM time_entries";
$statsResult = $conn->query($statsQuery)->fetch_assoc();

$response = [
    "logs" => [],
    "stats" => $statsResult
];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $response['logs'][] = $row;
    }
}

echo json_encode($response);
$conn->close();
?>