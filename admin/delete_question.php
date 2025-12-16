<?php
session_start();
include '../config/db.php';
// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['quiz_id'])) {
    $question_id = $_GET['id'];
    $quiz_id = $_GET['quiz_id']; // We need this to send you back to the right page

    $sql = "DELETE FROM questions WHERE id = '$question_id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: add_questions.php?quiz_id=" . $quiz_id);
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>