<?php
/**
 * Create / Add New Student Grade Record (INSERT)
 * Course: PHP and MySQL Development
 */
require_once 'db_connect.php';

$page_title = 'Add Student Grade';
$errors = [];

// Form values initialization
$student_id   = '';
$first_name   = '';
$last_name    = '';
$gender       = 'Male';
$course_year  = '';
$subject_code = '';
$prelim       = '';
$midterm      = '';
$prefinal     = '';
$final        = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and trim inputs
    $student_id   = trim($_POST['student_id'] ?? '');
    $first_name   = trim($_POST['first_name'] ?? '');
    $last_name    = trim($_POST['last_name'] ?? '');
    $gender       = trim($_POST['gender'] ?? 'Male');
    $course_year  = trim($_POST['course_year'] ?? '');
    $subject_code = trim($_POST['subject_code'] ?? '');
    
    $prelim   = $_POST['prelim'] ?? '';
    $midterm  = $_POST['midterm'] ?? '';
    $prefinal = $_POST['prefinal'] ?? '';
    $final    = $_POST['final'] ?? '';

    // Validation
    if ($student_id === '') {
        $errors[] = 'Student ID is required.';
    }
    if ($first_name === '') {
        $errors[] = 'First Name is required.';
    }
    if ($last_name === '') {
        $errors[] = 'Last Name is required.';
    }
    if ($course_year === '') {
        $errors[] = 'Course & Year is required.';
    }
    if ($subject_code === '') {
        $errors[] = 'Subject Code is required.';
    }

    // Validate Grade ranges (0 to 100)
    $grades = ['Prelim' => $prelim, 'Midterm' => $midterm, 'Pre-Final' => $prefinal, 'Final' => $final];
    foreach ($grades as $name => $val) {
        if ($val === '' || !is_numeric($val) || $val < 0 || $val > 100) {
            $errors[] = "{$name} grade must be a valid number between 0 and 100.";
        }
    }

    // Check if student already has a record for this specific subject
    if (empty($errors)) {
        $check_stmt = $conn->prepare("SELECT id FROM student_grades WHERE student_id = ? AND subject_code = ? LIMIT 1");
        $check_stmt->bind_param("ss", $student_id, $subject_code);
        $check_stmt->execute();
        $check_res = $check_stmt->get_result();
        if ($check_res && $check_res->num_rows > 0) {
            $errors[] = "A record for Student ID '{$student_id}' in subject '{$subject_code}' already exists.";
        }
        $check_stmt->close();
    }

    // If no validation errors, proceed with INSERT
    if (empty($errors)) {
        // Compute calculated fields
        $calc = calculate_grade_details($prelim, $midterm, $prefinal, $final);
        $final_grade      = $calc['final_grade'];
        $equivalent_grade = $calc['equivalent_grade'];
        $remarks          = $calc['remarks'];

        $insert_sql = "INSERT INTO student_grades 
            (student_id, first_name, last_name, gender, course_year, subject_code, prelim, midterm, prefinal, final, final_grade, equivalent_grade, remarks) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($insert_sql);
        if ($stmt) {
            $stmt->bind_param(
                "ssssssdddddds",
                $student_id,
                $first_name,
                $last_name,
                $gender,
                $course_year,
                $subject_code,
                $prelim,
                $midterm,
                $prefinal,
                $final,
                $final_grade,
                $equivalent_grade,
                $remarks
            );

            if ($stmt->execute()) {
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => "Student record for {$first_name} {$last_name} ({$student_id}) added successfully!"
                ];
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Failed to insert record: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errors[] = "Database prepare error: " . $conn->error;
        }
    }
}

include 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Breadcrumb & Title -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Student Grade</li>
            </ol>
        </nav>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="h5 fw-bold mb-0 text-dark">
                        <i class="bi bi-person-plus-fill text-primary me-2"></i> Add New Student Grade
                    </h4>
                    <span class="text-muted small">Enter student details and grading period marks</span>
                </div>
                <a href="index.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
            
            <div class="card-body p-4">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-4">
                        <strong class="d-block mb-1"><i class="bi bi-exclamation-octagon-fill me-1"></i> Please fix the following errors:</strong>
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $err): ?>
                                <li><?php echo htmlspecialchars($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="create.php" novalidate>
                    <div class="row g-4">
                        <!-- Left Column: Student Information -->
                        <div class="col-md-7">
                            <h6 class="text-uppercase text-secondary fw-bold small mb-3 border-bottom pb-2">
                                <i class="bi bi-person-vcard me-1"></i> Student Information
                            </h6>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label for="student_id" class="form-label fw-semibold">Student ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="student_id" name="student_id" placeholder="e.g. 2024-00108" value="<?php echo htmlspecialchars($student_id); ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="gender" class="form-label fw-semibold">Gender</label>
                                    <select class="form-select" id="gender" name="gender">
                                        <option value="Male" <?php echo ($gender === 'Male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo ($gender === 'Female') ? 'selected' : ''; ?>>Female</option>
                                        <option value="Other" <?php echo ($gender === 'Other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label for="first_name" class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="last_name" class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="course_year" class="form-label fw-semibold">Course & Year <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="course_year" name="course_year" placeholder="e.g. BSIT 3-A" value="<?php echo htmlspecialchars($course_year); ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="subject_code" class="form-label fw-semibold">Subject / Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="subject_code" name="subject_code" placeholder="e.g. IT301 - Web Systems" value="<?php echo htmlspecialchars($subject_code); ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Period Grades & Real-time Live Calculation -->
                        <div class="col-md-5">
                            <h6 class="text-uppercase text-secondary fw-bold small mb-3 border-bottom pb-2">
                                <i class="bi bi-calculator me-1"></i> Period Grades (0 - 100)
                            </h6>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label for="prelim" class="form-label small fw-semibold">Prelim (20%)</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="prelim" name="prelim" placeholder="0.00" value="<?php echo htmlspecialchars($prelim); ?>" required>
                                </div>
                                <div class="col-6">
                                    <label for="midterm" class="form-label small fw-semibold">Midterm (20%)</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="midterm" name="midterm" placeholder="0.00" value="<?php echo htmlspecialchars($midterm); ?>" required>
                                </div>
                                <div class="col-6">
                                    <label for="prefinal" class="form-label small fw-semibold">Pre-Final (20%)</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="prefinal" name="prefinal" placeholder="0.00" value="<?php echo htmlspecialchars($prefinal); ?>" required>
                                </div>
                                <div class="col-6">
                                    <label for="final" class="form-label small fw-semibold">Final (40%)</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="final" name="final" placeholder="0.00" value="<?php echo htmlspecialchars($final); ?>" required>
                                </div>
                            </div>

                            <!-- Live Calculation Preview Card -->
                            <div class="grade-preview-card text-center">
                                <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Live Computation Preview</span>
                                <div class="display-6 fw-bold text-primary mb-1" id="previewFinalGrade">0.00%</div>
                                <div class="d-flex justify-content-center align-items-center gap-3">
                                    <span class="small text-muted">Equivalent: <strong class="text-dark" id="previewEquivalent">5.00</strong></span>
                                    <span class="badge bg-secondary" id="previewRemarks">Pending</span>
                                </div>
                                <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">
                                    Formula: (Prelim&times;0.2) + (Midterm&times;0.2) + (PreFinal&times;0.2) + (Final&times;0.4)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="index.php" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-save me-1"></i> Save Student Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
