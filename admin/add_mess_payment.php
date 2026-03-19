<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireAdmin();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)$_POST['student_id'];
    $amount = sanitize($_POST['amount']);
    $month = sanitize($_POST['month']);
    $year = (int)$_POST['year'];
    $payment_date = sanitize($_POST['payment_date']);
    $payment_method = sanitize($_POST['payment_method']);
    $transaction_id = sanitize($_POST['transaction_id']);
    $status = 'paid';
    
    // Validation
    $errors = [];
    
    if (empty($student_id)) $errors[] = "Student is required";
    if (empty($amount)) $errors[] = "Amount is required";
    if (empty($month)) $errors[] = "Month is required";
    if (empty($year)) $errors[] = "Year is required";
    if (empty($payment_date)) $errors[] = "Payment date is required";
    if (empty($payment_method)) $errors[] = "Payment method is required";
    
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO mess_payments (student_id, amount, month, year, payment_date, payment_method, transaction_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("idsissss", $student_id, $amount, $month, $year, $payment_date, $payment_method, $transaction_id, $status);
        
        if ($stmt->execute()) {
            setMessage("Mess payment added successfully!", "success");
        } else {
            setMessage("Failed to add mess payment", "danger");
        }
    } else {
        $_SESSION['message'] = implode("<br>", $errors);
        $_SESSION['message_type'] = 'danger';
    }
}

redirect('mess_payments.php');
