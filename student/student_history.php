<?php
session_start();
include '../config/db.php';


// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch Results for this specific user
// We select passing_marks so we can use it for the calculation
$sql = "SELECT results.*, quizzes.title, quizzes.passing_marks 
        FROM results 
        JOIN quizzes ON results.quiz_id = quizzes.id 
        WHERE results.user_id = '$user_id' 
        ORDER BY results.attempt_time DESC";

$result = $conn->query($sql);
?>

<?php include '../includes/header.php'; ?>
<title>Student History</title>
    
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-mortarboard me-2"></i>Student Portal
            </a>
            <div class="navbar-nav ms-auto d-flex flex-row align-items-center gap-2">
                <a href="index.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-house me-1"></i>Dashboard
                </a>
                <a href="../logout.php" class="btn btn-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <div class="page-header mb-4 fade-in">
            <h2 class="page-title">
                <i class="bi bi-clock-history me-2"></i>My Attempt History
            </h2>
            <p class="page-subtitle">View all your quiz attempts and results</p>
        </div>
        
        <div class="card shadow fade-in">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Quiz Title</th>
                                <th>Score</th>
                                <th>Passing Marks</th>
                                <th>Total Questions</th>
                                <th>Status</th>
                                <th>Date Attempted</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <?php 
                                    $score = $row['score'];
                                    $passing = $row['passing_marks'];
                                    
                                    if ($score >= $passing) {
                                        $status = "PASS";
                                        $badge = "bg-success";
                                        $icon = "bi-check-circle-fill";
                                    } else {
                                        $status = "FAIL";
                                        $badge = "bg-danger";
                                        $icon = "bi-x-circle-fill";
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <i class="bi bi-clipboard-check me-2"></i>
                                            <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $badge; ?>">
                                                <strong><?php echo $score; ?></strong>
                                            </span>
                                        </td>
                                        <td><?php echo $passing; ?></td>
                                        <td><?php echo $row['total_questions']; ?></td>
                                        <td>
                                            <span class="badge <?php echo $badge; ?>">
                                                <i class="bi <?php echo $icon; ?> me-1"></i><?php echo $status; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <i class="bi bi-calendar me-1"></i>
                                            <?php echo date("M j, Y g:i A", strtotime($row['attempt_time'])); ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: var(--text-light);"></i>
                                        <p class="mt-3 text-secondary">You haven't taken any quizzes yet.</p>
                                        <a href="index.php" class="btn btn-primary mt-2">
                                            <i class="bi bi-arrow-left me-2"></i>Browse Quizzes
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>