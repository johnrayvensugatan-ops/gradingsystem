<?php
/**
 * Header Component
 * Bootstrap 5 + Navigation Bar
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' : ''; ?>Student Grading System</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <i class="bi bi-mortarboard-fill fs-3 text-warning"></i>
            <span>Student Grading System</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page === 'index.php') ? 'active fw-bold' : ''; ?>" href="index.php">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page === 'create.php') ? 'active fw-bold' : ''; ?>" href="create.php">
                        <i class="bi bi-plus-circle me-1"></i> Add Record
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-primary d-none d-md-inline-block px-3 py-2">
                    <i class="bi bi-code-slash me-1"></i> Web Systems Technologies
                </span>
                <a href="create.php" class="btn btn-warning btn-sm fw-semibold shadow-sm">
                    <i class="bi bi-person-plus-fill me-1"></i> New Student
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Main Container -->
<main class="py-4">
    <div class="container">
        <!-- Flash Notification Messages -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo htmlspecialchars($_SESSION['flash_message']['type']); ?> alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-<?php echo ($_SESSION['flash_message']['type'] === 'success') ? 'check-circle-fill' : 'exclamation-triangle-fill'; ?> me-2"></i>
                <?php 
                    echo htmlspecialchars($_SESSION['flash_message']['text']); 
                    unset($_SESSION['flash_message']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
