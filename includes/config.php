<?php
/**
 * Database Configuration
 * Hostel Management System
 */

// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'hostel_management');

// Create database connection
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
mysqli_query($conn, $sql);

// Select database
mysqli_select_db($conn, DB_NAME);

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8mb4");

// Session start
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get the base URL - determine project root from config.php location
$scriptPath = dirname(__FILE__);
$scriptPath = str_replace('\\', '/', $scriptPath);
// Find position of /includes and get the project root
$includesPos = strrpos($scriptPath, '/includes');
$projectRoot = substr($scriptPath, 0, $includesPos);
// Extract just the project folder name
$projectFolder = basename($projectRoot);
// URL encode the folder name to handle spaces
$projectFolderEncoded = rawurlencode($projectFolder);
define('BASE_URL', 'http://localhost/' . $projectFolderEncoded . '/');

// Upload directory
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');

// Allowed file types for upload
define('ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB

/**
 * Function to sanitize input
 */
function sanitize($input) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8'));
}

/**
 * Function to redirect
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Function to display message
 */
function setMessage($msg, $type = 'danger') {
    $_SESSION['message'] = $msg;
    $_SESSION['message_type'] = $type;
}

/**
 * Function to display message and clear it
 */
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $type = $_SESSION['message_type'] ?? 'danger';
        echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
                ' . $_SESSION['message'] . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

/**
 * Function to check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

/**
 * Function to check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Function to check if user is student
 */
function isStudent() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

/**
 * Function to upload file
 */
function uploadFile($file, $prefix = '') {
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Check file type
        if (!in_array($fileExtension, ALLOWED_TYPES)) {
            return ['success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, PNG, GIF are allowed.'];
        }
        
        // Check file size
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['success' => false, 'message' => 'File size exceeds 2MB limit.'];
        }
        
        // Generate unique filename
        $newFilename = $prefix . time() . '_' . bin2hex(random_bytes(4)) . '.' . $fileExtension;
        $targetPath = UPLOAD_DIR . $newFilename;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => true, 'filename' => $newFilename];
        } else {
            return ['success' => false, 'message' => 'Failed to upload file.'];
        }
    }
    return ['success' => false, 'message' => 'No file uploaded.'];
}

/**
 * Function to get student by user_id
 */
function getStudentByUserId($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Function to get room by id
 */
function getRoomById($room_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Function to calculate occupied beds
 */
function updateRoomOccupancy($room_id) {
    global $conn;
    
    // Count students in room
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM students WHERE room_id = ? AND status = 'active'");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $occupied = $row['count'];
    
    // Update room
    $update = $conn->prepare("UPDATE rooms SET occupied_beds = ? WHERE id = ?");
    $update->bind_param("ii", $occupied, $room_id);
    $update->execute();
    
    // Update status based on occupancy
    $room = getRoomById($room_id);
    if ($room) {
        $status = 'available';
        if ($occupied >= $room['total_beds']) {
            $status = 'full';
        } elseif ($occupied > 0) {
            $status = 'available';
        }
        $updateStatus = $conn->prepare("UPDATE rooms SET status = ? WHERE id = ?");
        $updateStatus->bind_param("si", $status, $room_id);
        $updateStatus->execute();
    }
}

/**
 * Function to get all rooms
 */
function getAllRooms() {
    global $conn;
    $result = $conn->query("SELECT * FROM rooms ORDER BY room_number");
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Function to get all students
 */
function getAllStudents() {
    global $conn;
    $result = $conn->query("SELECT s.*, r.room_number, r.room_type 
                           FROM students s 
                           LEFT JOIN rooms r ON s.room_id = r.id 
                           ORDER BY s.id DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Function to get total count
 */
function getTotalCount($table) {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as count FROM $table");
    $row = $result->fetch_assoc();
    return $row['count'];
}

/**
 * Function to get monthly income
 */
function getMonthlyIncome($month, $year) {
    global $conn;
    $stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM fees WHERE month = ? AND year = ? AND status = 'paid'");
    $stmt->bind_param("si", $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Include functions.php for additional helper functions
require_once __DIR__ . '/functions.php';
