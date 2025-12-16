<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id'];
    
    // DELETE query (Cascade will automatically remove related questions and results)
    $sql = "DELETE FROM quizzes WHERE id = '$quiz_id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?msg=deleted");
    } else {
        echo "Error deleting quiz: " . $conn->error;
    }
}
?>