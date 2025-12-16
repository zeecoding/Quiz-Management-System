# Online Quiz Management System

A robust, scalable, and secure web-based application designed to facilitate the creation, administration, and grading of quizzes. This system automates the examination process, providing a seamless experience for both instructors (Admins) and students.

## 📌 Project Information

- **Course:** Web Technologies
- **Developers:**
  - Muhammad Zubair (SP23-BSE-006)
  - Abdul Hafeez (SP23-BSE-047)

---

## 🚀 Key Features

### 👨‍🏫 Admin (Teacher) Features
* **Dynamic Role Management** - Scalable user role system (Admin, Student, etc.) fetched dynamically from the database.
* **Dashboard Analytics** - Real-time overview of total students, active quizzes, and total attempts using optimized database queries.
* **Advanced Quiz Creation**
    * Set custom time limits (in minutes).
    * Define total marks and passing criteria.
    * **Publish/Draft Mode:** Control when students can see the quiz.
* **Flexible Question Bank**
    * **Single Choice:** Standard radio button questions.
    * **Multiple Choice:** Checkbox questions allowing multiple correct answers (e.g., "Select all that apply").
    * **Rich Editing:** Update question text, options, and types on the fly.
* **Result Management** - View detailed leaderboards, individual student scores, and pass/fail status.

### 👨‍🎓 Student Features
* **Secure Portal** - Individual login with session management.
* **Interactive Quiz Engine**
    * **Timer:** Countdown timer that auto-submits when time expires.
    * **Anti-Cheat System:** Detects tab switching or window minimization and warns/auto-submits the quiz.
    * **User Interface:** Clean, inline options with hover effects for better usability.
* **Instant Grading** - Automated weighted scoring algorithm handles both single and multiple-choice grading instantly.
* **Attempt History** - Comprehensive log of past quizzes with scores, dates, and status.
* **One-Time Access** - Strict prevention of retaking completed quizzes.

---

## 🛠️ Technical Stack

- **Frontend:** HTML5, CSS3, Bootstrap 5 (Responsive Design), JavaScript (Vanilla)
- **Backend:** Core PHP (Object Oriented & Procedural)
- **Database:** MySQL (Normalized with Indexes for Scalability)
- **Server:** Apache (XAMPP/WAMP)

---

## ⚙️ Installation & Setup

### 1. Environment Setup
1.  Install **XAMPP** or **WAMP**.
2.  Navigate to your `htdocs` folder (e.g., `C:\xampp\htdocs`).
3.  Create a folder named `quiz_project`.
4.  Extract the source code into this folder.

### 2. Database Configuration
1.  Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2.  Create a new database named `quiz_system`.
3.  Click the **SQL** tab and run the following **Updated Schema** to set up the tables with scalability features:

```sql
-- 1. Create Roles Table (Scalability)
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(50) NOT NULL
);

-- Insert Default Roles
INSERT INTO roles (role_name, display_name) VALUES 
('admin', 'Teacher'),
('student', 'Student');

-- 2. Create Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- 3. Create Quizzes Table
CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    time_limit INT NOT NULL,
    total_marks INT DEFAULT 10,
    passing_marks INT DEFAULT 5,
    is_published TINYINT DEFAULT 0,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Add Index for Dashboard Performance
CREATE INDEX idx_quizzes_created_by ON quizzes(created_by);

-- 4. Create Questions Table
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option VARCHAR(255) NOT NULL, -- Stores "A" or "A,C"
    type ENUM('single', 'multiple') DEFAULT 'single',
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

-- Add Index for Faster Quiz Loading
CREATE INDEX idx_questions_quiz_id ON questions(quiz_id);

-- 5. Create Results Table
CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    quiz_id INT,
    score FLOAT,
    total_questions INT,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

-- Add Index for "Already Attempted" Check
CREATE INDEX idx_results_user_quiz ON results(user_id, quiz_id);
