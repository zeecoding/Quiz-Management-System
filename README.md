# 📚 Online Quiz Management System

A robust, scalable, and secure web-based application designed to facilitate the creation, administration, and grading of quizzes. This system automates the examination process, providing a seamless experience for both instructors and students.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=flat&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat&logo=javascript&logoColor=black)

---

## 📌 Project Information

**Course:** Web Technologies  
**Developers:**
- Muhammad Zubair (SP23-BSE-006)
- Abdul Hafeez (SP23-BSE-047)

---

## ✨ Key Features

### 👨‍🏫 Admin (Teacher) Features

- **Dynamic Role Management** - Scalable user role system fetched dynamically from the database
- **Dashboard Analytics** - Real-time overview with optimized queries showing:
  - Total students enrolled
  - Active quizzes count
  - Total quiz attempts
- **Advanced Quiz Creation**
  - Custom time limits (in minutes)
  - Configurable total marks and passing criteria
  - **Publish/Draft Mode** - Control quiz visibility to students
- **Flexible Question Bank**
  - **Single Choice** - Standard radio button questions
  - **Multiple Choice** - Checkbox questions with multiple correct answers
  - **Rich Editing** - Update questions, options, and types dynamically
- **Result Management** - Detailed leaderboards with individual student scores and pass/fail status

### 👨‍🎓 Student Features

- **Secure Portal** - Individual login with session management
- **Interactive Quiz Engine**
  - **Countdown Timer** - Auto-submits quiz when time expires
  - **Anti-Cheat System** - Detects tab switching/window minimization with warnings
  - **Clean UI** - Inline options with hover effects for better usability
- **Instant Grading** - Automated weighted scoring for both question types
- **Attempt History** - Comprehensive log with scores, dates, and status
- **One-Time Access** - Prevents retaking completed quizzes

---

## 🛠️ Technical Stack

| Component | Technology |
|-----------|------------|
| **Frontend** | HTML5, CSS3, Bootstrap 5, Vanilla JavaScript |
| **Backend** | Core PHP (OOP & Procedural) |
| **Database** | MySQL (Normalized with Indexes) |
| **Server** | Apache (XAMPP/WAMP) |

---

## 📋 Prerequisites

Before you begin, ensure you have the following installed:
- **XAMPP** or **WAMP** (includes Apache, PHP, and MySQL)
- Web browser (Chrome, Firefox, Edge, etc.)
- Text editor (VS Code, Sublime Text, etc.) - optional for modifications

---

## ⚙️ Installation & Setup

### Step 1: Environment Setup

