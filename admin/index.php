<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$stats = getDashboardStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hostel Management System</title>
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
        
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
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
                        <i class="fas fa-building"></i> Zardari Hostel
                    </h5>
                    <small class="text-muted">Admin Panel</small>
                </div>
                <ul class="nav flex-column mt-3">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link active">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="students.php" class="nav-link">
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
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Profile</a></li>
                                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                
                <!-- Page Content -->
                <div class="p-4">
                    <!-- Stats Cards -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-value"><?php echo $stats['total_students']; ?></div>
                                        <div class="stat-label">Total Students</div>
                                    </div>
                                    <div class="stat-icon" style="background: rgba(102, 126, 234, 0.1); color: #667eea;">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-value"><?php echo $stats['total_rooms']; ?></div>
                                        <div class="stat-label">Total Rooms</div>
                                    </div>
                                    <div class="stat-icon" style="background: rgba(118, 75, 162, 0.1); color: #764ba2;">
                                        <i class="fas fa-bed"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-value"><?php echo $stats['available_beds']; ?></div>
                                        <div class="stat-label">Available Beds</div>
                                    </div>
                                    <div class="stat-icon" style="background: rgba(40, 167, 69, 0.1); color: #28a745;">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-value">₹<?php echo number_format($stats['monthly_income'], 0); ?></div>
                                        <div class="stat-label">Monthly Income</div>
                                    </div>
                                    <div class="stat-icon" style="background: rgba(255, 193, 7, 0.1); color: #ffc107;">
                                        <i class="fas fa-rupee-sign"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Stats -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-value">₹<?php echo number_format($stats['total_income'], 0); ?></div>
                                        <div class="stat-label">Total Income (All Time)</div>
                                    </div>
                                    <div class="stat-icon" style="background: rgba(0, 123, 255, 0.1); color: #007bff;">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-value"><?php echo $stats['pending_complaints']; ?></div>
                                        <div class="stat-label">Pending Complaints</div>
                                    </div>
                                    <div class="stat-icon" style="background: rgba(220, 53, 69, 0.1); color: #dc3545;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <a href="add_student.php" class="btn btn-outline-primary w-100 p-3">
                                                <i class="fas fa-user-plus fa-2x mb-2"></i><br>Add Student
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="add_room.php" class="btn btn-outline-success w-100 p-3">
                                                <i class="fas fa-plus-square fa-2x mb-2"></i><br>Add Room
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="fees.php" class="btn btn-outline-warning w-100 p-3">
                                                <i class="fas fa-money-bill-wave fa-2x mb-2"></i><br>Manage Fees
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="notifications.php" class="btn btn-outline-info w-100 p-3">
                                                <i class="fas fa-bell fa-2x mb-2"></i><br>Send Notification
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
