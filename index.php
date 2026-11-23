<?php
/**
 * Dashboard & Student Records List (SELECT & READ)
 * Course: PHP and MySQL Development
 */
require_once 'db_connect.php';

$page_title = 'Dashboard';

// Search and Filter parameters
$search = trim($_GET['search'] ?? '');
$filter_remarks = trim($_GET['remarks'] ?? '');
$filter_course = trim($_GET['course'] ?? '');

// Base Query
$where_clauses = [];
$params = [];
$types = '';

if ($search !== '') {
    $where_clauses[] = "(student_id LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR subject_code LIKE ?)";
    $search_param = "%{$search}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ssss';
}

if ($filter_remarks !== '' && in_array($filter_remarks, ['Passed', 'Failed', 'Incomplete', 'Dropped'])) {
    $where_clauses[] = "remarks = ?";
    $params[] = $filter_remarks;
    $types .= 's';
}

if ($filter_course !== '') {
    $where_clauses[] = "course_year = ?";
    $params[] = $filter_course;
    $types .= 's';
}

$sql = "SELECT * FROM student_grades";
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}
$sql .= " ORDER BY id DESC";

// Execute Query with prepared statement
if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

// Fetch all records into an array
$students = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Overall System Statistics (for Summary Cards)
$stats_sql = "SELECT 
    COUNT(*) as total_records,
    SUM(CASE WHEN remarks = 'Passed' THEN 1 ELSE 0 END) as passed_count,
    SUM(CASE WHEN remarks = 'Failed' THEN 1 ELSE 0 END) as failed_count,
    AVG(final_grade) as average_grade
    FROM student_grades";
$stats_res = $conn->query($stats_sql);
$stats = $stats_res ? $stats_res->fetch_assoc() : [
    'total_records' => 0,
    'passed_count' => 0,
    'failed_count' => 0,
    'average_grade' => 0
];

$total_records = (int)($stats['total_records'] ?? 0);
$passed_count  = (int)($stats['passed_count'] ?? 0);
$failed_count  = (int)($stats['failed_count'] ?? 0);
$avg_grade     = round((float)($stats['average_grade'] ?? 0), 2);
$pass_rate     = ($total_records > 0) ? round(($passed_count / $total_records) * 100, 1) : 0;

// Distinct courses for filter dropdown
$courses_res = $conn->query("SELECT DISTINCT course_year FROM student_grades ORDER BY course_year ASC");
$distinct_courses = [];
if ($courses_res) {
    while ($c = $courses_res->fetch_assoc()) {
        if (!empty($c['course_year'])) {
            $distinct_courses[] = $c['course_year'];
        }
    }
}

include 'header.php';
?>

