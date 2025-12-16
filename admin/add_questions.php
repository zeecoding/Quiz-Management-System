<?php
session_start();
include '../config/db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Get Quiz ID
if (isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id'];
} else {
    header("Location: index.php");
    exit();
}

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type']; 
    $question_text = $_POST['question_text'];
    $opt_a = $_POST['option_a'];
    $opt_b = $_POST['option_b'];
    $opt_c = $_POST['option_c'];
    $opt_d = $_POST['option_d'];
    
    // LOGIC: Handle correct answer based on Type
    $correct = "";
    if ($type == 'multiple') {
        // If multiple, join array with commas (e.g., "A,C")
        if (isset($_POST['correct_multiple'])) {
            $correct = implode(',', $_POST['correct_multiple']);
        }
    } else {
        // If single, take the single radio value
        if (isset($_POST['correct_single'])) {
            $correct = $_POST['correct_single'];
        }
    }

    // Insert into DB
    $sql = "INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option, type) 
            VALUES ('$quiz_id', '$question_text', '$opt_a', '$opt_b', '$opt_c', '$opt_d', '$correct', '$type')";

    if ($conn->query($sql) === TRUE) {
        $message = "Question added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch existing questions
$q_sql = "SELECT * FROM questions WHERE quiz_id = '$quiz_id' ORDER BY id DESC";
$questions = $conn->query($q_sql);
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar_admin.php'; ?>

<div class="container mt-4 mb-5">
    <div class="page-header mb-4 fade-in">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title mb-0">
                    <i class="bi bi-question-circle me-2"></i>Add Questions
                </h2>
                <p class="page-subtitle mb-0">Quiz #<?php echo $quiz_id; ?> - Add questions</p>
            </div>
            <a href="index.php" class="btn btn-success">
                <i class="bi bi-check-circle me-2"></i>Finish & Return
            </a>
        </div>
    </div>

    <div class="row fade-in">
        <div class="col-md-6 mb-4">
            <div class="card shadow-lg">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>New Question</h5>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($message)) echo "<div class='alert alert-success'>$message</div>"; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-grid me-2"></i>Question Type</label>
                            <select name="type" id="typeSelect" class="form-select" onchange="toggleAnswerType()" required>
                                <option value="single">Single Choice (Radio)</option>
                                <option value="multiple">Multiple Choice (Checkbox)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><i class="bi bi-chat-text me-2"></i>Question Text</label>
                            <textarea name="question_text" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-primary text-white">A</span>
                                <input type="text" name="option_a" class="form-control" placeholder="Option A" required>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-secondary text-white">B</span>
                                <input type="text" name="option_b" class="form-control" placeholder="Option B" required>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-success text-white">C</span>
                                <input type="text" name="option_c" class="form-control" placeholder="Option C" required>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-warning text-white">D</span>
                                <input type="text" name="option_d" class="form-control" placeholder="Option D" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><i class="bi bi-check-circle me-2"></i>Correct Answer</label>
                            <div class="card p-3 bg-light">
                                
                                <div id="singleParams">
                                    <small class="text-secondary mb-2 d-block">Select ONE correct option:</small>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="correct_single" value="A" required>
                                            <label class="form-check-label">A</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="correct_single" value="B">
                                            <label class="form-check-label">B</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="correct_single" value="C">
                                            <label class="form-check-label">C</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="correct_single" value="D">
                                            <label class="form-check-label">D</label>
                                        </div>
                                    </div>
                                </div>

                                <div id="multipleParams" style="display:none;">
                                    <small class="text-secondary mb-2 d-block">Select ALL correct options:</small>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="correct_multiple[]" value="A">
                                            <label class="form-check-label">A</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="correct_multiple[]" value="B">
                                            <label class="form-check-label">B</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="correct_multiple[]" value="C">
                                            <label class="form-check-label">C</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="correct_multiple[]" value="D">
                                            <label class="form-check-label">D</label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-plus-circle me-2"></i>Add Question
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-lg">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>Questions Added
                        <span class="badge bg-primary ms-2"><?php echo $questions->num_rows; ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if ($questions->num_rows > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php 
                            $cnt = 1;
                            while ($row = $questions->fetch_assoc()): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge bg-primary me-2"><?php echo $cnt++; ?></span>
                                                <strong><?php echo htmlspecialchars($row['question_text']); ?></strong>
                                            </div>
                                            <small class="text-muted d-block ms-4">
                                                Type: <strong><?php echo ucfirst($row['type']); ?></strong> | 
                                                Correct: <strong><?php echo $row['correct_option']; ?></strong>
                                            </small>
                                        </div>
                                        <div>
                                            <a href="edit_question.php?id=<?php echo $row['id']; ?>&quiz_id=<?php echo $quiz_id; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                            <a href="delete_question.php?id=<?php echo $row['id']; ?>&quiz_id=<?php echo $quiz_id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this question?')"><i class="bi bi-trash"></i></a>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <p class="text-secondary">No questions added yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAnswerType() {
    var type = document.getElementById("typeSelect").value;
    var singleDiv = document.getElementById("singleParams");
    var multipleDiv = document.getElementById("multipleParams");
    
    // Get all input fields to manage 'required' attribute
    var radioInputs = document.querySelectorAll('input[name="correct_single"]');
    var checkboxInputs = document.querySelectorAll('input[name="correct_multiple[]"]');

    if (type === "single") {
        singleDiv.style.display = "block";
        multipleDiv.style.display = "none";
        
        // Enable radio, Disable checkboxes
        radioInputs.forEach(el => el.required = true); 
        // We can't strictly require checkboxes in HTML5 easily, handled by backend or custom JS
    } else {
        singleDiv.style.display = "none";
        multipleDiv.style.display = "block";
        
        radioInputs.forEach(el => el.required = false);
    }
}
// Run on load to set initial state
window.onload = toggleAnswerType;
</script>

<?php include '../includes/footer.php'; ?>