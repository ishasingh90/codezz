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

// Database connection parameters
$servername = "localhost"; 
$username = "root"; 
$password = "rishikesh@astha"; 
$dbname = "hostel_room_management"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from form
    $mobile_number = $_POST['mobile_number'];
    $name = $_POST['name'];
    $father_name = $_POST['father_name'];
    $mother_name = $_POST['mother_name'];
    $email = $_POST['email'];
    $permanent_address = $_POST['permanent_address'];
    $college_name = $_POST['college_name'];
    $aadhaar_number = $_POST['aadhaar_number'];
    $room_number = $_POST['room_number'];
    $branch = $_POST['branch'];
    $roll_number = $_POST['roll_number'];

    // Check if the room number exists in the rooms table
    $checkRoom = $conn->prepare("SELECT room_id FROM rooms WHERE room_number = ?");
    $checkRoom->bind_param("s", $room_number);
    $checkRoom->execute();
    $checkRoom->store_result();

    if ($checkRoom->num_rows > 0) {
        // Prepare SQL statement to avoid SQL injection
        $stmt = $conn->prepare("INSERT INTO students (mobile_number, name, father_name, mother_name, email, permanent_address, college_name, aadhaar_number, room_number, living_status, branch, roll_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Check if the statement was prepared successfully
        if ($stmt === false) {
            die("MySQL prepare error: " . htmlspecialchars($conn->error));
        }

        // Bind parameters (including living status)
        $living_status = 'Living'; // Default value
        $stmt->bind_param("ssssssssssss", $mobile_number, $name, $father_name, $mother_name, $email, $permanent_address, $college_name, $aadhaar_number, $room_number, $living_status, $branch, $roll_number);

        // Execute the statement
        try {
            if ($stmt->execute()) {
                echo "<p>New student added successfully!</p>";
            } else {
                throw new Exception("Execute error: " . htmlspecialchars($stmt->error));
            }
        } catch (mysqli_sql_exception $e) {
            echo "<p>SQL Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        } catch (Exception $e) {
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "<p>Error: Room number $room_number does not exist.</p>";
    }

    // Close the checkRoom statement
    $checkRoom->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student - Hostel Room Management</title>
    <link rel="stylesheet" type="text/css" href="css/DJ.css">
</head>
<body>
    <div class="container">
        <h2>Add New Student</h2>
        <form method="post" action="">
            <label for="mobile-number">Mobile Number:</label>
            <input type="text" id="mobile-number" name="mobile_number" required>
            
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="father-name">Father's Name:</label>
            <input type="text" id="father-name" name="father_name" required>
            
            <label for="mother-name">Mother's Name:</label>
            <input type="text" id="mother-name" name="mother_name" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="permanent-address">Permanent Address:</label>
            <input type="text" id="permanent-address" name="permanent_address" required>
            
            <label for="college-name">College Name:</label>
            <input type="text" id="college-name" name="college_name" required>
            
            <label for="aadhaar-number">Aadhaar Number:</label>
            <input type="text" id="aadhaar-number" name="aadhaar_number" required>
            
            <label for="room-number">Room Number:</label>
            <input type="text" id="room-number" name="room_number" required>

            <!-- New Branch and Roll Number fields -->
            <label for="branch">Branch:</label>
            <input type="text" id="branch" name="branch" required>
            
            <label for="roll-number">Roll Number:</label>
            <input type="text" id="roll-number" name="roll_number" required>

            <button type="submit">Save</button>
            <button type="reset">Clear</button>
        </form>
    </div>
</body>
</html>
