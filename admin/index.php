<?php
session_start();
include '../config/db.php';

// Security Check
// This works because the updated login page (index.php) now saves 'role_name' into $_SESSION['role']
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// FETCH QUIZZES FROM DATABASE
$my_id = $_SESSION['user_id'];
$sql = "SELECT * FROM quizzes WHERE created_by = '$my_id' ORDER BY id DESC";
$result = $conn->query($sql);

// ---------------------------------------------------------
// FIX: Update the Student Count Query
// We must JOIN with the 'roles' table now because the 'role' column is gone
// ---------------------------------------------------------
$student_count_sql = "SELECT COUNT(*) as total 
                      FROM users 
                      JOIN roles ON users.role_id = roles.id 
                      WHERE roles.role_name = 'student'";

$student_count = $conn->query($student_count_sql)->fetch_assoc()['total'];

// 2. Count Active Quizzes
$quiz_count_sql = "SELECT COUNT(*) as total FROM quizzes WHERE is_published=1";
$quiz_count = $conn->query($quiz_count_sql)->fetch_assoc()['total'];

// 3. Count Total Attempts
$attempt_count_sql = "SELECT COUNT(*) as total FROM results";
$attempt_count = $conn->query($attempt_count_sql)->fetch_assoc()['total'];
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar_admin.php'; ?>

<div class="container mt-4 mb-5">
    <div class="row mb-5 fade-in">
        <div class="col-md-4 mb-4">
            <div class="stat-card stat-primary">
                <div class="stat-label">
                    <i class="bi bi-people me-2"></i>Total Students
                </div>
                <div class="stat-value"><?php echo $student_count; ?></div>
                <div class="stat-description">Registered users</div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="stat-card stat-success">
                <div class="stat-label">
                    <i class="bi bi-check-circle me-2"></i>Active Quizzes
                </div>
                <div class="stat-value"><?php echo $quiz_count; ?></div>
                <div class="stat-description">Published and live</div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="stat-card stat-warning">
                <div class="stat-label">
                    <i class="bi bi-clipboard-data me-2"></i>Total Attempts
                </div>
                <div class="stat-value"><?php echo $attempt_count; ?></div>
                <div class="stat-description">Quizzes submitted</div>
            </div>
        </div>
    </div>

    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title mb-0">
                    <i class="bi bi-list-ul me-2"></i>Your Quizzes
                </h2>
                <p class="page-subtitle mb-0">Manage and monitor your quiz collection</p>
            </div>
            <a href="create_quiz.php" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create New Quiz
            </a>
        </div>
    </div>

    <div class="card shadow fade-in">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Quiz Title</th>
                            <th>Status</th>
                            <th>Time Limit</th>
                            <th>Total Marks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php 
                            $counter = 1;
                            while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo $counter; ?></strong></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                                    </td>
                                    <td>
                                        <?php if ($row['is_published'] == 1): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Published
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">
                                                <i class="bi bi-pencil me-1"></i>Draft
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <i class="bi bi-clock me-1"></i><?php echo $row['time_limit']; ?> mins
                                    </td>
                                    <td>
                                        <i class="bi bi-star me-1"></i><?php echo $row['total_marks']; ?> marks
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <?php if ($row['is_published'] == 1): ?>
                                                <a href="publish_quiz.php?quiz_id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-sm btn-warning" 
                                                   title="Unpublish">
                                                    <i class="bi bi-eye-slash"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="publish_quiz.php?quiz_id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-sm btn-success" 
                                                   title="Publish">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="add_questions.php?quiz_id=<?php echo $row['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Edit Questions">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="quiz_results.php?quiz_id=<?php echo $row['id']; ?>" 
                                               class="btn btn-sm btn-primary" 
                                               title="View Results">
                                                <i class="bi bi-graph-up"></i>
                                            </a>
                                            <a href="delete_quiz.php?quiz_id=<?php echo $row['id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete this quiz?')" 
                                               title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php 
                            $counter++;
                            endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: var(--text-light);"></i>
                                    <p class="mt-3 text-secondary">No quizzes created yet. Create your first quiz to get started!</p>
                                    <a href="create_quiz.php" class="btn btn-primary mt-2">
                                        <i class="bi bi-plus-circle me-2"></i>Create Your First Quiz
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

<?php include '../includes/footer.php'; ?>