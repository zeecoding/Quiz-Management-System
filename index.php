<?php
session_start();
include 'config/db.php'; 

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. Check if user exists AND fetch Role Name via JOIN
    // This connects the 'users' table with the 'roles' table
    $sql = "SELECT users.*, roles.role_name 
            FROM users 
            JOIN roles ON users.role_id = roles.id 
            WHERE users.email = '$email'";
            
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // 2. Verify Password
        if (password_verify($password, $row['password'])) {
            
            // 3. Set Session Variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['full_name'];
            
            // IMPORTANT: We store 'role_name' (admin/student) in the session
            // This ensures backward compatibility with all your other files
            $_SESSION['role'] = $row['role_name']; 

            // 4. Redirect based on Role
            if ($row['role_name'] == 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: student/index.php");
            }
            exit();
        } else {
            $message = "Invalid Password!";
        }
    } else {
        $message = "User not found!";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-5 fade-in">
            <div class="text-center mb-4">
                <div class="icon-wrapper mx-auto">
                    <i class="bi bi-clipboard-check" style="font-size: 2rem;"></i>
                </div>
                <h2 class="page-title">Welcome Back</h2>
                <p class="text-secondary">Sign in to your account to continue</p>
            </div>
            
            <div class="card shadow-lg">
                <div class="card-body p-4">
                    <?php if($message) { echo "<div class='alert alert-danger'><i class='bi bi-exclamation-triangle me-2'></i>$message</div>"; } ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-envelope me-2"></i>Email Address
                            </label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="bi bi-lock me-2"></i>Password
                            </label>
                            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                        </button>
                    </form>
                    <div class="mt-4 text-center">
                        <p class="text-secondary mb-0">Don't have an account? 
                            <a href="register.php" class="text-primary text-decoration-none fw-semibold">Register here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>