<!-- Header / Page Title -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center pb-3 mb-4 border-bottom gap-2">
    <div>
        <h2 class="h3 fw-bold text-dark mb-0">Student Grading Dashboard</h2>
    </div>
    <div class="d-flex gap-2">
        <a href="create.php" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Add Student Grade</span>
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary d-flex align-items-center gap-1">
            <i class="bi bi-printer"></i>
            <span class="d-none d-sm-inline">Print</span>
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card p-3 d-flex align-items-center gap-3">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Students</span>
                <h3 class="fw-bold mb-0 text-dark"><?php echo $total_records; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card p-3 d-flex align-items-center gap-3">
            <div class="stat-icon bg-success bg-opacity-10 text-success">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Passed</span>
                <h3 class="fw-bold mb-0 text-success"><?php echo $passed_count; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card p-3 d-flex align-items-center gap-3">
            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Failed</span>
                <h3 class="fw-bold mb-0 text-danger"><?php echo $failed_count; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card p-3 d-flex align-items-center gap-3">
            <div class="stat-icon bg-info bg-opacity-10 text-info">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Passing Rate</span>
                <h3 class="fw-bold mb-0 text-dark"><?php echo $pass_rate; ?>% <small class="text-muted fs-6 fw-normal">(Avg: <?php echo $avg_grade; ?>%)</small></h3>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="index.php" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by ID, Name, or Subject..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="course" class="form-select">
                    <option value="">All Courses / Sections</option>
                    <?php foreach ($distinct_courses as $c): ?>
                        <option value="<?php echo htmlspecialchars($c); ?>" <?php echo ($filter_course === $c) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="remarks" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="Passed" <?php echo ($filter_remarks === 'Passed') ? 'selected' : ''; ?>>Passed</option>
                    <option value="Failed" <?php echo ($filter_remarks === 'Failed') ? 'selected' : ''; ?>>Failed</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <?php if ($search !== '' || $filter_remarks !== '' || $filter_course !== ''): ?>
                    <a href="index.php" class="btn btn-outline-secondary" title="Reset Filters">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Student Grades Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold text-dark">
            <i class="bi bi-table text-primary me-2"></i> Student Records (<?php echo count($students); ?> found)
        </h5>
        <span class="text-muted small">Weights: Prelim (20%) + Midterm (20%) + Pre-Final (20%) + Final (40%)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th scope="col" class="ps-3">Student ID</th>
                    <th scope="col">Student Name</th>
                    <th scope="col">Course & Year</th>
                    <th scope="col">Subject</th>
                    <th scope="col" class="text-center">Prelim</th>
                    <th scope="col" class="text-center">Midterm</th>
                    <th scope="col" class="text-center">Pre-Final</th>
                    <th scope="col" class="text-center">Final</th>
                    <th scope="col" class="text-center">Final Grade</th>
                    <th scope="col" class="text-center">Equivalent</th>
                    <th scope="col" class="text-center">Remarks</th>
                    <th scope="col" class="text-center pe-3 no-print">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="12" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p class="mb-1 fw-semibold">No student records found.</p>
                            <span class="small">Try adjusting your search criteria or add a new record.</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $row): ?>
                        <tr>
                            <td class="ps-3 fw-semibold text-primary">
                                <?php echo htmlspecialchars($row['student_id']); ?>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">
                                    <?php echo htmlspecialchars($row['last_name'] . ', ' . $row['first_name']); ?>
                                </div>
                                <small class="text-muted"><?php echo htmlspecialchars($row['gender'] ?? 'N/A'); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?php echo htmlspecialchars($row['course_year']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="small text-truncate d-inline-block" style="max-width: 180px;" title="<?php echo htmlspecialchars($row['subject_code']); ?>">
                                    <?php echo htmlspecialchars($row['subject_code']); ?>
                                </span>
                            </td>
                            <td class="text-center"><?php echo number_format($row['prelim'], 2); ?></td>
                            <td class="text-center"><?php echo number_format($row['midterm'], 2); ?></td>
                            <td class="text-center"><?php echo number_format($row['prefinal'], 2); ?></td>
                            <td class="text-center"><?php echo number_format($row['final'], 2); ?></td>
                            <td class="text-center fw-bold">
                                <?php echo number_format($row['final_grade'], 2); ?>%
                            </td>
                            <td class="text-center fw-semibold text-primary">
                                <?php echo number_format($row['equivalent_grade'], 2); ?>
                            </td>
                            <td class="text-center">
                                <?php if ($row['remarks'] === 'Passed'): ?>
                                    <span class="badge-passed"><i class="bi bi-check-circle me-1"></i>Passed</span>
                                <?php else: ?>
                                    <span class="badge-failed"><i class="bi bi-x-circle me-1"></i>Failed</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-3 no-print">
                                <div class="btn-group" role="group">
                                    <a href="view.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-info btn-action" title="View Grade Slip">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary btn-action" title="Edit Record">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-action" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteConfirmModal"
                                            data-id="<?php echo $row['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>"
                                            data-subject="<?php echo htmlspecialchars($row['subject_code']); ?>"
                                            title="Delete Record">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Delete Confirmation -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="delete.php">
                <input type="hidden" name="id" id="deleteRecordId" value="">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="mb-2">Are you sure you want to permanently delete the grade record for:</p>
                    <p class="fs-5 fw-bold text-dark text-center bg-light py-2 rounded border" id="deleteStudentName">Student Name</p>
                    <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i>This action cannot be undone.</p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Delete Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
