<?php
/**
 * Edit / Update Student Grade Record (UPDATE)
 * Course: PHP and MySQL Development
 */
require_once 'db_connect.php';

$page_title = 'Edit Student Grade';
$errors = [];

// Validate ID from GET request
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'text' => 'Invalid student record ID specified.'
    ];
    header("Location: index.php");
    exit();
}

// Fetch existing record
$stmt = $conn->prepare("SELECT * FROM student_grades WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$record) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'text' => 'Student record not found in database.'
    ];
    header("Location: index.php");
    exit();
}

// Prefill form values
$student_id   = $record['student_id'];
$first_name   = $record['first_name'];
$last_name    = $record['last_name'];
$gender       = $record['gender'];
$course_year  = $record['course_year'];
$subject_code = $record['subject_code'];
$prelim       = $record['prelim'];
$midterm      = $record['midterm'];
$prefinal     = $record['prefinal'];
$final        = $record['final'];

// Process POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    if ($student_id === '') $errors[] = 'Student ID is required.';
    if ($first_name === '') $errors[] = 'First Name is required.';
    if ($last_name === '')  $errors[] = 'Last Name is required.';
    if ($course_year === '') $errors[] = 'Course & Year is required.';
    if ($subject_code === '') $errors[] = 'Subject Code is required.';

    $grades = ['Prelim' => $prelim, 'Midterm' => $midterm, 'Pre-Final' => $prefinal, 'Final' => $final];
    foreach ($grades as $name => $val) {
        if ($val === '' || !is_numeric($val) || $val < 0 || $val > 100) {
            $errors[] = "{$name} grade must be a valid number between 0 and 100.";
        }
    }

    // Check unique constraint excluding current ID
    if (empty($errors)) {
        $check_stmt = $conn->prepare("SELECT id FROM student_grades WHERE student_id = ? AND subject_code = ? AND id != ? LIMIT 1");
        $check_stmt->bind_param("ssi", $student_id, $subject_code, $id);
        $check_stmt->execute();
        $check_res = $check_stmt->get_result();
        if ($check_res && $check_res->num_rows > 0) {
            $errors[] = "Another record already exists for Student ID '{$student_id}' in subject '{$subject_code}'.";
        }
        $check_stmt->close();
    }

    if (empty($errors)) {
        // Recompute calculations
        $calc = calculate_grade_details($prelim, $midterm, $prefinal, $final);
        $final_grade      = $calc['final_grade'];
        $equivalent_grade = $calc['equivalent_grade'];
        $remarks          = $calc['remarks'];

        $update_sql = "UPDATE student_grades SET 
            student_id = ?, 
            first_name = ?, 
            last_name = ?, 
            gender = ?, 
            course_year = ?, 
            subject_code = ?, 
            prelim = ?, 
            midterm = ?, 
            prefinal = ?, 
            final = ?, 
            final_grade = ?, 
            equivalent_grade = ?, 
            remarks = ? 
            WHERE id = ?";

        $stmt = $conn->prepare($update_sql);
        if ($stmt) {
            $stmt->bind_param(
                "ssssssddddddsi",
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
                $remarks,
                $id
            );

            if ($stmt->execute()) {
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => "Record for {$first_name} {$last_name} ({$student_id}) has been successfully updated!"
                ];
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Failed to update record: " . $stmt->error;
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
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Student Grade</li>
            </ol>
        </nav>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="h5 fw-bold mb-0 text-dark">
                        <i class="bi bi-pencil-square text-primary me-2"></i> Edit Student Grade Record
                    </h4>
                    <span class="text-muted small">Update student information and adjust period grades</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="view.php?id=<?php echo $id; ?>" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-eye me-1"></i> View Slip
                    </a>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
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

                <form method="POST" action="edit.php?id=<?php echo $id; ?>" novalidate>
                    <div class="row g-4">
                        <!-- Left Column: Student Information -->
                        <div class="col-md-7">
                            <h6 class="text-uppercase text-secondary fw-bold small mb-3 border-bottom pb-2">
                                <i class="bi bi-person-vcard me-1"></i> Student Information
                            </h6>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label for="student_id" class="form-label fw-semibold">Student ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>" required>
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
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="last_name" class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="course_year" class="form-label fw-semibold">Course & Year <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="course_year" name="course_year" value="<?php echo htmlspecialchars($course_year); ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="subject_code" class="form-label fw-semibold">Subject / Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="subject_code" name="subject_code" value="<?php echo htmlspecialchars($subject_code); ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Period Grades & Live Calculation -->
                        <div class="col-md-5">
                            <h6 class="text-uppercase text-secondary fw-bold small mb-3 border-bottom pb-2">
                                <i class="bi bi-calculator me-1"></i> Period Grades (0 - 100)
                            </h6>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label for="prelim" class="form-label small fw-semibold">Prelim (20%)</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="prelim" name="prelim" value="<?php echo htmlspecialchars($prelim); ?>" required>
                                </div>
                                <div class="col-6">
                                    <label for="midterm" class="form-label small fw-semibold">Midterm (20%)</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="midterm" name="midterm" value="<?php echo htmlspecialchars($midterm); ?>" required>
                                </div>
                                <div class="col-6">
                                    <label for="prefinal" class="form-label small fw-semibold">Pre-Final (20%)</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="prefinal" name="prefinal" value="<?php echo htmlspecialchars($prefinal); ?>" required>
                                </div>
                                <div class="col-6">
                                    <label for="final" class="form-label small fw-semibold">Final (40%)</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="final" name="final" value="<?php echo htmlspecialchars($final); ?>" required>
                                </div>
                            </div>

                            <!-- Live Calculation Preview Card -->
                            <div class="grade-preview-card text-center">
                                <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Updated Final Grade</span>
                                <div class="display-6 fw-bold text-primary mb-1" id="previewFinalGrade"><?php echo number_format($record['final_grade'], 2); ?>%</div>
                                <div class="d-flex justify-content-center align-items-center gap-3">
                                    <span class="small text-muted">Equivalent: <strong class="text-dark" id="previewEquivalent"><?php echo number_format($record['equivalent_grade'], 2); ?></strong></span>
                                    <span class="badge <?php echo ($record['remarks'] === 'Passed') ? 'bg-success' : 'bg-danger'; ?>" id="previewRemarks">
                                        <?php echo htmlspecialchars($record['remarks']); ?>
                                    </span>
                                </div>
                                <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">
                                    Formula: (Prelim&times;0.2) + (Midterm&times;0.2) + (PreFinal&times;0.2) + (Final&times;0.4)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <span class="text-muted small">
                            <i class="bi bi-clock-history me-1"></i> Last updated: <?php echo date('M d, Y h:i A', strtotime($record['updated_at'])); ?>
                        </span>
                        <div class="d-flex gap-2">
                            <a href="index.php" class="btn btn-light px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="bi bi-check2-circle me-1"></i> Update Record
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
