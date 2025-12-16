<?php
session_start();
include '../config/db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$quiz_id = $_GET['quiz_id'];

// 1. Get Quiz Title
$quiz_sql = "SELECT title FROM quizzes WHERE id = '$quiz_id'";
$quiz_result = $conn->query($quiz_sql);
$quiz_data = $quiz_result->fetch_assoc();

// 2. Get Results (Join 'results' table with 'users' table to get names)
$sql = "SELECT results.*, users.full_name, users.email 
        FROM results 
        JOIN users ON results.user_id = users.id 
        WHERE results.quiz_id = '$quiz_id' 
        ORDER BY results.score DESC"; // Show highest scorers first

$result = $conn->query($sql);
?>

<?php include '../includes/header.php'; ?>

<?php include '../includes/navbar_admin.php'; ?>

<div class="container mt-4 mb-5">
    <div class="page-header mb-4 fade-in">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>Quiz Results
                </h2>
                <p class="page-subtitle mb-0">
                    <i class="bi bi-clipboard-check me-1"></i><?php echo htmlspecialchars($quiz_data['title']); ?>
                </p>
            </div>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <div class="card shadow fade-in">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Score</th>
                            <th>Total Questions</th>
                            <th>Percentage</th>
                            <th>Date Attempted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php 
                            $rank = 1;
                            $result->data_seek(0);
                            while ($row = $result->fetch_assoc()): ?>
                                <?php
                                $percentage = ($row['score'] / $row['total_questions']) * 100;
                                $percentage_rounded = round($percentage, 2);
                                $is_passing = $percentage_rounded >= 50;
                                ?>
                                <tr>
                                    <td>
                                        <?php if ($rank == 1): ?>
                                            <span class="badge bg-warning">
                                                <i class="bi bi-trophy-fill me-1"></i>1st
                                            </span>
                                        <?php elseif ($rank == 2): ?>
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-trophy me-1"></i>2nd
                                            </span>
                                        <?php elseif ($rank == 3): ?>
                                            <span class="badge" style="background: #cd7f32;">
                                                <i class="bi bi-trophy me-1"></i>3rd
                                            </span>
                                        <?php else: ?>
                                            <strong>#<?php echo $rank; ?></strong>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <i class="bi bi-person-circle me-2"></i>
                                        <strong><?php echo htmlspecialchars($row['full_name']); ?></strong>
                                    </td>
                                    <td>
                                        <i class="bi bi-envelope me-2"></i>
                                        <?php echo htmlspecialchars($row['email']); ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $is_passing ? 'bg-success' : 'bg-danger'; ?>">
                                            <strong><?php echo $row['score']; ?></strong>
                                        </span>
                                    </td>
                                    <td><?php echo $row['total_questions']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar <?php echo $is_passing ? 'bg-success' : 'bg-danger'; ?>" 
                                                     role="progressbar" 
                                                     style="width: <?php echo $percentage_rounded; ?>%">
                                                </div>
                                            </div>
                                            <strong><?php echo $percentage_rounded; ?>%</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="bi bi-calendar me-1"></i>
                                        <?php echo date("M j, Y g:i A", strtotime($row['attempt_time'])); ?>
                                    </td>
                                </tr>
                                <?php $rank++; ?>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: var(--text-light);"></i>
                                    <p class="mt-3 text-secondary">No students have taken this quiz yet.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>