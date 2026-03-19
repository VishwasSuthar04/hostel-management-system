<?php
/**
 * Authentication Functions
 * Hostel Management System
 */

// Check if user is logged in
function requireLogin() {
    if (!isLoggedIn()) {
        redirect(BASE_URL . 'login.php');
    }
}

// Check if user is admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        setMessage("Access denied. Admin privileges required.", "danger");
        redirect(BASE_URL . 'index.php');
    }
}

// Check if user is student
function requireStudent() {
    requireLogin();
    if (!isStudent()) {
        setMessage("Access denied. Student privileges required.", "danger");
        redirect(BASE_URL . 'index.php');
    }
}

// Login user
function loginUser($user_id, $username, $role) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
    $_SESSION['login_time'] = time();
}

// Logout user
function logout() {
    // Destroy session
    session_unset();
    session_destroy();
    redirect(BASE_URL . 'login.php');
}

// Get current user id
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Get current username
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

// Get current user role
function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}
