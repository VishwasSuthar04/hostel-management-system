<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

// Handle send notification
if (isset($_POST['send_notification'])) {
    $title = sanitize($_POST['title']);
    $message = sanitize($_POST['message']);
    $type = sanitize($_POST['type']);
    $send_to = $_POST['send_to'];
    
    if ($send_to === 'all') {
        sendNotificationToAll($title, $message, $type);
    } else {
        $student_id = (int)$_POST['student_id'];
        // Get user_id for student
        $stmt = $conn->prepare("SELECT user_id FROM students WHERE id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        
        if ($student && $student['user_id']) {
            sendNotificationToStudent($student['user_id'], $title, $message, $type);
        }
    }
    
    setMessage("Notification sent successfully!", "success");
    redirect('notifications.php');
}

// Handle delete notification
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM notifications WHERE id = $id");
    setMessage("Notification deleted!", "success");
    redirect('notifications.php');
}

// Get all notifications
$notifications = [];
$result = $conn->query("SELECT n.*, u.username FROM notifications n LEFT JOIN users u ON n.user_id = u.id ORDER BY n.created_at DESC");
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

// Get all students
$students = getAllStudents();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Hostel Management System</title>
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
                    <li class="nav-item"><a href="notifications.php" class="nav-link active"><i class="fas fa-bell"></i> Notifications</a></li>
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
                        <h4><i class="fas fa-bell"></i> Notifications</h4>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendNotificationModal">
                            <i class="fas fa-paper-plane"></i> Send Notification
                        </button>
                    </div>
                    
                    <?php displayMessage(); ?>
                    
                    <div class="table-card">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Message</th>
                                        <th>Type</th>
                                        <th>Sent To</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($notifications) > 0): ?>
                                        <?php foreach ($notifications as $notif): ?>
                                            <tr>
                                                <td><?php echo $notif['id']; ?></td>
                                                <td><?php echo htmlspecialchars($notif['title']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($notif['message'], 0, 50)); ?>...</td>
                                                <td>
                                                    <span class="badge bg-<?php echo $notif['type'] == 'important' ? 'danger' : 'info'; ?>">
                                                        <?php echo ucfirst($notif['type']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $notif['user_id'] ? htmlspecialchars($notif['username']) : 'All Students'; ?></td>
                                                <td><?php echo date('d M Y', strtotime($notif['created_at'])); ?></td>
                                                <td>
                                                    <a href="notifications.php?delete=<?php echo $notif['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this notification?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <p class="text-muted mb-0">No notifications found.</p>
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
    
    <!-- Send Notification Modal -->
    <div class="modal fade" id="sendNotificationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Send To</label>
                            <select class="form-select" name="send_to" id="sendTo" onchange="toggleStudentSelect()">
                                <option value="all">All Students</option>
                                <option value="specific">Specific Student</option>
                            </select>
                        </div>
                        <div class="mb-3" id="studentSelect" style="display: none;">
                            <label class="form-label">Select Student</label>
                            <select class="form-select" name="student_id">
                                <option value="">Select Student</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['student_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message *</label>
                            <textarea class="form-control" name="message" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="type">
                                <option value="general">General</option>
                                <option value="fee">Fee Related</option>
                                <option value="mess">Mess Related</option>
                                <option value="important">Important</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="send_notification" class="btn btn-primary">Send Notification</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleStudentSelect() {
            var sendTo = document.getElementById('sendTo').value;
            var studentSelect = document.getElementById('studentSelect');
            if (sendTo === 'specific') {
                studentSelect.style.display = 'block';
            } else {
                studentSelect.style.display = 'none';
            }
        }
    </script>
</body>
</html>
