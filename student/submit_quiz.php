<?php
session_start();
include '../config/db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quiz_id = $_POST['quiz_id'];
    $answers = $_POST['answers'];
    $user_id = $_SESSION['user_id'];

    $correct_count = 0;
    $total_questions = 0;

    // 1. Fetch Quiz Details (To get Total Marks)
    $q_sql = "SELECT total_marks, passing_marks FROM quizzes WHERE id = '$quiz_id'";
    $q_result = $conn->query($q_sql);
    $quiz_info = $q_result->fetch_assoc();

    $quiz_total_marks = $quiz_info['total_marks'];
    $passing_marks = $quiz_info['passing_marks'];

    // 2. Fetch correct answers for questions
    $sql = "SELECT id, correct_option FROM questions WHERE quiz_id = '$quiz_id'";
    $result = $conn->query($sql);

    // 3. Compare answers to count how many are correct
    while ($row = $result->fetch_assoc()) {
        $q_id = $row['id'];
        $correct_string = $row['correct_option']; // Database stores "A,B"

        if (isset($answers[$q_id])) {
            $user_ans = $answers[$q_id];

            // LOGIC: Check if it's an array (Multiple Choice) or String (Single Choice)
            if (is_array($user_ans)) {
                // MULTIPLE CHOICE LOGIC
                // 1. Convert user array to string "A,B" to compare
                // We sort it first to ensure "A,B" matches "B,A" if selected out of order
                sort($user_ans);
                $user_ans_string = implode(',', $user_ans);

                if ($user_ans_string === $correct_string) {
                    $correct_count++;
                }
            } else {
                // SINGLE CHOICE LOGIC (Existing)
                if ($user_ans === $correct_string) {
                    $correct_count++;
                }
            }
        }
        $total_questions++;
    }

    // 4. CALCULATE WEIGHTED SCORE
    // Formula: (Total Marks / Total Questions) * Correct Answers
    if ($total_questions > 0) {
        $marks_per_question = $quiz_total_marks / $total_questions;
        $final_score = $correct_count * $marks_per_question;
    } else {
        $final_score = 0;
    }

    // Round the score to 2 decimal places to be neat
    $final_score = round($final_score, 2);

    // 5. Save Result to Database
    $save_sql = "INSERT INTO results (user_id, quiz_id, score, total_questions) 
                 VALUES ('$user_id', '$quiz_id', '$final_score', '$total_questions')";
    $conn->query($save_sql);
}
?>

<?php include '../includes/header.php'; ?>
<title>Quiz Result</title>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center fade-in">
        <div class="col-md-8">
            <div class="card shadow-lg text-center">
                <div class="card-body p-5">
                    <?php
                    $is_passed = $final_score >= $passing_marks;
                    $percentage = ($final_score / $quiz_total_marks) * 100;
                    ?>

                    <div class="mb-4">
                        <?php if ($is_passed): ?>
                            <i class="bi bi-check-circle-fill" style="font-size: 5rem; color: var(--success);"></i>
                        <?php else: ?>
                            <i class="bi bi-x-circle-fill" style="font-size: 5rem; color: var(--danger);"></i>
                        <?php endif; ?>
                    </div>

                    <h1 class="mb-3">
                        <?php echo $is_passed ? 'Congratulations!' : 'Quiz Completed!'; ?>
                    </h1>

                    <div class="mb-4">
                        <h2 class="text-secondary mb-2">Your Score</h2>
                        <h1 class="display-1 fw-bold text-primary mb-0">
                            <?php echo $final_score; ?> / <?php echo $quiz_total_marks; ?>
                        </h1>
                        <p class="text-secondary mt-2">
                            <?php echo round($percentage, 1); ?>% Correct
                        </p>
                    </div>

                    <div class="card mb-4" style="background: var(--bg-secondary);">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="stat-value" style="font-size: 2rem;"><?php echo $correct_count; ?></div>
                                    <div class="stat-label">Correct</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-value" style="font-size: 2rem;">
                                        <?php echo $total_questions - $correct_count; ?></div>
                                    <div class="stat-label">Incorrect</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-value" style="font-size: 2rem;"><?php echo $total_questions; ?>
                                    </div>
                                    <div class="stat-label">Total</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <?php
                        if ($is_passed) {
                            echo "<div class='alert alert-success'><i class='bi bi-trophy-fill me-2'></i><strong>Congratulations!</strong> You passed the quiz with flying colors!</div>";
                        } else {
                            echo "<div class='alert alert-danger'><i class='bi bi-exclamation-triangle-fill me-2'></i><strong>Not quite there yet.</strong> You need {$passing_marks} marks to pass. Better luck next time!</div>";
                        }
                        ?>
                    </div>

                    <div class="d-flex gap-2 justify-content-center">
                        <a href="index.php" class="btn btn-primary btn-lg">
                            <i class="bi bi-house me-2"></i>Back to Dashboard
                        </a>
                        <a href="student_history.php" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-clock-history me-2"></i>View History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>