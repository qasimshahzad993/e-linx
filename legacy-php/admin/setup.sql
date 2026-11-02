-- Run in phpMyAdmin > elinx_db > SQL. Only creates tables that don't exist yet.
CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);
CREATE TABLE IF NOT EXISTS leads (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120), email VARCHAR(150), phone VARCHAR(40),
  service VARCHAR(150), message TEXT,
  status ENUM('new','contacted','closed') NOT NULL DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL, description TEXT,
  icon VARCHAR(80) DEFAULT 'bi bi-gear', is_active TINYINT(1) DEFAULT 1
);
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL, price DECIMAL(10,2) DEFAULT 0,
  description TEXT, is_active TINYINT(1) DEFAULT 1
);
CREATE TABLE IF NOT EXISTS portfolio (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL, category VARCHAR(80), description TEXT,
  link VARCHAR(255), icon VARCHAR(80) DEFAULT 'bi bi-window', is_active TINYINT(1) DEFAULT 1
);
