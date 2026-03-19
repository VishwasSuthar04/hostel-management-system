<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireStudent();

$student = getStudentByUserId($_SESSION['user_id']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = sanitize($_POST['amount']);
    $month = sanitize($_POST['month']);
    $year = (int)$_POST['year'];
    $payment_method = sanitize($_POST['payment_method']);
    $transaction_id = sanitize($_POST['transaction_id']);
    
    // Handle file upload
    $payment_proof = null;
    if (!empty($_FILES['payment_proof']['name'])) {
        $upload = uploadFile($_FILES['payment_proof'], 'payment_');
        if ($upload['success']) {
            $payment_proof = $upload['filename'];
        } else {
            setMessage($upload['message'], 'danger');
            redirect('upload_payment.php');
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO fees (student_id, amount, month, year, payment_date, payment_method, transaction_id, payment_proof, status) VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, 'pending')");
    $stmt->bind_param("idsisss", $student['id'], $amount, $month, $year, $payment_method, $transaction_id, $payment_proof);
    
    if ($stmt->execute()) {
        setMessage("Payment proof uploaded successfully! Pending admin approval.", "success");
    } else {
        setMessage("Failed to submit payment", "danger");
    }
    redirect('fees.php');
}

$months = getMonthsList();
$years = getYearsList();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Payment - Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-color: #667eea; --secondary-color: #764ba2; }
        body { background: #f5f7fa; }
        .sidebar { background: white; min-height: 100vh; box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1); }
        .sidebar .nav-link { color: #333; padding: 12px 20px; border-radius: 8px; margin: 5px 10px; transition: all 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; }
        .form-card { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05); }
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
                    <li class="nav-item"><a href="fees.php" class="nav-link"><i class="fas fa-money-bill"></i> My Fees</a></li>
                    <li class="nav-item"><a href="upload_payment.php" class="nav-link active"><i class="fas fa-upload"></i> Upload Payment</a></li>
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
                    <h4 class="mb-4"><i class="fas fa-upload"></i> Upload Payment Proof</h4>
                    
                    <?php displayMessage(); ?>
                    
                    <div class="form-card p-4">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Amount (₹) *</label>
                                    <input type="number" class="form-control" name="amount" step="0.01" value="<?php echo $student['monthly_fee']; ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Month *</label>
                                    <select class="form-select" name="month" required>
                                        <?php foreach ($months as $month): ?>
                                            <option value="<?php echo $month; ?>" <?php echo $month == date('F') ? 'selected' : ''; ?>><?php echo $month; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Year *</label>
                                    <select class="form-select" name="year" required>
                                        <?php foreach ($years as $year): ?>
                                            <option value="<?php echo $year; ?>" <?php echo $year == date('Y') ? 'selected' : ''; ?>><?php echo $year; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Payment Method *</label>
                                    <select class="form-select" name="payment_method" required>
                                        <option value="cash">Cash</option>
                                        <option value="account">Account Transfer</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Transaction ID</label>
                                    <input type="text" class="form-control" name="transaction_id" placeholder="For online transfer">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Payment Proof (JPG, JPEG, PNG - Max 2MB) *</label>
                                    <input type="file" class="form-control" name="payment_proof" accept=".jpg,.jpeg,.png" required>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Submit Payment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
