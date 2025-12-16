<?php
session_start();
include '../config/db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'];
$quiz_id = $_GET['quiz_id'];

// 1. Fetch Existing Data
$sql = "SELECT * FROM questions WHERE id = '$id'";
$result = $conn->query($sql);
$q = $result->fetch_assoc();

// Parse Correct Options (for Checkboxes)
// If correct_option is "A,B", explode creates array ["A", "B"]
$correct_answers_arr = explode(',', $q['correct_option']); 

// 2. Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type'];
    $question_text = $_POST['question_text'];
    $opt_a = $_POST['option_a'];
    $opt_b = $_POST['option_b'];
    $opt_c = $_POST['option_c'];
    $opt_d = $_POST['option_d'];
    
    // Determine Correct Answer based on Type
    $correct = "";
    if ($type == 'multiple') {
        if (isset($_POST['correct_multiple'])) {
            $correct = implode(',', $_POST['correct_multiple']);
        }
    } else {
        if (isset($_POST['correct_single'])) {
            $correct = $_POST['correct_single'];
        }
    }

    $update_sql = "UPDATE questions SET 
                   question_text='$question_text', 
                   option_a='$opt_a', option_b='$opt_b', option_c='$opt_c', option_d='$opt_d', 
                   correct_option='$correct',
                   type='$type'
                   WHERE id='$id'";

    if ($conn->query($update_sql) === TRUE) {
        header("Location: add_questions.php?quiz_id=" . $quiz_id);
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar_admin.php'; ?>

<div class="container mt-4 mb-5">
    <div class="page-header mb-4 fade-in">
        <h2 class="page-title"><i class="bi bi-pencil me-2"></i>Edit Question</h2>
    </div>
    
    <div class="row justify-content-center fade-in">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-question-circle me-2"></i>Question Details</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label">Question Type</label>
                            <select name="type" id="typeSelect" class="form-select" onchange="toggleAnswerType()" required>
                                <option value="single" <?php echo ($q['type'] == 'single') ? 'selected' : ''; ?>>Single Choice (Radio)</option>
                                <option value="multiple" <?php echo ($q['type'] == 'multiple') ? 'selected' : ''; ?>>Multiple Choice (Checkbox)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Question Text</label>
                            <textarea name="question_text" class="form-control" rows="3" required><?php echo htmlspecialchars($q['question_text']); ?></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-primary text-white">A</span>
                                <input type="text" name="option_a" class="form-control" value="<?php echo htmlspecialchars($q['option_a']); ?>" required>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-secondary text-white">B</span>
                                <input type="text" name="option_b" class="form-control" value="<?php echo htmlspecialchars($q['option_b']); ?>" required>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-success text-white">C</span>
                                <input type="text" name="option_c" class="form-control" value="<?php echo htmlspecialchars($q['option_c']); ?>" required>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-warning text-white">D</span>
                                <input type="text" name="option_d" class="form-control" value="<?php echo htmlspecialchars($q['option_d']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Correct Answer</label>
                            <div class="card p-3 bg-light">
                                
                                <div id="singleParams">
                                    <small class="text-secondary d-block mb-2">Select ONE correct option:</small>
                                    <div class="d-flex gap-3">
                                        <?php $opts = ['A','B','C','D']; ?>
                                        <?php foreach($opts as $opt): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="correct_single" value="<?php echo $opt; ?>" 
                                                    <?php echo ($q['correct_option'] == $opt) ? 'checked' : ''; ?>>
                                                <label class="form-check-label"><?php echo $opt; ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div id="multipleParams" style="display:none;">
                                    <small class="text-secondary d-block mb-2">Select ALL correct options:</small>
                                    <div class="d-flex gap-3">
                                        <?php foreach($opts as $opt): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="correct_multiple[]" value="<?php echo $opt; ?>" 
                                                    <?php echo (in_array($opt, $correct_answers_arr)) ? 'checked' : ''; ?>>
                                                <label class="form-check-label"><?php echo $opt; ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="add_questions.php?quiz_id=<?php echo $quiz_id; ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-check-circle me-2"></i>Update Question
                            </button>
                        </div>
                    </form>
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
    var radioInputs = document.querySelectorAll('input[name="correct_single"]');

    if (type === "single") {
        singleDiv.style.display = "block";
        multipleDiv.style.display = "none";
        radioInputs.forEach(el => el.required = true);
    } else {
        singleDiv.style.display = "none";
        multipleDiv.style.display = "block";
        radioInputs.forEach(el => el.required = false);
    }
}
window.onload = toggleAnswerType;
</script>

<?php include '../includes/footer.php'; ?>