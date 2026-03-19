<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireStudent();

$student = getStudentByUserId($_SESSION['user_id']);
$room = $student['room_id'] ? getRoomById($student['room_id']) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Room - Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-color: #667eea; --secondary-color: #764ba2; }
        body { background: #f5f7fa; }
        .sidebar { background: white; min-height: 100vh; box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1); }
        .sidebar .nav-link { color: #333; padding: 12px 20px; border-radius: 8px; margin: 5px 10px; transition: all 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; }
        .info-card { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar p-0">
                <div class="p-3 text-center border-bottom">
                    <h5 class="mb-0 text-primary"><i class="fas fa-building"></i> Royal Hostel</h5>
                    <small class="text-muted">Student Panel</small>
                </div>
                <ul class="nav flex-column mt-3">
                    <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="nav-item"><a href="profile.php" class="nav-link"><i class="fas fa-user"></i> My Profile</a></li>
                    <li class="nav-item"><a href="room.php" class="nav-link active"><i class="fas fa-bed"></i> My Room</a></li>
                    <li class="nav-item"><a href="fees.php" class="nav-link"><i class="fas fa-money-bill"></i> My Fees</a></li>
                    <li class="nav-item"><a href="upload_payment.php" class="nav-link"><i class="fas fa-upload"></i> Upload Payment</a></li>
                    <li class="nav-item"><a href="notifications.php" class="nav-link"><i class="fas fa-bell"></i> Notifications</a></li>
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
                                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                
                <div class="p-4">
                    <h4 class="mb-4"><i class="fas fa-bed"></i> My Room</h4>
                    
                    <?php if ($room): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card p-4 mb-4">
                                    <h5 class="mb-4">Room Information</h5>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Room Number</label>
                                        <h4><?php echo htmlspecialchars($room['room_number']); ?></h4>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Room Type</label>
                                        <p class="fw-bold"><?php echo ucfirst($room['room_type']); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Bed Number</label>
                                        <p class="fw-bold"><?php echo $student['bed_number']; ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Price per Month</label>
                                        <p class="fw-bold">₹<?php echo number_format($room['price_per_bed'], 2); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card p-4 mb-4">
                                    <h5 class="mb-4">Room Status</h5>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Total Beds</label>
                                        <p class="fw-bold"><?php echo $room['total_beds']; ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Occupied Beds</label>
                                        <p class="fw-bold"><?php echo $room['occupied_beds']; ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Available Beds</label>
                                        <p class="fw-bold text-success"><?php echo $room['available_beds']; ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Status</label>
                                        <p class="fw-bold">
                                            <span class="badge bg-<?php echo $room['status'] == 'available' ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst($room['status']); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($room['description']): ?>
                        <div class="info-card p-4">
                            <h5 class="mb-3">Room Description</h5>
                            <p><?php echo htmlspecialchars($room['description']); ?></p>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="info-card p-5 text-center">
                            <i class="fas fa-bed fa-4x text-muted mb-3"></i>
                            <h5>No Room Assigned</h5>
                            <p class="text-muted">You haven't been assigned a room yet. Please contact the administrator.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
