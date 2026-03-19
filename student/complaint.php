<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireStudent();

$student = getStudentByUserId($_SESSION['user_id']);

// Handle complaint submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = sanitize($_POST['subject']);
    $description = sanitize($_POST['description']);
    
    if (!empty($subject) && !empty($description)) {
        $stmt = $conn->prepare("INSERT INTO complaints (student_id, subject, description, status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("iss", $student['id'], $subject, $description);
        
        if ($stmt->execute()) {
            setMessage("Complaint submitted successfully!", "success");
        } else {
            setMessage("Failed to submit complaint", "danger");
        }
    } else {
        setMessage("Please fill in all fields", "danger");
    }
    redirect('complaint.php');
}

// Get student's complaints
$complaints = [];
$stmt = $conn->prepare("SELECT * FROM complaints WHERE student_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $student['id']);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaints - Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-color: #667eea; --secondary-color: #764ba2; }
        body { background: #f5f7fa; }
        .sidebar { background: white; min-height: 100vh; box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1); }
        .sidebar .nav-link { color: #333; padding: 12px 20px; border-radius: 8px; margin: 5px 10px; transition: all 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; }
        .complaint-card { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05); }
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
                    <li class="nav-item"><a href="upload_payment.php" class="nav-link"><i class="fas fa-upload"></i> Upload Payment</a></li>
                    <li class="nav-item"><a href="notifications.php" class="nav-link"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li class="nav-item"><a href="complaint.php" class="nav-link active"><i class="fas fa-exclamation-circle"></i> Complaints</a></li>
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
                    <h4 class="mb-4"><i class="fas fa-exclamation-circle"></i> Submit Complaint</h4>
                    
                    <?php displayMessage(); ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="complaint-card p-4">
                                <h5 class="mb-4">New Complaint</h5>
                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label class="form-label">Subject *</label>
                                        <input type="text" class="form-control" name="subject" placeholder="Enter subject" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description *</label>
                                        <textarea class="form-control" name="description" rows="5" placeholder="Describe your complaint" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Submit Complaint
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="complaint-card p-4">
                                <h5 class="mb-4">My Complaints</h5>
                                <?php if (count($complaints) > 0): ?>
                                    <?php foreach ($complaints as $complaint): ?>
                                        <div class="border-bottom py-3">
                                            <div class="d-flex justify-content-between">
                                                <h6><?php echo htmlspecialchars($complaint['subject']); ?></h6>
                                                <span class="badge bg-<?php echo $complaint['status'] == 'resolved' ? 'success' : ($complaint['status'] == 'in_progress' ? 'info' : 'warning'); ?>">
                                                    <?php echo ucfirst($complaint['status']); ?>
                                                </span>
                                            </div>
                                            <p class="text-muted mb-1"><?php echo htmlspecialchars(substr($complaint['description'], 0, 100)); ?>...</p>
                                            <small class="text-muted"><?php echo date('d M Y', strtotime($complaint['created_at'])); ?></small>
                                            <?php if ($complaint['reply']): ?>
                                                <div class="mt-2 p-2 bg-light rounded">
                                                    <strong>Reply:</strong>
                                                    <p class="mb-0"><?php echo htmlspecialchars($complaint['reply']); ?></p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">No complaints submitted yet.</p>
                                <?php endif; ?>
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
