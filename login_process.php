<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. Check if user exists
    $stmt = $conn->prepare("SELECT user_id, password_hash, role_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {
       
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $username;
        $_SESSION['role_id'] = $user['role_id'];
        
        $user_id = $_SESSION['user_id'];
        $sql_session = "INSERT INTO user_sessions (user_id, start_time) VALUES (?, NOW())";
        $stmt_session = $conn->prepare($sql_session);
        $stmt_session->bind_param("i", $user_id);
        
        if ($stmt_session->execute()) {
            
            $_SESSION['current_session_id'] = $conn->insert_id;
        }

       
        header("Location: user-dashboard.html");
        exit();
    } else {
        
        header("Location: index.html?error=invalid_credentials");
        exit();
    }
}
?>