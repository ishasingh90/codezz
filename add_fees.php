<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
 if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
 
$servername = "localhost";
$username = "root";
$password = "rishikesh@astha";
$dbname = "hostel_room_management";

$conn = new mysqli($servername, $username, $password, $dbname);
 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $mobile_number = $_POST['mobile_number'];
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];
    $total_fees = 50000;  

    $student_details_sql = "SELECT name, mobile_number FROM students WHERE student_id = ?";
    $student_details_stmt = $conn->prepare($student_details_sql);
    $student_details_stmt->bind_param("i", $student_id);
    $student_details_stmt->execute();
    $student_details_result = $student_details_stmt->get_result();

    if ($student_details_result->num_rows > 0) {
        $student_row = $student_details_result->fetch_assoc();
        $student_name = $student_row['name'];
        $stored_mobile_number = $student_row['mobile_number'];
 
        if ($mobile_number !== $stored_mobile_number) {
            $message = "Error: The entered mobile number does not match the registered mobile number (" . $stored_mobile_number . "). Please enter the correct mobile number.";
        } else { 
            $check_sql = "SELECT fees_paid FROM student_fees WHERE student_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("i", $student_id);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
               
                $check_stmt->bind_result($existing_fees_paid);
                $check_stmt->fetch();

                
                $updated_fees_paid = $existing_fees_paid + $amount;

                $update_sql = "UPDATE student_fees SET fees_paid = ?, payment_date = ? WHERE student_id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("dsi", $updated_fees_paid, $payment_date, $student_id);

                if ($update_stmt->execute()) {
                    $message = "Fees updated successfully!";
                } else {
                    $message = "Error: " . $update_stmt->error;
                }
            } else {
            
                $insert_sql = "INSERT INTO student_fees (student_id, student_name, amount, payment_date, total_fees, fees_paid, mobile_number)
                               VALUES (?, ?, ?, ?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("isssdds", $student_id, $student_name, $amount, $payment_date, $total_fees, $amount, $mobile_number);

                if ($insert_stmt->execute()) {
                    $message = "Fees added successfully!";
                } else {
                    $message = "Error: " . $insert_stmt->error;
                }
            }

            $check_stmt->close();
        }
    } else {
        $message = "Error: Student ID does not exist in the system. Please add the student first.";
    }

    $student_details_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Fees - Hostel Room Management</title>
    <link rel="stylesheet" type="text/css" href="css/dashboard.css">
</head>
<body>
    <div class="container">
        <h2>Add Student Fees</h2>
        
        <?php if (!empty($message)): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
        
        <form method="post" action="add_fees.php">
            <label for="student-id">Student ID:</label>
            <input type="text" id="student-id" name="student_id" required>
            
            <label for="amount">Amount:</label>
            <input type="text" id="amount" name="amount" required>
            
            <label for="mobile-number">Mobile Number:</label>
            <input type="text" id="mobile-number" name="mobile_number" required>
            
            <label for="payment-date">Payment Date:</label>
            <input type="date" id="payment-date" name="payment_date" required>
            
            <label for="fees-paid">Fees Paid:</label>
            <input type="text" id="fees-paid" name="fees_paid" required>
            
            <button type="submit" name="submit">Submit</button>
        </form>
    </div>
</body>
</html>
