<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireStudent();

$student = getStudentByUserId($_SESSION['user_id']);
$fees = getStudentFeeStatus($student['id']);
$notifications = getStudentNotifications($_SESSION['user_id']);
$unreadCount = getUnreadNotificationCount($_SESSION['user_id']);

// Get room details if assigned
$room = null;
if ($student['room_id']) {
    $room = getRoomById($student['room_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-color: #667eea; --secondary-color: #764ba2; }
        body { background: #f5f7fa; }
        .sidebar { background: white; min-height: 100vh; box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1); }
        .sidebar .nav-link { color: #333; padding: 12px 20px; border-radius: 8px; margin: 5px 10px; transition: all 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; }
        .stat-card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar p-0">
                <div class="p-3 text-center border-bottom">
                    <h5 class="mb-0 text-primary"><i class="fas fa-building"></i> Zardari Hostel</h5>
                    <small class="text-muted">Student Panel</small>
                </div>
                <ul class="nav flex-column mt-3">
                    <li class="nav-item"><a href="index.php" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="nav-item"><a href="profile.php" class="nav-link"><i class="fas fa-user"></i> My Profile</a></li>
                    <li class="nav-item"><a href="room.php" class="nav-link"><i class="fas fa-bed"></i> My Room</a></li>
                    <li class="nav-item"><a href="fees.php" class="nav-link"><i class="fas fa-money-bill"></i> My Fees</a></li>
                    <li class="nav-item"><a href="upload_payment.php" class="nav-link"><i class="fas fa-upload"></i> Upload Payment</a></li>
                    <li class="nav-item"><a href="notifications.php" class="nav-link">
                        <i class="fas fa-bell"></i> Notifications
                        <?php if ($unreadCount > 0): ?><span class="badge bg-danger ms-1"><?php echo $unreadCount; ?></span><?php endif; ?>
                    </a></li>
                    <li class="nav-item"><a href="complaint.php" class="nav-link"><i class="fas fa-exclamation-circle"></i> Complaints</a></li>
                    <li class="nav-item"><a href="change_password.php" class="nav-link"><i class="fas fa-lock"></i> Change Password</a></li>
                    <li class="nav-item mt-3"><a href="logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <div class="col-md-10 p-0">
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                    <div class="container-fluid">
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                        <i class="fas fa-user-circle"></i> <?php echo ucfirst($student['student_name']); ?>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                                        <li><a class="dropdown-item" href="change_password.php"><i class="fas fa-lock"></i> Change Password</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                
                <div class="p-4">
                    <h4 class="mb-4">Welcome, <?php echo htmlspecialchars($student['student_name']); ?>!</h4>
                    
                    <?php displayMessage(); ?>
                    
                    <!-- Profile Summary -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-user fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1">Student Name</h6>
                                        <h5 class="mb-0"><?php echo htmlspecialchars($student['student_name']); ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-bed fa-2x text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1">Room</h6>
                                        <h5 class="mb-0"><?php echo $room ? $room['room_number'] : 'Not Assigned'; ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-money-bill fa-2x text-warning"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1">Monthly Fee</h6>
                                        <h5 class="mb-0">RS<?php echo number_format($student['monthly_fee'], 0); ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <a href="fees.php" class="btn btn-outline-primary w-100 p-3">
                                                <i class="fas fa-money-bill-wave fa-2x mb-2"></i><br>View Fees
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="upload_payment.php" class="btn btn-outline-success w-100 p-3">
                                                <i class="fas fa-upload fa-2x mb-2"></i><br>Upload Payment
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="complaint.php" class="btn btn-outline-warning w-100 p-3">
                                                <i class="fas fa-exclamation-circle fa-2x mb-2"></i><br>Submit Complaint
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="profile.php" class="btn btn-outline-info w-100 p-3">
                                                <i class="fas fa-user fa-2x mb-2"></i><br>My Profile
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Notifications -->
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-bell"></i> Recent Notifications</h5>
                                    <a href="notifications.php" class="btn btn-sm btn-primary">View All</a>
                                </div>
                                <div class="card-body">
                                    <?php if (count($notifications) > 0): ?>
                                        <?php foreach (array_slice($notifications, 0, 5) as $notif): ?>
                                            <div class="border-bottom py-2">
                                                <div class="d-flex justify-content-between">
                                                    <strong><?php echo htmlspecialchars($notif['title']); ?></strong>
                                                    <small class="text-muted"><?php echo date('d M Y', strtotime($notif['created_at'])); ?></small>
                                                </div>
                                                <p class="mb-0 text-muted"><?php echo htmlspecialchars(substr($notif['message'], 0, 100)); ?>...</p>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted mb-0">No notifications</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
