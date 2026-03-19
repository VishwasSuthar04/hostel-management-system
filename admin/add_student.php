<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$rooms = getAllRooms();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = sanitize($_POST['student_name']);
    $father_name = sanitize($_POST['father_name']);
    $cnic = sanitize($_POST['cnic']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $address = sanitize($_POST['address']);
    $room_id = !empty($_POST['room_id']) ? (int)$_POST['room_id'] : null;
    $bed_number = !empty($_POST['bed_number']) ? (int)$_POST['bed_number'] : null;
    $entry_date = sanitize($_POST['entry_date']);
    $monthly_fee = sanitize($_POST['monthly_fee']);
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    // Validation
    $errors = [];
    
    if (empty($student_name)) $errors[] = "Student name is required";
    if (empty($father_name)) $errors[] = "Father name is required";
    if (empty($cnic)) $errors[] = "CNIC is required";
    if (empty($phone)) $errors[] = "Phone is required";
    if (empty($entry_date)) $errors[] = "Entry date is required";
    if (empty($username)) $errors[] = "Username is required";
    if (empty($password)) $errors[] = "Password is required";
    
    // Check if CNIC already exists
    $stmt = $conn->prepare("SELECT id FROM students WHERE cnic = ?");
    $stmt->bind_param("s", $cnic);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = "CNIC already registered";
    }
    
    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = "Username already exists";
    }
    
    if (empty($errors)) {
        // Insert user first
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'student')");
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        
        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            
            // Insert student
            $stmt = $conn->prepare("INSERT INTO students (user_id, student_name, father_name, cnic, phone, email, address, room_id, bed_number, entry_date, monthly_fee) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssssiis", $user_id, $student_name, $father_name, $cnic, $phone, $email, $address, $room_id, $bed_number, $entry_date, $monthly_fee);
            
            if ($stmt->execute()) {
                // Update room occupancy if room assigned
                if ($room_id) {
                    updateRoomOccupancy($room_id);
                }
                setMessage("Student added successfully!", "success");
                redirect('students.php');
            } else {
                $errors[] = "Failed to add student";
            }
        } else {
            $errors[] = "Failed to create user account";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - Hostel Management System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }
        
        body {
            background: #f5f7fa;
        }
        
        .sidebar {
            background: white;
            min-height: 100vh;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar .nav-link {
            color: #333;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 10px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .form-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-3 text-center border-bottom">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-building"></i> Royal Hostel
                    </h5>
                    <small class="text-muted">Admin Panel</small>
                </div>
                <ul class="nav flex-column mt-3">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="students.php" class="nav-link active">
                            <i class="fas fa-users"></i> Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="rooms.php" class="nav-link">
                            <i class="fas fa-bed"></i> Rooms
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="fees.php" class="nav-link">
                            <i class="fas fa-money-bill"></i> Fees
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="mess_payments.php" class="nav-link">
                            <i class="fas fa-utensils"></i> Mess Payments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="notifications.php" class="nav-link">
                            <i class="fas fa-bell"></i> Notifications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="complaints.php" class="nav-link">
                            <i class="fas fa-exclamation-circle"></i> Complaints
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="search.php" class="nav-link">
                            <i class="fas fa-search"></i> Search
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="reports.php" class="nav-link">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <a href="logout.php" class="nav-link text-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 p-0">
                <!-- Top Header -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user-circle"></i> <?php echo ucfirst($_SESSION['username']); ?>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                
                <!-- Page Content -->
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4><i class="fas fa-user-plus"></i> Add New Student</h4>
                        <a href="students.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                    
                    <!-- Display Errors -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Add Student Form -->
                    <div class="form-card">
                        <div class="card-body p-4">
                            <form method="POST" action="">
                                <h5 class="mb-4 border-bottom pb-2">Personal Information</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Student Name *</label>
                                        <input type="text" class="form-control" name="student_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Father Name *</label>
                                        <input type="text" class="form-control" name="father_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">CNIC *</label>
                                        <input type="text" class="form-control" name="cnic" placeholder="XXXXX-XXXXXXX-X" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone *</label>
                                        <input type="text" class="form-control" name="phone" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Address</label>
                                        <input type="text" class="form-control" name="address">
                                    </div>
                                </div>
                                
                                <h5 class="mb-4 mt-4 border-bottom pb-2">Hostel Information</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Room</label>
                                        <select class="form-select" name="room_id" id="room_id">
                                            <option value="">Select Room</option>
                                            <?php foreach ($rooms as $room): ?>
                                                <?php if ($room['available_beds'] > 0): ?>
                                                    <option value="<?php echo $room['id']; ?>">
                                                        <?php echo $room['room_number']; ?> (<?php echo $room['room_type']; ?> - <?php echo $room['available_beds']; ?> beds available)
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Bed Number</label>
                                        <input type="number" class="form-control" name="bed_number" min="1">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Entry Date *</label>
                                        <input type="date" class="form-control" name="entry_date" required value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Monthly Fee (₹)</label>
                                        <input type="number" class="form-control" name="monthly_fee" step="0.01" value="0">
                                    </div>
                                </div>
                                
                                <h5 class="mb-4 mt-4 border-bottom pb-2">Login Credentials</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Username *</label>
                                        <input type="text" class="form-control" name="username" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Password *</label>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> Save Student
                                    </button>
                                    <a href="students.php" class="btn btn-secondary btn-lg ms-2">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
