-- schema.sql
-- CREATE DATABASE for lab
CREATE DATABASE IF NOT EXISTS webapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE webapp;

-- Main data table for CRUD demo
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  age TINYINT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Simple user table for access control demo
CREATE TABLE app_users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(30) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','user') DEFAULT 'user'
);

-- Password is 'admin123' hashed with password_hash()
-- Generate your own: echo password_hash('yourpass', PASSWORD_DEFAULT);
INSERT INTO app_users (username,password_hash,role) VALUES
('admin','$2y$10$8K1p/a0dURXAm7QiT3RzuN66b9z4L8pG9ZxKqJfF8YwZ8wG6yS','admin');
