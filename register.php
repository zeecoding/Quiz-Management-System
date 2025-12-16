<?php
include 'config/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $role_id = $_POST['role_id']; // Now receiving an ID, not a string

    // Hash the password
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

    // Check if email already exists
    $check = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($check);

    if ($result->num_rows > 0) {
        $message = "Email already exists!";
    } else {
        // Insert User using role_id
        $sql = "INSERT INTO users (full_name, email, password, role_id) 
                VALUES ('$name', '$email', '$hashed_password', '$role_id')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Registration Successful! <a href='index.php'>Login Here</a>";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

// Fetch Roles Dynamically for the Dropdown
$roles_sql = "SELECT * FROM roles";
$roles_result = $conn->query($roles_sql);
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6 fade-in">
            <div class="text-center mb-4">
                <div class="icon-wrapper mx-auto">
                    <i class="bi bi-person-plus" style="font-size: 2rem;"></i>
                </div>
                <h2 class="page-title">Create Account</h2>
                <p class="text-secondary">Join us to start taking quizzes</p>
            </div>
            
            <div class="card shadow-lg">
                <div class="card-body p-4">
                    <?php if($message) { echo "<div class='alert alert-info'><i class='bi bi-info-circle me-2'></i>$message</div>"; } ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-person me-2"></i>Full Name
                            </label>
                            <input type="text" name="full_name" class="form-control" placeholder="Enter your full name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-envelope me-2"></i>Email Address
                            </label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-lock me-2"></i>Password
                            </label>
                            <input type="password" name="password" class="form-control" placeholder="Create a password" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="bi bi-person-badge me-2"></i>Register As
                            </label>
                            <select name="role_id" class="form-select" required>
                                <?php 
                                if ($roles_result->num_rows > 0) {
                                    while($role = $roles_result->fetch_assoc()) {
                                        // Use role_id as value, display_name for text
                                        echo "<option value='" . $role['id'] . "'>" . htmlspecialchars($role['display_name']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-person-plus me-2"></i>Create Account
                        </button>
                    </form>
                    <div class="mt-4 text-center">
                        <p class="text-secondary mb-0">Already have an account? 
                            <a href="index.php" class="text-primary text-decoration-none fw-semibold">Sign in here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>