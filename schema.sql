-- BEEN Recruitment Platform — MySQL schema
-- Run this once against your MySQL database, e.g.:
--   mysql -u root -p EmployeeRecruitment < schema.sql
 
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
CREATE TABLE IF NOT EXISTS employers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(150),
    email VARCHAR(150),
    password VARCHAR(150),
    status VARCHAR(20) DEFAULT 'Pending',
    phone VARCHAR(20),
    company_name VARCHAR(150),
    company_type VARCHAR(100),
    designation VARCHAR(100),
    location VARCHAR(150),
    website VARCHAR(200),
    openings VARCHAR(100),
    requirements TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
CREATE TABLE IF NOT EXISTS job_seekers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role_applied VARCHAR(50),
    experience VARCHAR(50),
    preferred_location VARCHAR(100),
    available_from DATE NULL,
    resume VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    email VARCHAR(150),
    amount DECIMAL(10,2),
    razorpay_order_id VARCHAR(100),
    razorpay_payment_id VARCHAR(100),
    status VARCHAR(20) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(150),
    project_type VARCHAR(100),
    budget_range VARCHAR(50),
    project_location VARCHAR(150),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
-- Default admin login (admin / admin123) — change this in production
INSERT INTO admin (username, password)
SELECT 'admin', 'admin123'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM admin WHERE username = 'admin');