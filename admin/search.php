<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

$searchTerm = '';
$students = [];

if (isset($_GET['search'])) {
    $searchTerm = sanitize($_GET['search']);
    if (!empty($searchTerm)) {
        $students = searchStudents($searchTerm);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - Hostel Management System</title>
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
                    <li class="nav-item"><a href="complaints.php" class="nav-link"><i class="fas fa-exclamation-circle"></i> Complaints</a></li>
                    <li class="nav-item"><a href="search.php" class="nav-link active"><i class="fas fa-search"></i> Search</a></li>
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
                    <h4 class="mb-4"><i class="fas fa-search"></i> Search Students</h4>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" action="" class="row g-3">
                                <div class="col-md-10">
                                    <input type="text" class="form-control" name="search" placeholder="Search by name, CNIC, father name, or phone..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <?php if (!empty($searchTerm)): ?>
                        <div class="table-card">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Father Name</th>
                                            <th>CNIC</th>
                                            <th>Phone</th>
                                            <th>Room</th>
                                            <th>Entry Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($students) > 0): ?>
                                            <?php foreach ($students as $student): ?>
                                                <tr>
                                                    <td><?php echo $student['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($student['father_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($student['cnic']); ?></td>
                                                    <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                                    <td><?php echo $student['room_number'] ? $student['room_number'] : '-'; ?></td>
                                                    <td><?php echo date('d M Y', strtotime($student['entry_date'])); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $student['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                            <?php echo ucfirst($student['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="9" class="text-center py-4">
                                                    <p class="text-muted mb-0">No students found matching "<?php echo htmlspecialchars($searchTerm); ?>"</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
