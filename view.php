<?php
/**
 * View Student Grade Slip / Detailed Record
 * Course: PHP and MySQL Development
 */
require_once 'db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'text' => 'Invalid student ID specified.'
    ];
    header("Location: index.php");
    exit();
}

// Fetch record
$stmt = $conn->prepare("SELECT * FROM student_grades WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$student) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'text' => 'Student record not found.'
    ];
    header("Location: index.php");
    exit();
}

$page_title = 'Grade Slip - ' . $student['first_name'] . ' ' . $student['last_name'];
include 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Actions & Navigation Bar (hidden on print) -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
            </a>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-primary shadow-sm">
                    <i class="bi bi-printer me-1"></i> Print Grade Slip
                </button>
                <a href="edit.php?id=<?php echo $student['id']; ?>" class="btn btn-outline-primary">
                    <i class="bi bi-pencil-square me-1"></i> Edit Record
                </a>
            </div>
        </div>

        <!-- Grade Slip Card -->
        <div class="card border shadow-sm print-card">
            <!-- Institutional Header -->
            <div class="card-header bg-white py-4 text-center border-bottom">
                <div class="d-flex justify-content-center align-items-center gap-2 mb-1">
                    <i class="bi bi-mortarboard-fill fs-2 text-primary"></i>
                    <h4 class="fw-bold mb-0 text-dark">STUDENT OFFICIAL GRADE SLIP</h4>
                </div>
                <p class="text-muted small mb-0">Department of Web Systems Technologies &bull; Academic Grading Evaluation</p>
            </div>

            <div class="card-body p-4">
                <!-- Student Info Grid -->
                <div class="row g-3 mb-4 bg-light p-3 rounded border">
                    <div class="col-sm-6">
                        <small class="text-muted text-uppercase fw-semibold d-block">Student ID</small>
                        <span class="fs-5 fw-bold text-primary"><?php echo htmlspecialchars($student['student_id']); ?></span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted text-uppercase fw-semibold d-block">Student Name</small>
                        <span class="fs-5 fw-bold text-dark"><?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?></span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted text-uppercase fw-semibold d-block">Course & Year</small>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($student['course_year']); ?></span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted text-uppercase fw-semibold d-block">Gender</small>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($student['gender']); ?></span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted text-uppercase fw-semibold d-block">Subject / Course Unit</small>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($student['subject_code']); ?></span>
                    </div>
                </div>

                <!-- Detailed Breakdown Table -->
                <h6 class="text-uppercase text-secondary fw-bold small mb-2">Grading Period Breakdown</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered text-center align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Grading Period</th>
                                <th>Weight</th>
                                <th>Score / Grade</th>
                                <th>Weighted Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-semibold text-start ps-3">Prelim</td>
                                <td>20%</td>
                                <td class="fw-semibold"><?php echo number_format($student['prelim'], 2); ?>%</td>
                                <td><?php echo number_format($student['prelim'] * 0.20, 2); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-start ps-3">Midterm</td>
                                <td>20%</td>
                                <td class="fw-semibold"><?php echo number_format($student['midterm'], 2); ?>%</td>
                                <td><?php echo number_format($student['midterm'] * 0.20, 2); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-start ps-3">Pre-Final</td>
                                <td>20%</td>
                                <td class="fw-semibold"><?php echo number_format($student['prefinal'], 2); ?>%</td>
                                <td><?php echo number_format($student['prefinal'] * 0.20, 2); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-start ps-3">Final Exam & Project</td>
                                <td>40%</td>
                                <td class="fw-semibold"><?php echo number_format($student['final'], 2); ?>%</td>
                                <td><?php echo number_format($student['final'] * 0.40, 2); ?></td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fs-6">
                                <th colspan="2" class="text-start ps-3">Overall Final Computed Grade:</th>
                                <th class="text-primary fs-5"><?php echo number_format($student['final_grade'], 2); ?>%</th>
                                <th><?php echo number_format($student['final_grade'], 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Final Evaluation Summary Banner -->
                <div class="row g-3 text-center mb-4">
                    <div class="col-sm-6">
                        <div class="p-3 border rounded bg-light">
                            <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Equivalent Grade</span>
                            <span class="display-6 fw-bold text-primary"><?php echo number_format($student['equivalent_grade'], 2); ?></span>
                            <small class="text-muted d-block mt-1">Scale: 1.00 (Highest) - 5.00 (Failed)</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 border rounded bg-light">
                            <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Academic Status</span>
                            <div>
                                <?php if ($student['remarks'] === 'Passed'): ?>
                                    <span class="badge bg-success fs-5 px-4 py-2">PASSED</span>
                                <?php else: ?>
                                    <span class="badge bg-danger fs-5 px-4 py-2">FAILED</span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted d-block mt-1">Passing Benchmark: 75.00%</small>
                        </div>
                    </div>
                </div>

                <!-- Signatures Section for Printed Slip -->
                <div class="row pt-4 mt-4 border-top text-center">
                    <div class="col-6">
                        <div style="border-bottom: 1px solid #333; width: 80%; margin: 40px auto 5px;"></div>
                        <small class="text-muted fw-semibold">Subject Instructor</small>
                    </div>
                    <div class="col-6">
                        <div style="border-bottom: 1px solid #333; width: 80%; margin: 40px auto 5px;"></div>
                        <small class="text-muted fw-semibold">College Registrar / Dean</small>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white text-muted small d-flex justify-content-between align-items-center py-3">
                <span>Record Created: <?php echo date('M d, Y', strtotime($student['created_at'])); ?></span>
                <span>System Verified &bull; Web Systems Technologies</span>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
