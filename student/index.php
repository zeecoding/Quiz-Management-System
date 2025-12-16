<?php
session_start();
include '../config/db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. FETCH ALL QUIZZES
$sql = "SELECT * FROM quizzes WHERE is_published=1 ORDER BY id DESC";
$result = $conn->query($sql);

// 2. FETCH ATTEMPTED QUIZZES
// We get a list of all quiz IDs this specific user has already finished
$attempted_sql = "SELECT quiz_id FROM results WHERE user_id = '$user_id'";
$attempted_result = $conn->query($attempted_sql);
$attempted_quizzes = [];
while($row = $attempted_result->fetch_assoc()) {
    $attempted_quizzes[] = $row['quiz_id'];
}
?>

<?php include '../includes/header.php'; ?>
    
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-mortarboard me-2"></i>Student Portal
            </a>
            <div class="navbar-nav ms-auto d-flex flex-row align-items-center gap-2">
                <span class="navbar-text me-3">
                    <i class="bi bi-person-circle me-2"></i>Hi, <?php echo htmlspecialchars($_SESSION['name']); ?>
                </span>
                <a href="../profile.php" class="btn btn-outline-light btn-sm" title="Profile" aria-label="Profile">
                    <i class="bi bi-person"></i>
                </a>
                <a href="student_history.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-clock-history me-1"></i>History
                </a>
                <a href="../logout.php" class="btn btn-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="page-header mb-4 fade-in">
            <h2 class="page-title">
                <i class="bi bi-clipboard-check me-2"></i>Available Quizzes
            </h2>
            <p class="page-subtitle">Select a quiz to start testing your knowledge</p>
        </div>
        
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4 fade-in">
                        <div class="quiz-card">
                            <div class="quiz-title"><?php echo htmlspecialchars($row['title']); ?></div>
                            <div class="quiz-meta">
                                <div class="quiz-meta-item">
                                    <i class="bi bi-clock text-primary"></i>
                                    <span><?php echo $row['time_limit']; ?> minutes</span>
                                </div>
                                <div class="quiz-meta-item">
                                    <i class="bi bi-star text-warning"></i>
                                    <span><?php echo $row['total_marks']; ?> total marks</span>
                                </div>
                                <div class="quiz-meta-item">
                                    <i class="bi bi-check-circle text-success"></i>
                                    <span><?php echo $row['passing_marks']; ?> passing marks</span>
                                </div>
                            </div>
                            
                            <?php if (in_array($row['id'], $attempted_quizzes)): ?>
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="bi bi-check-circle me-2"></i>Already Attempted
                                </button>
                            <?php else: ?>
                                <a href="take_quiz.php?quiz_id=<?php echo $row['id']; ?>" class="btn btn-primary w-100">
                                    <i class="bi bi-play-circle me-2"></i>Start Quiz
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 fade-in">
                    <div class="card shadow">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 4rem; color: var(--text-light);"></i>
                            <h4 class="mt-3 mb-2">No Quizzes Available</h4>
                            <p class="text-secondary">There are no published quizzes at the moment. Please check back later.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>