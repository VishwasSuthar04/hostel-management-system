<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireStudent();

$student = getStudentByUserId($_SESSION['user_id']);
$fees = getStudentFeeStatus($student['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Fees - Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-color: #667eea; --secondary-color: #764ba2; }
        body { background: #f5f7fa; }
        .sidebar { background: white; min-height: 100vh; box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1); }
        .sidebar .nav-link { color: #333; padding: 12px 20px; border-radius: 8px; margin: 5px 10px; transition: all 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; }
        .table-card { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05); }
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
                    <li class="nav-item"><a href="room.php" class="nav-link"><i class="fas fa-bed"></i> My Room</a></li>
                    <li class="nav-item"><a href="fees.php" class="nav-link active"><i class="fas fa-money-bill"></i> My Fees</a></li>
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
                    <h4 class="mb-4"><i class="fas fa-money-bill"></i> My Fees</h4>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6>Monthly Fee</h6>
                                    <h3>₹<?php echo number_format($student['monthly_fee'], 2); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-card">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Amount</th>
                                        <th>Month</th>
                                        <th>Year</th>
                                        <th>Payment Date</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Proof</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($fees) > 0): ?>
                                        <?php foreach ($fees as $fee): ?>
                                            <tr>
                                                <td><?php echo $fee['id']; ?></td>
                                                <td>₹<?php echo number_format($fee['amount'], 2); ?></td>
                                                <td><?php echo $fee['month']; ?></td>
                                                <td><?php echo $fee['year']; ?></td>
                                                <td><?php echo date('d M Y', strtotime($fee['payment_date'])); ?></td>
                                                <td><?php echo ucfirst($fee['payment_method']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $fee['status'] == 'paid' ? 'success' : ($fee['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                                        <?php echo ucfirst($fee['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($fee['payment_proof']): ?>
                                                        <a href="../uploads/<?php echo $fee['payment_proof']; ?>" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <p class="text-muted mb-0">No fee records found.</p>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
