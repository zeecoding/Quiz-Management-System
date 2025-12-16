<?php
session_start();
include '../config/db.php';

// Security: Only Admins can access this
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $time_limit = $_POST['time_limit'];
    $total_marks = $_POST['total_marks']; // NEW
    $passing_marks = $_POST['passing_marks'];
    $creator_id = $_SESSION['user_id']; 

    // Insert into Database
    $sql = "INSERT INTO quizzes (title, time_limit, total_marks, passing_marks, created_by) 
            VALUES ('$title', '$time_limit', '$total_marks', '$passing_marks', '$creator_id')";

    if ($conn->query($sql) === TRUE) {
        $quiz_id = $conn->insert_id;
        header("Location: add_questions.php?quiz_id=" . $quiz_id);
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<?php include '../includes/header.php'; ?>
<title>Create New Quiz - Admin</title>
    
    <?php include '../includes/navbar_admin.php'; ?>
    
    <div class="container mt-4 mb-5">
        <div class="page-header mb-4 fade-in">
            <h2 class="page-title">
                <i class="bi bi-plus-circle me-2"></i>Create New Quiz
            </h2>
            <p class="page-subtitle">Step 1: Setup quiz details and parameters</p>
        </div>
        
        <div class="row justify-content-center fade-in">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>Quiz Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bi bi-pencil me-2"></i>Quiz Title
                                </label>
                                <input type="text" name="title" class="form-control" placeholder="Enter quiz title" required>
                                <small class="text-secondary">Choose a clear and descriptive title for your quiz</small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <label class="form-label">
                                        <i class="bi bi-star me-2"></i>Total Marks
                                    </label>
                                    <input type="number" name="total_marks" class="form-control" placeholder="e.g. 10" min="1" required>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label class="form-label">
                                        <i class="bi bi-check-circle me-2"></i>Passing Marks
                                    </label>
                                    <input type="number" name="passing_marks" class="form-control" placeholder="e.g. 5" min="1" required>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label class="form-label">
                                        <i class="bi bi-clock me-2"></i>Time Limit (Minutes)
                                    </label>
                                    <input type="number" name="time_limit" class="form-control" placeholder="e.g. 30" min="1" required>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="bi bi-arrow-right me-2"></i>Next: Add Questions
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include '../includes/footer.php'; ?>