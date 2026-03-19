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
    
    // Handle file upload
    $payment_proof = null;
    if (!empty($_FILES['payment_proof']['name'])) {
        $upload = uploadFile($_FILES['payment_proof'], 'fee_');
        if ($upload['success']) {
            $payment_proof = $upload['filename'];
        } else {
            $errors[] = $upload['message'];
        }
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO fees (student_id, amount, month, year, payment_date, payment_method, transaction_id, payment_proof, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("idsisssss", $student_id, $amount, $month, $year, $payment_date, $payment_method, $transaction_id, $payment_proof, $status);
        
        if ($stmt->execute()) {
            setMessage("Fee added successfully!", "success");
        } else {
            setMessage("Failed to add fee", "danger");
        }
    } else {
        $_SESSION['message'] = implode("<br>", $errors);
        $_SESSION['message_type'] = 'danger';
    }
}

redirect('fees.php');
