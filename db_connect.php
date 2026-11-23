<?php
/**
 * Database Connection File
 * Course: PHP and MySQL Development (Web Systems Technologies)
 * 
 * Supports both local development (XAMPP) and live hosting (InfinityFree).
 * Contains verified InfinityFree MySQL credentials and auto-table initialization.
 */

// Detect if running locally (XAMPP) or on remote host (InfinityFree)
$is_local = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1']) || php_sapi_name() === 'cli';

// Disable default mysqli exceptions so we can handle errors gracefully
mysqli_report(MYSQLI_REPORT_OFF);

if ($is_local) {
    // ==========================================
    // 1. LOCAL XAMPP CONFIGURATION
    // ==========================================
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'student_grading_db';
    $db_port = 3306;

    // Connect to local MySQL server first without selecting database
    $conn = @new mysqli($db_host, $db_user, $db_pass, '', $db_port);

    if ($conn->connect_error) {
        die('<div style="font-family: Arial, sans-serif; padding: 25px; background-color: #fff3cd; color: #664d03; border: 1px solid #ffecb5; border-radius: 8px; margin: 30px auto; max-width: 700px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">' .
            '<h3 style="margin-top:0;">MySQL Server is Not Running in XAMPP</h3>' .
            '<p>Could not connect to MySQL server at <code>' . htmlspecialchars($db_host) . '</code>.</p>' .
            '<p><strong>How to fix:</strong></p>' .
            '<ol>' .
            '<li>Open the <strong>XAMPP Control Panel</strong>.</li>' .
            '<li>Click the <strong>Start</strong> button next to <strong>MySQL</strong>.</li>' .
            '<li>Refresh this page.</li>' .
            '</ol>' .
            '</div>');
    }

    // Automatically create database if it does not exist yet
    $conn->query("CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->select_db($db_name);

} else {
    // ==========================================
    // 2. INFINITYFREE HOSTING CONFIGURATION
    // ==========================================
    $db_host = 'sql212.infinityfree.com';
    $db_user = 'if0_43472398';
    $db_pass = 'KRSrrKkpTu6w';
    $db_name = 'if0_43472398_db_gradingsystem';
    $db_port = 3306;

    try {
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
    } catch (Throwable $e) {
        $conn = null;
    }

    if (!$conn || $conn->connect_error) {
        $error_msg = $conn ? $conn->connect_error : (isset($e) ? $e->getMessage() : 'Unable to connect to InfinityFree database host');
        die('<div style="font-family: Arial, sans-serif; padding: 25px; background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; border-radius: 8px; margin: 30px auto; max-width: 700px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">' .
            '<h3 style="margin-top:0;">InfinityFree Database Connection Failed</h3>' .
            '<p><strong>Error:</strong> ' . htmlspecialchars($error_msg) . '</p>' .
            '<p>Please verify your database credentials inside <code>db_connect.php</code>:</p>' .
            '<ul>' .
            '<li><strong>MySQL Hostname:</strong> ' . htmlspecialchars($db_host) . '</li>' .
            '<li><strong>MySQL Username:</strong> ' . htmlspecialchars($db_user) . '</li>' .
            '<li><strong>Database Name:</strong> ' . htmlspecialchars($db_name) . '</li>' .
            '</ul>' .
            '<p>Ensure your database <code>' . htmlspecialchars($db_name) . '</code> is created in the InfinityFree Control Panel.</p>' .
            '</div>');
    }
}

// Ensure UTF-8 Character Encoding
$conn->set_charset("utf8mb4");

// Automatically check and create the student_grades table + seed initial data if missing (works on both local & InfinityFree!)
$table_check = $conn->query("SHOW TABLES LIKE 'student_grades'");
if ($table_check && $table_check->num_rows === 0) {
    $create_table_sql = "CREATE TABLE `student_grades` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `student_id` VARCHAR(30) NOT NULL,
      `first_name` VARCHAR(100) NOT NULL,
      `last_name` VARCHAR(100) NOT NULL,
      `gender` ENUM('Male', 'Female', 'Other') NOT NULL DEFAULT 'Male',
      `course_year` VARCHAR(50) NOT NULL,
      `subject_code` VARCHAR(100) NOT NULL,
      `prelim` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
      `midterm` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
      `prefinal` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
      `final` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
      `final_grade` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
      `equivalent_grade` DECIMAL(3,2) NOT NULL DEFAULT 5.00,
      `remarks` ENUM('Passed', 'Failed', 'Incomplete', 'Dropped') NOT NULL DEFAULT 'Passed',
      `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uq_student_subject` (`student_id`, `subject_code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $conn->query($create_table_sql);

    // Seed initial sample records for demonstration
    $seed_sql = "INSERT INTO `student_grades` (`student_id`, `first_name`, `last_name`, `gender`, `course_year`, `subject_code`, `prelim`, `midterm`, `prefinal`, `final`, `final_grade`, `equivalent_grade`, `remarks`) VALUES
    ('2024-00101', 'John Kenneth', 'Dela Cruz', 'Male', 'BSIT 3-A', 'IT301 - Web Systems Technologies', 88.50, 91.00, 90.00, 93.50, 91.30, 1.50, 'Passed'),
    ('2024-00102', 'Maria Sofia', 'Santos', 'Female', 'BSIT 3-A', 'IT301 - Web Systems Technologies', 95.00, 94.00, 96.50, 98.00, 96.25, 1.00, 'Passed'),
    ('2024-00103', 'Carlos Miguel', 'Reyes', 'Male', 'BSIT 3-B', 'IT301 - Web Systems Technologies', 72.00, 68.50, 70.00, 69.00, 69.75, 5.00, 'Failed'),
    ('2024-00104', 'Alyssa Joy', 'Aquino', 'Female', 'BSCS 2-A', 'CS204 - Data Structures and Algorithms', 84.00, 86.50, 89.00, 87.50, 86.95, 2.00, 'Passed'),
    ('2024-00105', 'Christian Mark', 'Bautista', 'Male', 'BSIT 3-A', 'IT301 - Web Systems Technologies', 78.50, 80.00, 77.00, 79.50, 79.05, 2.75, 'Passed'),
    ('2024-00106', 'Bea Patricia', 'Gonzales', 'Female', 'BSIS 3-A', 'IS302 - Database Administration', 90.00, 89.00, 92.00, 91.50, 91.00, 1.50, 'Passed'),
    ('2024-00107', 'Joshua Neil', 'Espina', 'Male', 'BSIT 3-A', 'IT301 - Web Systems Technologies', 92.00, 93.50, 95.00, 94.00, 93.75, 1.25, 'Passed');";
    
    $conn->query($seed_sql);
}

/**
 * Grade Calculation Helper Function
 * Computes:
 * - Weighted Final Grade: Prelim (20%) + Midterm (20%) + Pre-Final (20%) + Final (40%)
 * - Equivalent Grade on Philippine College Scale (1.00 to 5.00)
 * - Remarks: 'Passed' or 'Failed'
 */
function calculate_grade_details($prelim, $midterm, $prefinal, $final) {
    $prelim   = floatval($prelim);
    $midterm  = floatval($midterm);
    $prefinal = floatval($prefinal);
    $final    = floatval($final);

    // Standard institutional weight: 20% Prelim, 20% Midterm, 20% Pre-Final, 40% Final
    $final_grade = round(($prelim * 0.20) + ($midterm * 0.20) + ($prefinal * 0.20) + ($final * 0.40), 2);

    // Equivalent Grade conversion table
    if ($final_grade >= 97.00) {
        $equivalent = 1.00;
    } elseif ($final_grade >= 94.00) {
        $equivalent = 1.25;
    } elseif ($final_grade >= 91.00) {
        $equivalent = 1.50;
    } elseif ($final_grade >= 88.00) {
        $equivalent = 1.75;
    } elseif ($final_grade >= 85.00) {
        $equivalent = 2.00;
    } elseif ($final_grade >= 82.00) {
        $equivalent = 2.25;
    } elseif ($final_grade >= 79.00) {
        $equivalent = 2.50;
    } elseif ($final_grade >= 76.00) {
        $equivalent = 2.75;
    } elseif ($final_grade >= 75.00) {
        $equivalent = 3.00;
    } else {
        $equivalent = 5.00;
    }

    $remarks = ($final_grade >= 75.00) ? 'Passed' : 'Failed';

    return [
        'final_grade'      => $final_grade,
        'equivalent_grade' => number_format($equivalent, 2),
        'remarks'          => $remarks
    ];
}
?>
