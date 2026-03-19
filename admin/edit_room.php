<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

// Get room ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setMessage("Invalid room ID", "danger");
    redirect('rooms.php');
}

$room_id = (int)$_GET['id'];

// Get room details
$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    setMessage("Room not found", "danger");
    redirect('rooms.php');
}

$room = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = sanitize($_POST['room_number']);
    $room_type = sanitize($_POST['room_type']);
    $total_beds = (int)$_POST['total_beds'];
    $price_per_bed = sanitize($_POST['price_per_bed']);
    $description = sanitize($_POST['description']);
    $status = sanitize($_POST['status']);
    
    // Validation
    $errors = [];
    
    if (empty($room_number)) $errors[] = "Room number is required";
    if (empty($room_type)) $errors[] = "Room type is required";
    if (empty($total_beds) || $total_beds < 1) $errors[] = "Total beds must be at least 1";
    if (empty($price_per_bed)) $errors[] = "Price per bed is required";
    
    // Check if room number already exists (excluding current room)
    $stmt = $conn->prepare("SELECT id FROM rooms WHERE room_number = ? AND id != ?");
    $stmt->bind_param("si", $room_number, $room_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = "Room number already exists";
    }
    
    // Check if reducing total beds below occupied
    if ($total_beds < $room['occupied_beds']) {
        $errors[] = "Cannot reduce total beds below occupied beds ({$room['occupied_beds']})";
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE rooms SET room_number = ?, room_type = ?, total_beds = ?, price_per_bed = ?, description = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssidssi", $room_number, $room_type, $total_beds, $price_per_bed, $description, $status, $room_id);
        
        if ($stmt->execute()) {
            // Update room occupancy
            updateRoomOccupancy($room_id);
            
            setMessage("Room updated successfully!", "success");
            redirect('rooms.php');
        } else {
            $errors[] = "Failed to update room";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room - Hostel Management System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }
        
        body {
            background: #f5f7fa;
        }
        
        .sidebar {
            background: white;
            min-height: 100vh;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar .nav-link {
            color: #333;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 10px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .form-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-3 text-center border-bottom">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-building"></i> Royal Hostel
                    </h5>
                    <small class="text-muted">Admin Panel</small>
                </div>
                <ul class="nav flex-column mt-3">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="students.php" class="nav-link">
                            <i class="fas fa-users"></i> Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="rooms.php" class="nav-link active">
                            <i class="fas fa-bed"></i> Rooms
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="fees.php" class="nav-link">
                            <i class="fas fa-money-bill"></i> Fees
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="mess_payments.php" class="nav-link">
                            <i class="fas fa-utensils"></i> Mess Payments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="notifications.php" class="nav-link">
                            <i class="fas fa-bell"></i> Notifications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="complaints.php" class="nav-link">
                            <i class="fas fa-exclamation-circle"></i> Complaints
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="search.php" class="nav-link">
                            <i class="fas fa-search"></i> Search
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="reports.php" class="nav-link">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <a href="logout.php" class="nav-link text-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 p-0">
                <!-- Top Header -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
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
                
                <!-- Page Content -->
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4><i class="fas fa-edit"></i> Edit Room</h4>
                        <a href="rooms.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                    
                    <!-- Display Errors -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Edit Room Form -->
                    <div class="form-card">
                        <div class="card-body p-4">
                            <form method="POST" action="">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Room Number *</label>
                                        <input type="text" class="form-control" name="room_number" value="<?php echo htmlspecialchars($room['room_number']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Room Type *</label>
                                        <select class="form-select" name="room_type" required>
                                            <option value="single" <?php echo ($room['room_type'] == 'single') ? 'selected' : ''; ?>>Single Room</option>
                                            <option value="double" <?php echo ($room['room_type'] == 'double') ? 'selected' : ''; ?>>Double Room</option>
                                            <option value="triple" <?php echo ($room['room_type'] == 'triple') ? 'selected' : ''; ?>>Triple Room</option>
                                            <option value="dormitory" <?php echo ($room['room_type'] == 'dormitory') ? 'selected' : ''; ?>>Dormitory</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Total Beds *</label>
                                        <input type="number" class="form-control" name="total_beds" value="<?php echo $room['total_beds']; ?>" min="1" required>
                                        <small class="text-muted">Currently occupied: <?php echo $room['occupied_beds']; ?></small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Price per Bed (₹) *</label>
                                        <input type="number" class="form-control" name="price_per_bed" value="<?php echo $room['price_per_bed']; ?>" step="0.01" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="available" <?php echo ($room['status'] == 'available') ? 'selected' : ''; ?>>Available</option>
                                            <option value="full" <?php echo ($room['status'] == 'full') ? 'selected' : ''; ?>>Full</option>
                                            <option value="maintenance" <?php echo ($room['status'] == 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($room['description'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> Update Room
                                    </button>
                                    <a href="rooms.php" class="btn btn-secondary btn-lg ms-2">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
