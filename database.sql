-- Student Grading System Database Dump
-- 100% Compatible with InfinityFree phpMyAdmin and local XAMPP

-- Drop table if exists to allow clean re-import
DROP TABLE IF EXISTS `student_grades`;

-- Table structure for table `student_grades`
CREATE TABLE `student_grades` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Initial Data for Testing and Demonstration
INSERT INTO `student_grades` (`student_id`, `first_name`, `last_name`, `gender`, `course_year`, `subject_code`, `prelim`, `midterm`, `prefinal`, `final`, `final_grade`, `equivalent_grade`, `remarks`) VALUES
('2024-00101', 'John Kenneth', 'Dela Cruz', 'Male', 'BSIT 3-A', 'IT301 - Web Systems Technologies', 88.50, 91.00, 90.00, 93.50, 91.30, 1.50, 'Passed'),
('2024-00102', 'Maria Sofia', 'Santos', 'Female', 'BSIT 3-A', 'IT301 - Web Systems Technologies', 95.00, 94.00, 96.50, 98.00, 96.25, 1.00, 'Passed'),
('2024-00103', 'Carlos Miguel', 'Reyes', 'Male', 'BSIT 3-B', 'IT301 - Web Systems Technologies', 72.00, 68.50, 70.00, 69.00, 69.75, 5.00, 'Failed'),
('2024-00104', 'Alyssa Joy', 'Aquino', 'Female', 'BSCS 2-A', 'CS204 - Data Structures and Algorithms', 84.00, 86.50, 89.00, 87.50, 86.95, 2.00, 'Passed'),
('2024-00105', 'Christian Mark', 'Bautista', 'Male', 'BSIT 3-A', 'IT301 - Web Systems Technologies', 78.50, 80.00, 77.00, 79.50, 79.05, 2.75, 'Passed'),
('2024-00106', 'Bea Patricia', 'Gonzales', 'Female', 'BSIS 3-A', 'IS302 - Database Administration', 90.00, 89.00, 92.00, 91.50, 91.00, 1.50, 'Passed'),
('2024-00107', 'Joshua Neil', 'Espina', 'Male', 'BSIT 3-A', 'IT301 - Web Systems Technologies', 92.00, 93.50, 95.00, 94.00, 93.75, 1.25, 'Passed');
