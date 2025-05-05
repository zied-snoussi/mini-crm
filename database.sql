-- Create database
CREATE DATABASE
IF NOT EXISTS mini_crm;
USE mini_crm;

-- Create users table
CREATE TABLE
IF NOT EXISTS users
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR
(50) NOT NULL UNIQUE,
    email VARCHAR
(100) NOT NULL UNIQUE,
    password VARCHAR
(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create prospects table
CREATE TABLE
IF NOT EXISTS prospects
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR
(100) NOT NULL,
    company VARCHAR
(100),
    phone VARCHAR
(20),
    email VARCHAR
(100) NOT NULL,
    status ENUM
('new', 'contacted', 'in negotiation', 'won', 'lost') DEFAULT 'new',
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY
(user_id) REFERENCES users
(id) ON
DELETE
SET NULL
);

-- Create notes table
CREATE TABLE
IF NOT EXISTS notes
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    prospect_id INT NOT NULL,
    user_id INT,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY
(prospect_id) REFERENCES prospects
(id) ON
DELETE CASCADE,
    FOREIGN KEY (user_id)
REFERENCES users
(id) ON
DELETE
SET NULL
);

-- Create documents table
CREATE TABLE
IF NOT EXISTS documents
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    prospect_id INT NOT NULL,
    user_id INT,
    filename VARCHAR
(255) NOT NULL,
    original_filename VARCHAR
(255) NOT NULL,
    file_type VARCHAR
(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY
(prospect_id) REFERENCES prospects
(id) ON
DELETE CASCADE,
    FOREIGN KEY (user_id)
REFERENCES users
(id) ON
DELETE
SET NULL
);

-- Insert default admin user (password: admin123)
INSERT INTO users
    (username, email, password)
VALUES
    ('admin', 'admin@example.com', '$2y$10$Nzk9HQIWt11PwqS/loYl7O0LeqetMktzK8vGgyAIhN67eV6OrW86u');

-- Insert sample prospects
INSERT INTO prospects
    (name, company, phone, email, status, user_id)
VALUES
    ('John Smith', 'ABC Corp', '555-123-4567', 'john@abccorp.com', 'new', 1),
    ('Jane Doe', 'XYZ Inc', '555-987-6543', 'jane@xyzinc.com', 'contacted', 1),
    ('Michael Johnson', 'Acme Ltd', '555-456-7890', 'michael@acme.com', 'in negotiation', 1),
    ('Sarah Williams', 'Global Tech', '555-789-0123', 'sarah@globaltech.com', 'won', 1),
    ('Robert Brown', 'Innovative Solutions', '555-321-6547', 'robert@innovative.com', 'lost', 1);
