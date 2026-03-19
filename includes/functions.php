<?php
/**
 * Helper Functions
 * Hostel Management System
 */

/**
 * Get dashboard statistics for admin
 */
function getDashboardStats() {
    global $conn;
    
    $stats = [];
    
    // Total students
    $result = $conn->query("SELECT COUNT(*) as count FROM students WHERE status = 'active'");
    $stats['total_students'] = $result->fetch_assoc()['count'];
    
    // Total rooms
    $result = $conn->query("SELECT COUNT(*) as count FROM rooms");
    $stats['total_rooms'] = $result->fetch_assoc()['count'];
    
    // Available beds
    $result = $conn->query("SELECT SUM(available_beds) as total FROM rooms");
    $stats['available_beds'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Total income this month
    $currentMonth = date('F');
    $currentYear = date('Y');
    $stats['monthly_income'] = getMonthlyIncome($currentMonth, $currentYear);
    
    // Total income (all time)
    $result = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM fees WHERE status = 'paid'");
    $stats['total_income'] = $result->fetch_assoc()['total'];
    
    // Pending payments
    $result = $conn->query("SELECT COUNT(*) as count FROM fees WHERE status = 'pending'");
    $stats['pending_payments'] = $result->fetch_assoc()['count'];
    
    // Pending complaints
    $result = $conn->query("SELECT COUNT(*) as count FROM complaints WHERE status = 'pending'");
    $stats['pending_complaints'] = $result->fetch_assoc()['count'];
    
    return $stats;
}

/**
 * Get student fee status
 */
function getStudentFeeStatus($student_id) {
    global $conn;
    
    $fees = [];
    
    // Get all fees for student
    $stmt = $conn->prepare("SELECT * FROM fees WHERE student_id = ? ORDER BY year DESC, month DESC");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $fees[] = $row;
    }
    
    return $fees;
}

/**
 * Get student mess payments
 */
function getStudentMessPayments($student_id) {
    global $conn;
    
    $payments = [];
    
    $stmt = $conn->prepare("SELECT * FROM mess_payments WHERE student_id = ? ORDER BY year DESC, month DESC");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
    
    return $payments;
}

/**
 * Get student notifications
 */
function getStudentNotifications($user_id) {
    global $conn;
    
    $notifications = [];
    
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? OR user_id IS NULL ORDER BY created_at DESC LIMIT 20");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    
    return $notifications;
}

/**
 * Get unread notification count
 */
function getUnreadNotificationCount($user_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? OR user_id IS NULL AND is_read = 'no'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'];
}

/**
 * Mark notification as read
 */
function markNotificationAsRead($notification_id) {
    global $conn;
    
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 'yes' WHERE id = ?");
    $stmt->bind_param("i", $notification_id);
    return $stmt->execute();
}

/**
 * Send notification to all students
 */
function sendNotificationToAll($title, $message, $type = 'general') {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (NULL, ?, ?, ?)");
    $stmt->bind_param("sss", $title, $message, $type);
    return $stmt->execute();
}

/**
 * Send notification to specific student
 */
function sendNotificationToStudent($user_id, $title, $message, $type = 'general') {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $title, $message, $type);
    return $stmt->execute();
}

/**
 * Get months list
 */
function getMonthsList() {
    return [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
}

/**
 * Get years list (last 10 years)
 */
function getYearsList() {
    $years = [];
    $currentYear = date('Y');
    for ($i = $currentYear; $i >= $currentYear - 10; $i--) {
        $years[] = $i;
    }
    return $years;
}

/**
 * Format date
 */
function formatDate($date) {
    return date('d M Y', strtotime($date));
}

/**
 * Format date with time
 */
function formatDateTime($date) {
    return date('d M Y, h:i A', strtotime($date));
}

/**
 * Get room type badge class
 */
function getRoomTypeBadgeClass($type) {
    $classes = [
        'single' => 'bg-success',
        'double' => 'bg-primary',
        'triple' => 'bg-warning',
        'dormitory' => 'bg-info'
    ];
    return $classes[$type] ?? 'bg-secondary';
}

/**
 * Get status badge class
 */
function getStatusBadgeClass($status) {
    $classes = [
        'active' => 'bg-success',
        'inactive' => 'bg-secondary',
        'left' => 'bg-danger',
        'paid' => 'bg-success',
        'pending' => 'bg-warning',
        'rejected' => 'bg-danger',
        'available' => 'bg-success',
        'full' => 'bg-danger',
        'maintenance' => 'bg-warning',
        'resolved' => 'bg-success',
        'in_progress' => 'bg-info'
    ];
    return $classes[$status] ?? 'bg-secondary';
}

/**
 * Search students
 */
function searchStudents($searchTerm) {
    global $conn;
    
    $searchTerm = "%$searchTerm%";
    $stmt = $conn->prepare("SELECT s.*, r.room_number, r.room_type 
                           FROM students s 
                           LEFT JOIN rooms r ON s.room_id = r.id 
                           WHERE s.student_name LIKE ? 
                           OR s.cnic LIKE ? 
                           OR s.father_name LIKE ? 
                           OR s.phone LIKE ?
                           ORDER BY s.id DESC");
    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    
    return $students;
}

/**
 * Get monthly report
 */
function getMonthlyReport($month, $year) {
    global $conn;
    
    $report = [];
    
    // Get fees for the month
    $stmt = $conn->prepare("SELECT f.*, s.student_name, s.father_name, r.room_number 
                           FROM fees f 
                           LEFT JOIN students s ON f.student_id = s.id 
                           LEFT JOIN rooms r ON s.room_id = r.id
                           WHERE f.month = ? AND f.year = ?
                           ORDER BY f.payment_date DESC");
    $stmt->bind_param("si", $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $report['fees'] = [];
    $totalAmount = 0;
    while ($row = $result->fetch_assoc()) {
        $report['fees'][] = $row;
        if ($row['status'] === 'paid') {
            $totalAmount += $row['amount'];
        }
    }
    $report['total_amount'] = $totalAmount;
    
    // Get mess payments for the month
    $stmt = $conn->prepare("SELECT m.*, s.student_name, s.father_name 
                           FROM mess_payments m 
                           LEFT JOIN students s ON m.student_id = s.id
                           WHERE m.month = ? AND m.year = ?
                           ORDER BY m.payment_date DESC");
    $stmt->bind_param("si", $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $report['mess_payments'] = [];
    $totalMessAmount = 0;
    while ($row = $result->fetch_assoc()) {
        $report['mess_payments'][] = $row;
        if ($row['status'] === 'paid') {
            $totalMessAmount += $row['amount'];
        }
    }
    $report['total_mess_amount'] = $totalMessAmount;
    $report['total_income'] = $totalAmount + $totalMessAmount;
    
    return $report;
}