1. Install [XAMPP](https://www.apachefriends.org/) or [WAMP](https://www.wampserver.com/)
2. Navigate to your server's root directory:
   - **XAMPP**: `C:\xampp\htdocs`
   - **WAMP**: `C:\wamp64\www`
3. Create a new folder named `quiz_project`
4. Extract/copy the project files into this folder

### Step 2: Database Configuration

1. Start **Apache** and **MySQL** from your XAMPP/WAMP control panel
2. Open **phpMyAdmin** by visiting: `http://localhost/phpmyadmin`
3. Click **New** to create a database named `quiz_system`
4. Select the `quiz_system` database and click the **SQL** tab
5. Copy and paste the following schema and click **Go**:

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
```

### Step 3: Configure Database Connection

1. Navigate to `config/db.php` in your project folder
2. Verify the database credentials match your setup:

```php
<?php
$servername = "localhost";
$username = "root";
$password = "";  // Leave blank for default XAMPP/WAMP
$dbname = "quiz_system";
?>
```

### Step 4: Launch the Application

1. Ensure Apache and MySQL are running
2. Open your web browser
3. Visit: `http://localhost/quiz_project/`
4. You should see the login/registration page

---

## 📖 Usage Guide

### For Admins (Teachers)

1. **Register an Account**
   - Click "Register" on the login page
   - Fill in your details and select **"Teacher"** as your role
   - Submit the form

2. **Create a Quiz**
   - Navigate to "Create New Quiz"
   - Enter quiz title, time limit (minutes), total marks, and passing marks
   - Click "Create Quiz"

3. **Add Questions**
   - Select **"Single Choice"** for standard multiple-choice questions
   - Select **"Multiple Choice"** for questions with multiple correct answers
   - Enter question text and four options (A, B, C, D)
   - Select the correct answer(s)
   - Click "Add Question"

4. **Publish Quiz**
   - Return to your dashboard
   - Click the eye icon (👁️) next to a quiz to publish it
   - Published quizzes become visible to students

5. **View Results**
   - Click "View Results" on any quiz
   - See leaderboard with student names, scores, and pass/fail status

### For Students

1. **Register an Account**
   - Click "Register" on the login page
   - Fill in your details and select **"Student"** as your role
   - Submit the form

2. **Take a Quiz**
   - View all published quizzes on your dashboard
   - Click "Start Quiz" on any available quiz
   - **Important:** Do not switch tabs or minimize the window during the quiz
   - Answer all questions within the time limit
   - Click "Submit Quiz" or wait for auto-submission

3. **View Results**
   - Your score appears immediately after submission
   - Check "My History" to see all past attempts with dates and scores

4. **Anti-Cheat Notice**
   - The system detects tab switching and window changes
   - Multiple violations will result in automatic submission
   - Stay focused on the quiz window throughout

---

## 🔒 Security Features

| Feature | Implementation |
|---------|----------------|
| **Password Security** | Bcrypt hashing via `password_hash()` |
| **SQL Injection Protection** | Prepared statements with parameter binding |
| **Session Management** | Role-based access control with session validation |
| **XSS Prevention** | Output escaping using `htmlspecialchars()` |
| **CSRF Protection** | Session-based validation for critical operations |

---

## 📁 Project Structure

```
quiz_project/
├── config/
│   └── db.php              # Database connection
├── admin/
│   ├── dashboard.php       # Teacher dashboard
│   ├── create_quiz.php     # Quiz creation page
│   └── view_results.php    # Results management
├── student/
│   ├── dashboard.php       # Student dashboard
│   ├── take_quiz.php       # Quiz interface
│   └── history.php         # Attempt history
├── assets/
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript files
│   └── images/             # Images and icons
├── index.php               # Login page
├── register.php            # Registration page
└── logout.php              # Session termination
```

---

## 🐛 Troubleshooting

### Common Issues

**Issue:** Database connection error  
**Solution:** Verify credentials in `config/db.php` and ensure MySQL is running

**Issue:** "Table doesn't exist" error  
**Solution:** Re-run the SQL schema in phpMyAdmin

**Issue:** Cannot access admin features  
**Solution:** Ensure you registered with "Teacher" role selected

**Issue:** Quiz timer not working  
**Solution:** Enable JavaScript in your browser settings

**Issue:** Blank page after login  
**Solution:** Check PHP error logs in `C:\xampp\apache\logs\error.log`

---

## 🚀 Future Enhancements

- [ ] Question bank import/export (CSV/JSON)
- [ ] Image support in questions
- [ ] Randomized question order
- [ ] Email notifications for quiz results
- [ ] Advanced analytics dashboard
- [ ] Mobile app integration
- [ ] Multi-language support

---

## 📄 License

This project is developed as part of academic coursework and is free to use for educational purposes.

---

## 🤝 Contributing

This is an academic project, but feedback and suggestions are welcome! Feel free to:
- Report bugs
- Suggest new features
- Submit improvements

---

## 📧 Contact

For questions or support:
- **Muhammad Zubair** - SP23-BSE-006
- **Abdul Hafeez** - SP23-BSE-047

---

## 🙏 Acknowledgments

- Course instructors for guidance and support
- Bootstrap team for the responsive framework
- PHP and MySQL communities for extensive documentation

---

**Made with ❤️ for Web Technologies Course**
