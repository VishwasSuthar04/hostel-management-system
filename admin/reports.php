<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

// Get current month and year
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('F');
$currentYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

$report = getMonthlyReport($currentMonth, $currentYear);
$months = getMonthsList();
$years = getYearsList();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Hostel Management System</title>
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
                    <small class="text-muted">Admin Panel</small>
                </div>
                <ul class="nav flex-column mt-3">
                    <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="nav-item"><a href="students.php" class="nav-link"><i class="fas fa-users"></i> Students</a></li>
                    <li class="nav-item"><a href="rooms.php" class="nav-link"><i class="fas fa-bed"></i> Rooms</a></li>
                    <li class="nav-item"><a href="fees.php" class="nav-link"><i class="fas fa-money-bill"></i> Fees</a></li>
                    <li class="nav-item"><a href="mess_payments.php" class="nav-link"><i class="fas fa-utensils"></i> Mess Payments</a></li>
                    <li class="nav-item"><a href="notifications.php" class="nav-link"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li class="nav-item"><a href="complaints.php" class="nav-link"><i class="fas fa-exclamation-circle"></i> Complaints</a></li>
                    <li class="nav-item"><a href="search.php" class="nav-link"><i class="fas fa-search"></i> Search</a></li>
                    <li class="nav-item"><a href="reports.php" class="nav-link active"><i class="fas fa-chart-bar"></i> Reports</a></li>
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
                
                <div class="p-4">
                    <h4 class="mb-4"><i class="fas fa-chart-bar"></i> Monthly Reports</h4>
                    
                    <!-- Month/Year Filter -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" action="" class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Month</label>
                                    <select class="form-select" name="month">
                                        <?php foreach ($months as $month): ?>
                                            <option value="<?php echo $month; ?>" <?php echo $month == $currentMonth ? 'selected' : ''; ?>><?php echo $month; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Year</label>
                                    <select class="form-select" name="year">
                                        <?php foreach ($years as $year): ?>
                                            <option value="<?php echo $year; ?>" <?php echo $year == $currentYear ? 'selected' : ''; ?>><?php echo $year; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Summary Cards -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="stat-card">
                                <h6 class="text-muted">Room Fees</h6>
                                <h3>RS <?php echo number_format($report['total_amount'], 2); ?></h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <h6 class="text-muted">Mess Fees</h6>
                                <h3>RS <?php echo number_format($report['total_mess_amount'], 2); ?></h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <h6 class="text-muted">Total Income</h6>
                                <h3>RS <?php echo number_format($report['total_income'], 2); ?></h3>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Fee Details -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Room Fee Details - <?php echo $currentMonth . ' ' . $currentYear; ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Room</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Method</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($report['fees']) > 0): ?>
                                            <?php foreach ($report['fees'] as $fee): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($fee['student_name']); ?></td>
                                                    <td><?php echo $fee['room_number'] ?? '-'; ?></td>
                                                    <td>₹<?php echo number_format($fee['amount'], 2); ?></td>
                                                    <td><?php echo date('d M Y', strtotime($fee['payment_date'])); ?></td>
                                                    <td><?php echo ucfirst($fee['payment_method']); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $fee['status'] == 'paid' ? 'success' : 'warning'; ?>">
                                                            <?php echo ucfirst($fee['status']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-3">No fee records found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mess Payment Details -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Mess Payment Details - <?php echo $currentMonth . ' ' . $currentYear; ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Method</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($report['mess_payments']) > 0): ?>
                                            <?php foreach ($report['mess_payments'] as $payment): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($payment['student_name']); ?></td>
                                                    <td>₹<?php echo number_format($payment['amount'], 2); ?></td>
                                                    <td><?php echo date('d M Y', strtotime($payment['payment_date'])); ?></td>
                                                    <td><?php echo ucfirst($payment['payment_method']); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $payment['status'] == 'paid' ? 'success' : 'warning'; ?>">
                                                            <?php echo ucfirst($payment['status']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-3">No mess payment records found</td>
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
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
