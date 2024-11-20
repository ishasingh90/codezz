<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "rishikesh@astha";
$dbname = "hostel_room_management";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $student_id = $_POST['student_id'];
    $amount = $_POST['amount'];
    $mobile_number = $_POST['mobile_number'];
    $payment_date = date("Y-m-d");
    $total_fees = 50000; // Fixed total fees

    // Check if the student already has a record in the student_fees table
    $check_sql = "SELECT fees_paid FROM student_fees WHERE student_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $student_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Student exists, update the fees_paid
        $check_stmt->bind_result($existing_fees_paid);
        $check_stmt->fetch();

        // Add the new payment to the existing fees
        $updated_fees_paid = $existing_fees_paid + $amount;

        $update_sql = "UPDATE student_fees SET fees_paid = ?, payment_date = ?, mobile_number = ? WHERE student_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("dssi", $updated_fees_paid, $payment_date, $mobile_number, $student_id);

        if ($update_stmt->execute()) {
            echo "Fees updated successfully!";
        } else {
            echo "Error: " . $update_stmt->error;
        }

        $update_stmt->close();
    } else {
        // Student doesn't exist in student_fees table, insert new row
        $insert_sql = "INSERT INTO student_fees (student_id, amount, payment_date, total_fees, fees_paid, mobile_number)
                       VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("issdds", $student_id, $amount, $payment_date, $total_fees, $amount, $mobile_number);

        if ($insert_stmt->execute()) {
            echo "Fees added successfully!";
        } else {
            echo "Error: " . $insert_stmt->error;
        }

        $insert_stmt->close();
    }

    $check_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Fees - Hostel Room Management</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Student Fees</h2>
        <form method="post" action="student_fees.php">
            <label for="student-id">Student ID:</label>
            <input type="text" id="student-id" name="student_id" required>
            
            <label for="amount">Amount:</label>
            <input type="text" id="amount" name="amount" required>
            
            <label for="mobile-number">Mobile Number:</label>
            <input type="text" id="mobile-number" name="mobile_number" required>
            
            <button type="submit" name="submit">Submit</button>
        </form>
    </div>
</body>
</html>
