<?php
/**
 * Setup Script - Hostel Management System
 * Run this file to initialize the database and create admin user
 */

// Display all errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/config.php';

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Create rooms table FIRST (before students because of foreign key)
$sql = "CREATE TABLE IF NOT EXISTS rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_number VARCHAR(20) UNIQUE NOT NULL,
    room_type ENUM('single', 'double', 'triple', 'dormitory') NOT NULL,
    total_beds INT NOT NULL,
    occupied_beds INT DEFAULT 0,
    available_beds INT DEFAULT 0,
    price_per_bed DECIMAL(10,2) NOT NULL,
    description TEXT,
    status ENUM('available', 'full', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Create students table
$sql = "CREATE TABLE IF NOT EXISTS students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE,
    student_name VARCHAR(100) NOT NULL,
    father_name VARCHAR(100) NOT NULL,
    cnic VARCHAR(20) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    address TEXT,
    room_id INT,
    bed_number INT,
    entry_date DATE NOT NULL,
    monthly_fee DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('active', 'inactive', 'left') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
)";
$conn->query($sql);

// Create fees table
$sql = "CREATE TABLE IF NOT EXISTS fees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    month VARCHAR(20) NOT NULL,
    year INT NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('cash', 'account') NOT NULL,
    transaction_id VARCHAR(100),
    payment_proof VARCHAR(255),
    status ENUM('paid', 'pending', 'rejected') DEFAULT 'paid',
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
)";
$conn->query($sql);

// Create mess_payments table
$sql = "CREATE TABLE IF NOT EXISTS mess_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    month VARCHAR(20) NOT NULL,
    year INT NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('cash', 'account') NOT NULL,
    transaction_id VARCHAR(100),
    payment_proof VARCHAR(255),
    status ENUM('paid', 'pending', 'rejected') DEFAULT 'paid',
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
)";
$conn->query($sql);

// Create notifications table
$sql = "CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('general', 'fee', 'mess', 'complaint', 'important') DEFAULT 'general',
    is_read ENUM('yes', 'no') DEFAULT 'no',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
$conn->query($sql);

// Create complaints table
$sql = "CREATE TABLE IF NOT EXISTS complaints (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    subject VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pending', 'in_progress', 'resolved') DEFAULT 'pending',
    reply TEXT,
    replied_by INT,
    replied_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (replied_by) REFERENCES users(id) ON DELETE SET NULL
)";
$conn->query($sql);

// Create contact_messages table
$sql = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Check if admin user already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $admin_username, $admin_email);
$admin_username = 'admin';
$admin_email = 'admin@hostel.com';
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Create admin user with bcrypt hashed password
    $password = 'admin123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
    $stmt->bind_param("sss", $admin_username, $admin_email, $hashed_password);
    $stmt->execute();
    
    // Create student user
    $student_username = 'student1';
    $student_email = 'student@hostel.com';
    $student_password = 'student123';
    $student_hashed = password_hash($student_password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'student')");
    $stmt->bind_param("sss", $student_username, $student_email, $student_hashed);
    $stmt->execute();
    $student_user_id = $conn->insert_id;
    
    // Create student record
    $student_name = 'John Doe';
    $father_name = 'James Doe';
    $cnic = '12345-6789012-3';
    $phone = '03001234567';
    $entry_date = date('Y-m-d');
    $monthly_fee = 5000.00;
    
    $stmt = $conn->prepare("INSERT INTO students (user_id, student_name, father_name, cnic, phone, email, entry_date, monthly_fee, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')");
    $stmt->bind_param("isssssid", $student_user_id, $student_name, $father_name, $cnic, $phone, $student_email, $entry_date, $monthly_fee);
    $stmt->execute();
    
    // Create sample rooms
    $conn->query("INSERT INTO rooms (room_number, room_type, total_beds, occupied_beds, price_per_bed, status) VALUES ('101', 'single', 1, 0, 8000.00, 'available')");
    $conn->query("INSERT INTO rooms (room_number, room_type, total_beds, occupied_beds, price_per_bed, status) VALUES ('102', 'double', 2, 0, 6000.00, 'available')");
    $conn->query("INSERT INTO rooms (room_number, room_type, total_beds, occupied_beds, price_per_bed, status) VALUES ('103', 'triple', 3, 0, 4500.00, 'available')");
    
    $message = "Setup completed successfully! Admin and student users created.";
} else {
    // Update existing admin user with correct password
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ?, role = 'admin' WHERE username = 'admin'");
    $stmt->bind_param("s", $hashed_password);
    $stmt->execute();
    
    // Check if student exists, if not create
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = 'student1'");
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $student_username = 'student1';
        $student_email = 'student@hostel.com';
        $student_password = 'student123';
        $student_hashed = password_hash($student_password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'student')");
        $stmt->bind_param("sss", $student_username, $student_email, $student_hashed);
        $stmt->execute();
    } else {
        // Update existing student password
        $student_password = 'student123';
        $student_hashed = password_hash($student_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ?, role = 'student' WHERE username = 'student1'");
        $stmt->bind_param("s", $student_hashed);
        $stmt->execute();
    }
    
    $message = "Setup completed successfully! Admin and student users updated.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0"><i class="fas fa-cog"></i> Setup Complete</h4>
                    </div>
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-success"><?php echo $message; ?></h5>
                        <hr>
                        <div class="text-start bg-light p-3 rounded mb-3">
                            <h6>Admin Credentials:</h6>
                            <p class="mb-1"><strong>Username:</strong> admin</p>
                            <p class="mb-0"><strong>Password:</strong> admin123</p>
                        </div>
                        <div class="text-start bg-light p-3 rounded">
                            <h6>Student Credentials:</h6>
                            <p class="mb-1"><strong>Username:</strong> student1</p>
                            <p class="mb-0"><strong>Password:</strong> student123</p>
                        </div>
                        <div class="mt-4">
                            <a href="login.php" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> Go to Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

