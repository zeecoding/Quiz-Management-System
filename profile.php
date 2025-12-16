<?php
session_start();
include 'config/db.php'; // Updated path

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Updated login redirect
    exit();
}

$message = "";
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass === $confirm_pass) {
        $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
        
        $sql = "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'";
        if ($conn->query($sql) === TRUE) {
            $message = "<div class='alert alert-success'>Password updated successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error updating password.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Passwords do not match!</div>";
    }
}
?>

<?php include 'includes/header.php'; ?>

<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <i class="bi bi-person-circle me-2"></i>Profile Settings
        </a>
        <div class="navbar-nav ms-auto">
            <?php if($_SESSION['role'] == 'admin'): ?>
                <a href="admin/index.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                </a>
            <?php else: ?>
                <a href="student/index.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center fade-in">
        <div class="col-md-6">
            <div class="card shadow-lg mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-lock me-2"></i>Change Password
                    </h5>
                </div>
                <div class="card-body p-4">
                    <?php echo $message; ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="bi bi-lock me-2"></i>New Password
                            </label>
                            <input type="password" name="new_password" class="form-control" placeholder="Enter new password" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="bi bi-lock-fill me-2"></i>Confirm New Password
                            </label>
                            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-check-circle me-2"></i>Update Password
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-body text-center p-4">
                    <i class="bi bi-person-circle" style="font-size: 3rem; color: var(--primary);"></i>
                    <h5 class="mt-3 mb-1"><?php echo htmlspecialchars($_SESSION['name']); ?></h5>
                    <p class="text-secondary mb-0">
                        <span class="badge bg-primary"><?php echo ucfirst($_SESSION['role']); ?></span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>