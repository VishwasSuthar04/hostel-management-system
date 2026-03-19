<?php
// Display all errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PHP Test</h1>";

try {
    // Test database connection
    $conn = mysqli_connect('localhost', 'root', '');
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    echo "<p>✓ MySQL Connection: OK</p>";
    
    // Create database
    mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS hostel_management");
    mysqli_select_db($conn, "hostel_management");
    echo "<p>✓ Database: OK</p>";
    
    // Test query
    $result = mysqli_query($conn, "SELECT 1 as test");
    echo "<p>✓ Query Test: OK</p>";
    
    echo "<h2>Setup should work! Try running setup.php now.</h2>";
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>

