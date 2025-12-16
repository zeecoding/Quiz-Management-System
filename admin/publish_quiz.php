<?php
session_start();
include '../config/db.php';

// Security Check: Only Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id'];
    
    // 1. Get current status
    $sql = "SELECT is_published FROM quizzes WHERE id = '$quiz_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    
    // 2. Toggle status (If 0 make 1, if 1 make 0)
    $new_status = ($row['is_published'] == 0) ? 1 : 0;
    
    // 3. Update Database
    $update_sql = "UPDATE quizzes SET is_published = '$new_status' WHERE id = '$quiz_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>