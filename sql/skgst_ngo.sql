-- CREATE DATABASE IF NOT EXISTS skgst_ngo;
-- USE skgst_ngo;

-- Drop and recreate contact_form table
DROP TABLE IF EXISTS contact_form;
CREATE TABLE contact_form (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    mobile VARCHAR(15) NOT NULL,
    message TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Drop and recreate volunteer_form table
DROP TABLE IF EXISTS volunteer_form;
CREATE TABLE volunteer_form (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    interest VARCHAR(100),
    message TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(10) NOT NULL,
    address VARCHAR(255) NOT NULL,
    gender ENUM('Male','Female','Other') NOT NULL,
    dob DATE NOT NULL,
    occupation VARCHAR(100) NOT NULL,
    qualification VARCHAR(100) NOT NULL,
    password VARCHAR(50) NOT NULL,
    role VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_email_role (email, role)
);

DROP TABLE IF EXISTS donations;
CREATE TABLE donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    address TEXT,
    pan_number VARCHAR(20),
    amount DECIMAL(10,2) NOT NULL,
    payment_id VARCHAR(100) NOT NULL,
    status VARCHAR(50) DEFAULT 'Success',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);






