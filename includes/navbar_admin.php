<nav class="navbar navbar-expand-lg navbar-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="../admin/index.php">
      <i class="bi bi-clipboard-check me-2"></i>Quiz Admin
    </a>
    <div class="navbar-nav ms-auto d-flex flex-row align-items-center gap-2">
      <span class="navbar-text me-3">
        <i class="bi bi-person-circle me-2"></i>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>
      </span>
      <a class="btn btn-outline-light btn-sm" href="../profile.php" title="Profile" aria-label="Profile">
        <i class="bi bi-person"></i>
      </a>
      <a class="btn btn-light btn-sm" href="../logout.php">
        <i class="bi bi-box-arrow-right me-1"></i>Logout
      </a>
    </div>
  </div>
</nav>
