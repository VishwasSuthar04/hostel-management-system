<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

// Handle delete student
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get student info first
    $stmt = $conn->prepare("SELECT user_id FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    
    if ($student) {
        // Delete student
        $delete = $conn->prepare("DELETE FROM students WHERE id = ?");
        $delete->bind_param("i", $id);
        
        if ($delete->execute()) {
            // Delete associated user
            if ($student['user_id']) {
                $conn->query("DELETE FROM users WHERE id = " . $student['user_id']);
            }
            setMessage("Student deleted successfully!", "success");
        }
    }
    redirect('students.php');
}

// Get all students
$students = getAllStudents();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - Hostel Management System</title>
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
        
        .table-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
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
                        <h4><i class="fas fa-users"></i> Students Management</h4>
                        <a href="add_student.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Student
                        </a>
                    </div>
                    
                    <!-- Display Message -->
                    <?php displayMessage(); ?>
                    
                    <!-- Students Table -->
                    <div class="table-card">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Father Name</th>
                                        <th>CNIC</th>
                                        <th>Phone</th>
                                        <th>Room</th>
                                        <th>Entry Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($students) > 0): ?>
                                        <?php foreach ($students as $student): ?>
                                            <tr>
                                                <td><?php echo $student['id']; ?></td>
                                                <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                                                <td><?php echo htmlspecialchars($student['father_name']); ?></td>
                                                <td><?php echo htmlspecialchars($student['cnic']); ?></td>
                                                <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                                <td>
                                                    <?php if ($student['room_number']): ?>
                                                        <span class="badge bg-info"><?php echo $student['room_number']; ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Not Assigned</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('d M Y', strtotime($student['entry_date'])); ?></td>
                                                <td>
                                                    <?php
                                                    $statusClass = [
                                                        'active' => 'bg-success',
                                                        'inactive' => 'bg-secondary',
                                                        'left' => 'bg-danger'
                                                    ];
                                                    ?>
                                                    <span class="badge <?php echo $statusClass[$student['status']] ?? 'bg-secondary'; ?>">
                                                        <?php echo ucfirst($student['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="students.php?delete=<?php echo $student['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this student?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <p class="text-muted mb-0">No students found. <a href="add_student.php">Add your first student</a></p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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
