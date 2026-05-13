<?php
include 'db.php';

$sql = "INSERT INTO report_summaries (user_id, total_duration_seconds, report_date)
        SELECT user_id, SUM(duration_seconds), CURDATE()
        FROM time_entries
        GROUP BY user_id
        ON DUPLICATE KEY UPDATE total_duration_seconds = VALUES(total_duration_seconds)";

if ($conn->query($sql)) {
    echo "Report summaries table has been updated successfully.";
} else {
    echo "Error updating reports: " . $conn->error;
}
?>