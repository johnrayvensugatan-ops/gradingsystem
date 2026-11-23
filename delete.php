<?php
/**
 * Delete Student Grade Record (DELETE)
 * Course: PHP and MySQL Development
 */
require_once 'db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Support both POST (recommended from modal) and GET requests
$id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
} elseif (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
}

if ($id <= 0) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'text' => 'Invalid ID specified for deletion.'
    ];
    header("Location: index.php");
    exit();
}

// Retrieve details for descriptive flash message
$stmt = $conn->prepare("SELECT student_id, first_name, last_name, subject_code FROM student_grades WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$record = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$record) {
    $_SESSION['flash_message'] = [
        'type' => 'warning',
        'text' => 'Record already deleted or does not exist.'
    ];
    header("Location: index.php");
    exit();
}

// Perform DELETE using prepared statement
$del_stmt = $conn->prepare("DELETE FROM student_grades WHERE id = ?");
$del_stmt->bind_param("i", $id);

if ($del_stmt->execute()) {
    $_SESSION['flash_message'] = [
        'type' => 'success',
        'text' => "Grade record for {$record['first_name']} {$record['last_name']} ({$record['student_id']}) has been successfully removed."
    ];
} else {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'text' => "Failed to delete record: " . $del_stmt->error
    ];
}
$del_stmt->close();

header("Location: index.php");
exit();
?>
