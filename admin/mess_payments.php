<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

// Handle mess payment status update
if (isset($_POST['update_status'])) {
    $payment_id = (int)$_POST['payment_id'];
    $status = sanitize($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE mess_payments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $payment_id);
    
    if ($stmt->execute()) {
        setMessage("Mess payment status updated successfully!", "success");
    }
    redirect('mess_payments.php');
}

// Get mess payments with student details
$payments = [];
$result = $conn->query("SELECT m.*, s.student_name, s.father_name 
                        FROM mess_payments m 
                        LEFT JOIN students s ON m.student_id = s.id
                        ORDER BY m.payment_date DESC");
while ($row = $result->fetch_assoc()) {
    $payments[] = $row;
}

// Get all students
$students = getAllStudents();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mess Payments - Hostel Management System</title>
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
                    <small class="text-muted">Admin Panel</small>
                </div>
                <ul class="nav flex-column mt-3">
                    <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="nav-item"><a href="students.php" class="nav-link"><i class="fas fa-users"></i> Students</a></li>
                    <li class="nav-item"><a href="rooms.php" class="nav-link"><i class="fas fa-bed"></i> Rooms</a></li>
                    <li class="nav-item"><a href="fees.php" class="nav-link"><i class="fas fa-money-bill"></i> Fees</a></li>
                    <li class="nav-item"><a href="mess_payments.php" class="nav-link active"><i class="fas fa-utensils"></i> Mess Payments</a></li>
                    <li class="nav-item"><a href="notifications.php" class="nav-link"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li class="nav-item"><a href="complaints.php" class="nav-link"><i class="fas fa-exclamation-circle"></i> Complaints</a></li>
                    <li class="nav-item"><a href="search.php" class="nav-link"><i class="fas fa-search"></i> Search</a></li>
                    <li class="nav-item"><a href="reports.php" class="nav-link"><i class="fas fa-chart-bar"></i> Reports</a></li>
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
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4><i class="fas fa-utensils"></i> Mess Payment Management</h4>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMessModal">
                            <i class="fas fa-plus"></i> Add Mess Payment
                        </button>
                    </div>
                    
                    <?php displayMessage(); ?>
                    
                    <div class="table-card">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Student</th>
                                        <th>Amount</th>
                                        <th>Month</th>
                                        <th>Year</th>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($payments) > 0): ?>
                                        <?php foreach ($payments as $payment): ?>
                                            <tr>
                                                <td><?php echo $payment['id']; ?></td>
                                                <td><?php echo htmlspecialchars($payment['student_name']); ?></td>
                                                <td>₹<?php echo number_format($payment['amount'], 2); ?></td>
                                                <td><?php echo $payment['month']; ?></td>
                                                <td><?php echo $payment['year']; ?></td>
                                                <td><?php echo date('d M Y', strtotime($payment['payment_date'])); ?></td>
                                                <td><?php echo ucfirst($payment['payment_method']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $payment['status'] == 'paid' ? 'success' : ($payment['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                                        <?php echo ucfirst($payment['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal<?php echo $payment['id']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            
                                            <div class="modal fade" id="updateStatusModal<?php echo $payment['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Update Status</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" action="">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Status</label>
                                                                    <select class="form-select" name="status">
                                                                        <option value="paid" <?php echo $payment['status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                                                        <option value="pending" <?php echo $payment['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                        <option value="rejected" <?php echo $payment['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <p class="text-muted mb-0">No mess payment records found.</p>
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
    
    <!-- Add Mess Payment Modal -->
    <div class="modal fade" id="addMessModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Mess Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="add_mess_payment.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Student *</label>
                            <select class="form-select" name="student_id" required>
                                <option value="">Select Student</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo $student['id']; ?>">
                                        <?php echo htmlspecialchars($student['student_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount (₹) *</label>
                            <input type="number" class="form-control" name="amount" step="0.01" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Month *</label>
                                <select class="form-select" name="month" required>
                                    <?php foreach (getMonthsList() as $month): ?>
                                        <option value="<?php echo $month; ?>"><?php echo $month; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Year *</label>
                                <select class="form-select" name="year" required>
                                    <?php foreach (getYearsList() as $year): ?>
                                        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Date *</label>
                            <input type="date" class="form-control" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Method *</label>
                                <select class="form-select" name="payment_method" required>
                                    <option value="cash">Cash</option>
                                    <option value="account">Account Transfer</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Transaction ID</label>
                                <input type="text" class="form-control" name="transaction_id">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Payment</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
