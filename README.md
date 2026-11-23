# Student Grading System (Web Systems Technologies)

A responsive web-based CRUD application developed with **PHP**, **MySQL**, **HTML5**, **CSS3**, and **Bootstrap 5**. Designed for managing student grades, calculating weighted academic marks, tracking student performance, and generating printable official grade slips.

---

## 📌 Project Information

- **Course:** PHP and MySQL Development (Web Systems Technologies)
- **Project Title:** Student Grading System
- **Framework & Libraries:** Bootstrap 5.3, Bootstrap Icons, Custom CSS/JS
- **Backend:** PHP 8+ with MySQLi (Object-Oriented & Prepared Statements)
- **Database:** MySQL / MariaDB (`student_grading_db`)
- **Deployment Platform:** InfinityFree Free Hosting (`https://yourname.epizy.com`)
- **Instructor Submission Email:** `kenthjoshuaespina@isufst.edu.ph`
- **Presentation Time:** 5–7 Minutes

---

## ✨ Features & CRUD Capabilities

| CRUD Operation | Implemented Feature | Description |
|---|---|---|
| **CREATE (Insert)** | `create.php` & Dashboard Modal | Add new student records with validation, duplicate checks, and live grade calculation. |
| **READ (Select)** | `index.php` & `view.php` | View all records in a responsive Bootstrap table with real-time summary statistics, keyword search, course filtering, and printable official grade slips. |
| **UPDATE (Edit)** | `edit.php` | Edit existing student data and period marks with real-time recalculation of final grade, equivalent GPA, and passing remarks. |
| **DELETE (Remove)** | `delete.php` & Confirmation Modal | Securely delete student records using prepared statements and modal confirmation with flash alerts. |

### 🎯 Additional Academic Features
- **Real-Time Grade Calculation (JavaScript):** As you type grades for Prelim (20%), Midterm (20%), Pre-Final (20%), and Final Exam (40%), the final grade and equivalent grade update instantly.
- **Equivalent Grade Scale (Philippine College Grading):**
  - `97.00 - 100.00` &rarr; **1.00** (Excellent)
  - `94.00 - 96.99` &rarr; **1.25** (Very Good)
  - `91.00 - 93.99` &rarr; **1.50** (Very Good)
  - `88.00 - 90.99` &rarr; **1.75** (Good)
  - `85.00 - 87.99` &rarr; **2.00** (Good)
  - `82.00 - 84.99` &rarr; **2.25** (Fair)
  - `79.00 - 81.99` &rarr; **2.50** (Fair)
  - `76.00 - 78.99` &rarr; **2.75** (Passing)
  - `75.00 - 75.99` &rarr; **3.00** (Passing)
  - `Below 75.00` &rarr; **5.00** (Failed)
- **Passing Status Badge:** Automatic visual indicators (**Passed** in green, **Failed** in red).
- **Executive Analytics:** Total Students, Passed Count, Failed Count, Average Class Grade, and Passing Rate (%).
- **Official Printable Grade Slip (`view.php`):** Clean print layout ready for printing or saving as PDF without browser buttons or navigation bars.

---

## 🗄️ Database Design (`database.sql`)

The database contains the `student_grades` table with **15 fields** (exceeding the 5+ fields requirement):

| Column | Data Type | Key / Constraint | Description |
|---|---|---|---|
| `id` | `INT(11)` | `PRIMARY KEY AUTO_INCREMENT` | Unique record record identifier |
| `student_id` | `VARCHAR(30)` | `NOT NULL` | Student institutional ID (e.g., `2024-00101`) |
| `first_name` | `VARCHAR(100)` | `NOT NULL` | Student first name |
| `last_name` | `VARCHAR(100)` | `NOT NULL` | Student last name |
| `gender` | `ENUM('Male','Female','Other')` | `DEFAULT 'Male'` | Gender |
| `course_year` | `VARCHAR(50)` | `NOT NULL` | Course and section (e.g., `BSIT 3-A`) |
| `subject_code` | `VARCHAR(100)` | `NOT NULL` | Subject code & name (e.g., `IT301 - Web Systems`) |
| `prelim` | `DECIMAL(5,2)` | `DEFAULT 0.00` | Prelim grade score (0.00 - 100.00) |
| `midterm` | `DECIMAL(5,2)` | `DEFAULT 0.00` | Midterm grade score (0.00 - 100.00) |
| `prefinal` | `DECIMAL(5,2)` | `DEFAULT 0.00` | Pre-Final grade score (0.00 - 100.00) |
| `final` | `DECIMAL(5,2)` | `DEFAULT 0.00` | Final Exam & Project score (0.00 - 100.00) |
| `final_grade` | `DECIMAL(5,2)` | `DEFAULT 0.00` | Computed weighted final grade percentage |
| `equivalent_grade` | `DECIMAL(3,2)` | `DEFAULT 5.00` | Computed equivalent GPA (1.00 to 5.00) |
| `remarks` | `ENUM('Passed','Failed')` | `DEFAULT 'Passed'` | Academic status |
| `created_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Date and time record was created |
| `updated_at` | `TIMESTAMP` | `ON UPDATE CURRENT_TIMESTAMP` | Date and time record was last modified |

---

## 🚀 Local Development Setup (XAMPP)

Follow these steps to run the application on your computer:

1. **Copy Project Folder to XAMPP:**
   - Copy this `StudentGradingSystem` folder into:
     ```
     C:\xampp\htdocs\StudentGradingSystem
     ```
2. **Start Services in XAMPP:**
   - Open **XAMPP Control Panel**.
   - Click **Start** for **Apache**.
   - Click **Start** for **MySQL**.
3. **Import Database:**
   - Open your browser and navigate to `http://localhost/phpmyadmin`.
   - Click **New** on the left sidebar to create a database named `student_grading_db` (or simply click **Import**).
   - Click the **Import** tab.
   - Click **Choose File** and select `database.sql` from the project folder.
   - Click **Go** (or **Import**) at the bottom.
