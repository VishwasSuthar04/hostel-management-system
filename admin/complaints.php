<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

// Handle complaint reply
if (isset($_POST['reply_complaint'])) {
    $complaint_id = (int)$_POST['complaint_id'];
    $reply = sanitize($_POST['reply']);
    
    $stmt = $conn->prepare("UPDATE complaints SET reply = ?, replied_by = ?, replied_at = NOW(), status = 'resolved' WHERE id = ?");
    $stmt->bind_param("sii", $reply, $_SESSION['user_id'], $complaint_id);
    
    if ($stmt->execute()) {
        setMessage("Reply sent successfully!", "success");
    }
    redirect('complaints.php');
}

// Get all complaints with student details
$complaints = [];
$result = $conn->query("SELECT c.*, s.student_name, s.father_name, u.username as admin_name 
                        FROM complaints c 
                        LEFT JOIN students s ON c.student_id = s.id 
                        LEFT JOIN users u ON c.replied_by = u.id
                        ORDER BY c.created_at DESC");
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
                    <li class="nav-item"><a href="mess_payments.php" class="nav-link"><i class="fas fa-utensils"></i> Mess Payments</a></li>
                    <li class="nav-item"><a href="notifications.php" class="nav-link"><i class="fas fa-bell"></i> Notifications</a></li>
                    <li class="nav-item"><a href="complaints.php" class="nav-link active"><i class="fas fa-exclamation-circle"></i> Complaints</a></li>
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
                    <h4 class="mb-4"><i class="fas fa-exclamation-circle"></i> Complaints Management</h4>
                    
                    <?php displayMessage(); ?>
                    
                    <div class="table-card">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Student</th>
                                        <th>Subject</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($complaints) > 0): ?>
                                        <?php foreach ($complaints as $complaint): ?>
                                            <tr>
                                                <td><?php echo $complaint['id']; ?></td>
                                                <td>
                                                    <?php echo htmlspecialchars($complaint['student_name']); ?>
                                                    <br><small class="text-muted"><?php echo $complaint['father_name']; ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($complaint['subject']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($complaint['description'], 0, 50)); ?>...</td>
                                                <td>
                                                    <span class="badge bg-<?php echo $complaint['status'] == 'resolved' ? 'success' : ($complaint['status'] == 'in_progress' ? 'info' : 'warning'); ?>">
                                                        <?php echo ucfirst($complaint['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('d M Y', strtotime($complaint['created_at'])); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#replyModal<?php echo $complaint['id']; ?>">
                                                        <i class="fas fa-reply"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            
                                            <!-- Reply Modal -->
                                            <div class="modal fade" id="replyModal<?php echo $complaint['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Reply to Complaint</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" action="">
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <strong>Subject:</strong> <?php echo htmlspecialchars($complaint['subject']); ?>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <strong>Description:</strong>
                                                                    <p><?php echo htmlspecialchars($complaint['description']); ?></p>
                                                                </div>
                                                                <?php if ($complaint['reply']): ?>
                                                                    <div class="mb-3">
                                                                        <strong>Previous Reply:</strong>
                                                                        <p><?php echo htmlspecialchars($complaint['reply']); ?></p>
                                                                        <small class="text-muted">Replied by: <?php echo $complaint['admin_name']; ?> on <?php echo date('d M Y', strtotime($complaint['replied_at'])); ?></small>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <input type="hidden" name="complaint_id" value="<?php echo $complaint['id']; ?>">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Your Reply *</label>
                                                                    <textarea class="form-control" name="reply" rows="4" required><?php echo $complaint['reply'] ?? ''; ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" name="reply_complaint" class="btn btn-primary">Send Reply</button>
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <p class="text-muted mb-0">No complaints found.</p>
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
