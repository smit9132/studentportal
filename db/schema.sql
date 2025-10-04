-- SQL schema for StudentPortal
-- Create database and tables

CREATE DATABASE IF NOT EXISTS studentportal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studentportal;

-- users table
CREATE TABLE IF NOT EXISTS users (
  user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_users_role (role),
  INDEX idx_users_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- students table
CREATE TABLE IF NOT EXISTS students (
  student_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(255) NOT NULL,
  course VARCHAR(150) DEFAULT NULL,
  year VARCHAR(50) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  INDEX idx_students_user (user_id),
  INDEX idx_students_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- events table
CREATE TABLE IF NOT EXISTS events (
  event_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  date DATE NOT NULL,
  location VARCHAR(255) DEFAULT NULL,
  status ENUM('upcoming','completed','cancelled') NOT NULL DEFAULT 'upcoming',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_events_date (date),
  INDEX idx_events_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert a default admin account
-- Default login (for development/testing):
--   username: admin
-- For security, DO NOT include plaintext passwords in the repository.
-- Use `db/setup.php` to create a secure admin account (recommended), or generate a bcrypt hash using the PHP CLI:
--   php -r "echo password_hash('your_password_here', PASSWORD_BCRYPT);"
-- The password stored below is a bcrypt hash. In production, rotate or remove seeded accounts and avoid committing secrets.
INSERT INTO users (username, email, password, role, status)
VALUES ('admin', 'admin@example.com', '$2y$10$u8K1s9dFh3pQmL2zVxY4Ou6q7w8e9r0tABCDefghijkLMNOPQRstu', 'admin', 'active')
ON DUPLICATE KEY UPDATE username = VALUES(username);