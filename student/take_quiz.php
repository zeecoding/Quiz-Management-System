<?php
session_start();
include '../config/db.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$quiz_id = $_GET['quiz_id'];
$user_id = $_SESSION['user_id'];

// ---------------------------------------------------------
// CHECK 1: PREVENT RETAKE
// ---------------------------------------------------------
$check_sql = "SELECT * FROM results WHERE user_id = '$user_id' AND quiz_id = '$quiz_id'";
$check_result = $conn->query($check_sql);

if ($check_result->num_rows > 0) {
    echo "<div style='text-align:center; margin-top:50px;'>
            <h2>You have already attempted this quiz!</h2>
            <a href='index.php'>Return to Dashboard</a>
          </div>";
    exit();
}

// Fetch Quiz Details
$quiz_sql = "SELECT * FROM quizzes WHERE id = '$quiz_id'";
$quiz_result = $conn->query($quiz_sql);
$quiz = $quiz_result->fetch_assoc();

// Fetch Questions
$q_sql = "SELECT * FROM questions WHERE quiz_id = '$quiz_id'";
$questions = $conn->query($q_sql);
?>

<?php include '../includes/header.php'; ?>
<title>Attempt Quiz</title>
<style>
    /* Prevent copying text */
    body {
        user-select: none;
    }
    /* Ensure the input and label are perfectly centered */
    .option-item {
        display: flex;
        align-items: center;
        padding: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: all 0.2s;
        cursor: pointer;
    }
    .option-item:hover {
        background-color: #f8f9fa;
        border-color: #0d6efd;
    }
    /* Make the input slightly larger */
    .form-check-input {
        width: 1.3em;
        height: 1.3em;
        margin-top: 0; /* Remove default bootstrap margin */
        flex-shrink: 0; /* Prevent shrinking if text is long */
    }
    /* Style the text */
    .option-label {
        margin-left: 12px;
        margin-bottom: 0;
        cursor: pointer;
        width: 100%;
    }
</style>

<div class="timer-box" id="timerBox">
    <i class="bi bi-clock"></i>
    Time Left: <span id="timer">00:00</span>
</div>

<div class="container mt-5 mb-5">
    <div class="text-center mb-4 fade-in">
        <h2 class="page-title"><?php echo htmlspecialchars($quiz['title']); ?></h2>
        <p class="text-secondary">
            <i class="bi bi-info-circle me-1"></i>
            Total Questions: <strong><?php echo $questions->num_rows; ?></strong> |
            Time Limit: <strong><?php echo $quiz['time_limit']; ?> minutes</strong>
        </p>
    </div>

    <div class="alert alert-warning fade-in">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Important:</strong> Do not switch tabs or minimize the browser window.
        Doing so will automatically submit your quiz!
    </div>

    <form id="quizForm" action="submit_quiz.php" method="POST">
        <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">

        <?php
        $count = 1;
        $questions->data_seek(0);
        while ($q = $questions->fetch_assoc()):
            // Determine input type (radio or checkbox)
            $inputType = ($q['type'] == 'multiple') ? 'checkbox' : 'radio';
            // Determine name attribute (answers[id] vs answers[id][])
            $inputName = ($q['type'] == 'multiple') ? "answers[{$q['id']}][]" : "answers[{$q['id']}]";
            ?>
            <div class="question-card fade-in">
                <div class="d-flex align-items-start mb-3">
                    <span class="question-number"><?php echo $count; ?></span>
                    <div class="question-text flex-grow-1">
                        <?php echo htmlspecialchars($q['question_text']); ?>
                        <?php if ($q['type'] == 'multiple'): ?>
                            <span class="badge bg-info ms-2">Select all that apply</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="options-container">
                    <div class="option-item">
                        <input class="form-check-input" type="<?php echo $inputType; ?>" name="<?php echo $inputName; ?>"
                            value="A" id="q<?php echo $q['id']; ?>_a">
                        <label class="option-label" for="q<?php echo $q['id']; ?>_a">
                            <?php echo htmlspecialchars($q['option_a']); ?>
                        </label>
                    </div>

                    <div class="option-item">
                        <input class="form-check-input" type="<?php echo $inputType; ?>" name="<?php echo $inputName; ?>"
                            value="B" id="q<?php echo $q['id']; ?>_b">
                        <label class="option-label" for="q<?php echo $q['id']; ?>_b">
                            <?php echo htmlspecialchars($q['option_b']); ?>
                        </label>
                    </div>

                    <div class="option-item">
                        <input class="form-check-input" type="<?php echo $inputType; ?>" name="<?php echo $inputName; ?>"
                            value="C" id="q<?php echo $q['id']; ?>_c">
                        <label class="option-label" for="q<?php echo $q['id']; ?>_c">
                            <?php echo htmlspecialchars($q['option_c']); ?>
                        </label>
                    </div>

                    <div class="option-item">
                        <input class="form-check-input" type="<?php echo $inputType; ?>" name="<?php echo $inputName; ?>"
                            value="D" id="q<?php echo $q['id']; ?>_d">
                        <label class="option-label" for="q<?php echo $q['id']; ?>_d">
                            <?php echo htmlspecialchars($q['option_d']); ?>
                        </label>
                    </div>
                </div>
            </div>
            <?php
            $count++;
        endwhile;
        ?>
        <div class="card shadow-lg mt-4 fade-in">
            <div class="card-body p-4 text-center">
                <button type="submit" class="btn btn-success btn-lg px-5">
                    <i class="bi bi-check-circle me-2"></i>Submit Quiz
                </button>
                <p class="text-secondary mt-3 mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Make sure you've answered all questions before submitting
                </p>
            </div>
        </div>
    </form>
</div>

<script>
    // 1. TIMER LOGIC
    let timeLimit = <?php echo $quiz['time_limit']; ?> * 60;
    const display = document.getElementById('timer');
    const timerBox = document.getElementById('timerBox');

    const countdown = setInterval(() => {
        const minutes = Math.floor(timeLimit / 60);
        let seconds = timeLimit % 60;
        seconds = seconds < 10 ? '0' + seconds : seconds;
        display.textContent = `${minutes}:${seconds}`;

        if (timeLimit <= 300) { // Less than 5 minutes
            timerBox.classList.add('warning');
        }

        if (timeLimit <= 0) {
            clearInterval(countdown);
            submitQuiz();
        }
        timeLimit--;
    }, 1000);

    // 2. AUTO-SUBMIT FUNCTION
    function submitQuiz() {
        // Remove confirm() here for auto-submit when timer ends to prevent blocking
        document.getElementById('quizForm').submit();
    }

    // 3. TAB SWITCHING DETECTION
    document.addEventListener("visibilitychange", function () {
        if (document.hidden) {
            // Optional: Uncomment the alert if you want to warn them
            // alert("Tab switching detected! Your quiz is being submitted automatically.");
            submitQuiz();
        }
    });
</script>
<?php include '../includes/footer.php'; ?>