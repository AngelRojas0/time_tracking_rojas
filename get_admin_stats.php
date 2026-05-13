<?php
header("Content-Type: application/json");
require "db.php";

// 1. Total Users (Existing accounts only)
$usersRes = $conn->query("SELECT COUNT(*) as total FROM users WHERE role_id != 1");
$userCount = $usersRes->fetch_assoc()['total'] ?? 0;

// 2. Total Sessions (Existing accounts only)
$sessRes = $conn->query("SELECT COUNT(*) as total FROM time_entries WHERE user_id IN (SELECT user_id FROM users)");
$sessionCount = $sessRes->fetch_assoc()['total'] ?? 0;

// 3. Total Time Tracked (CTE synced with Reports logic)
$timeQuery = "
    WITH SyncedTime AS (
        SELECT t.duration_seconds 
        FROM time_entries t
        INNER JOIN users u ON t.user_id = u.user_id
        WHERE t.duration_seconds IS NOT NULL
    )
    SELECT COALESCE(SUM(duration_seconds), 0) as total_seconds FROM SyncedTime
";

$timeRes = $conn->query($timeQuery)->fetch_assoc();
$totalSeconds = (int)($timeRes['total_seconds'] ?? 0);

// Convert to HH:MM:SS
$h = floor($totalSeconds / 3600);
$m = floor(($totalSeconds / 60) % 60);
$s = $totalSeconds % 60;

$formattedTime = sprintf('%02d:%02d:%02d', $h, $m, $s);

echo json_encode([
    "totalUsers" => (int)$userCount,
    "totalSessions" => (int)$sessionCount,
    "totalTime" => $formattedTime
]);
?>