4. **Access the Application:**
   - Open your browser and visit:
     ```
     http://localhost/StudentGradingSystem/
     ```

---

## 🌐 InfinityFree Deployment Instructions

Follow these steps to publish your project live on the internet:

1. **Create an InfinityFree Account:**
   - Sign up at [https://www.infinityfree.net](https://www.infinityfree.net).
2. **Create a Free Hosting Account:**
   - In the InfinityFree **Client Area**, click **Create Account**.
   - Choose a subdomain (e.g., `yourfullname.epizy.com` or `yourname.infinityfreeapp.com`).
   - Complete account creation and wait 1–2 minutes for setup.
3. **Create MySQL Database on InfinityFree:**
   - In your InfinityFree account details, click **Control Panel** (vPanel).
   - Click **MySQL Databases**.
   - Under **Create New Database**, type `student_grading_db` and click **Create Database**.
   - Note down the database details displayed on that page:
     - **MySQL Hostname:** e.g., `sql300.epizy.com`
     - **MySQL Username:** e.g., `epiz_12345678`
     - **MySQL Password:** (Found in your InfinityFree Client Area under Account Details)
     - **MySQL Database Name:** e.g., `epiz_12345678_student_grading_db`
4. **Update `db_connect.php`:**
   - Open `db_connect.php` in any code editor.
   - Verified credentials in `db_connect.php`:
     ```php
     $db_host = 'sql212.infinityfree.com';
     $db_user = 'if0_43472398';
     $db_pass = 'KRSrrKkpTu6w';
     $db_name = 'if0_43472398_db_gradingsystem';
     ```
5. **Import Database to InfinityFree phpMyAdmin:**
   - In your InfinityFree Control Panel, click **phpMyAdmin**.
   - Select your database on the left.
   - Click the **Import** tab.
   - Choose your `database.sql` file and click **Go**.
6. **Upload Files via Online File Manager:**
   - In the InfinityFree Control Panel, click **Online File Manager** (Monsta FTP).
   - Open the **`htdocs/`** directory.
   - Upload all files (`index.php`, `create.php`, `edit.php`, `delete.php`, `view.php`, `header.php`, `footer.php`, `db_connect.php`, `database.sql`, and the `assets/` folder).
   *(Make sure files are placed directly inside `htdocs/`, not nested in an extra subfolder!)*
7. **Verify Your Live Application:**
   - Visit your custom InfinityFree domain (e.g., `https://yourfullname.epizy.com`).
   - Test adding, editing, searching, viewing, and deleting a record.

---

## 📦 GitHub Repository Submission Guide

1. Create a free account at [https://github.com](https://github.com).
2. Click **New Repository**, name it `StudentGradingSystem`, choose **Public**, and click **Create repository**.
3. **Upload Files:**
   - Click **uploading an existing file** on the repository page.
   - Drag and drop all files and folders from `StudentGradingSystem` (including `database.sql` and `README.md`).
   - Click **Commit changes**.
4. Copy your GitHub repository URL (e.g., `https://github.com/yourusername/StudentGradingSystem`).

---

## 📧 Final Submission Checklist

Send an email to your instructor with the following details:

- **Recipient:** `kenthjoshuaespina@isufst.edu.ph`
- **Subject:** `Final Project Submission: Student Grading System - [Your Full Name]`
- **Body:**
  - **Student Name:** [Your Full Name]
  - **Course & Section:** [e.g., BSIT 3-A]
  - **Assigned Project Title:** Student Grading System
  - **Live InfinityFree URL:** `https://yourfullname.epizy.com`
  - **GitHub Repository URL:** `https://github.com/yourusername/StudentGradingSystem`
  - **Database SQL File:** Included in GitHub repository as `database.sql`

---

## 🎤 5–7 Minute Presentation Guide

When presenting your project to your instructor, use this recommended structure:

| Time | Slide / Demonstration Topic | Key Points to Say |
|---|---|---|
| **0:00 - 1:00** | **Introduction & Objective** | *"Good day, Sir! Today I am presenting my Web Systems Technologies final project: the Student Grading System. The main objective of this project is to implement a responsive full-stack CRUD application using PHP, MySQL, and Bootstrap 5 to automate grade computation and student evaluation."* |
| **1:00 - 2:00** | **Database Structure** | Explain the `student_grades` table (15 fields). Highlight the primary key, unique constraint preventing duplicate student subjects, proper data types, and timestamps. |
| **2:00 - 3:30** | **Live CRUD Demonstration** | - **Create (Insert):** Fill out student info and show the live JavaScript grade calculation as you type period grades.<br>- **Read (Select):** Show the dashboard, search by student name/ID, filter by section, and review summary statistics.<br>- **Update (Edit):** Edit a student's final grade and demonstrate automatic recalculation of remarks from Failed to Passed.<br>- **Delete (Remove):** Show the delete confirmation modal preventing accidental data loss. |
| **3:30 - 4:30** | **Printable Grade Slip Feature** | Open a student's grade slip (`view.php`) and click **Print Grade Slip** to show the professional paper layout. |
| **4:30 - 5:30** | **InfinityFree Hosting Deployment** | Explain how `db_connect.php` connects to InfinityFree MySQL, show your live `epizy.com` website in the browser, and demonstrate that CRUD works live in production. |
| **5:30 - 6:00** | **Conclusion & Q&A** | Thank the instructor and open the floor for any questions or code inspection. |
