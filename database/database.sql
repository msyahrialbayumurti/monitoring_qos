CREATE TABLE IF NOT EXISTS users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('admin','user') NOT NULL DEFAULT 'user',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS surveys (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 title VARCHAR(200) NOT NULL,
 slug VARCHAR(220) NOT NULL UNIQUE,
 description TEXT,
 status ENUM('draft','published') NOT NULL DEFAULT 'published',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS questions (
 id INT AUTO_INCREMENT PRIMARY KEY,
 survey_id INT NOT NULL,
 question_text TEXT NOT NULL,
 question_type ENUM('likert','text','number','choice') NOT NULL DEFAULT 'likert',
 variable_code VARCHAR(10) NULL,
 question_order INT NOT NULL DEFAULT 1,
 required TINYINT(1) NOT NULL DEFAULT 1,
 FOREIGN KEY (survey_id) REFERENCES surveys(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS response_submissions (
 id INT AUTO_INCREMENT PRIMARY KEY,
 survey_id INT NOT NULL,
 respondent_name VARCHAR(100) NOT NULL,
 gender ENUM('Laki-Laki','Perempuan','Memilih tidak menjawab') NOT NULL,
 respondent_status ENUM('Mahasiswa','Sudah Bekerja','Lainnya') NOT NULL,
 age TINYINT UNSIGNED NOT NULL,
 submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (survey_id) REFERENCES surveys(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS answers (
 id INT AUTO_INCREMENT PRIMARY KEY,
 submission_id INT NOT NULL,
 survey_id INT NOT NULL,
 question_id INT NOT NULL,
 answer TEXT NOT NULL,
 FOREIGN KEY (submission_id) REFERENCES response_submissions(id) ON DELETE CASCADE,
 FOREIGN KEY (survey_id) REFERENCES surveys(id) ON DELETE CASCADE,
 FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
 UNIQUE KEY unique_submission_question (submission_id, question_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contact_messages (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(150) NOT NULL,
 message TEXT NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Survei utama akan dibuat otomatis ketika membuka index.php pertama kali.
