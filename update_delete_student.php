 
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

// Include database connection
include 'includes/db_connect.php'; // Ensure this path is correct

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$row = null; // Initialize $row for later use

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['search'])) {
        // Search for student by mobile number
        $mobile_number = $_POST['mobile_number'];

        $sql = "SELECT * FROM students WHERE mobile_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $mobile_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            echo "<p>No student found with this mobile number.</p>";
        }

        $stmt->close();
    } elseif (isset($_POST['update'])) {
        // Update student details
        $mobile_number = $_POST['mobile_number'];
        $name = $_POST['name'];
        $father_name = $_POST['father_name'];
        $mother_name = $_POST['mother_name'];
        $email = $_POST['email'];
        $permanent_address = $_POST['permanent_address'];
        $college_name = $_POST['college_name'];
        $aadhaar_number = $_POST['aadhaar_number'];
        $room_number = $_POST['room_number'];
        $living_status = $_POST['living_status'];

        $sql = "UPDATE students 
                SET name=?, father_name=?, mother_name=?, email=?, permanent_address=?, college_name=?, aadhaar_number=?, room_number=?, living_status=? 
                WHERE mobile_number=?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssss", $name, $father_name, $mother_name, $email, $permanent_address, $college_name, $aadhaar_number, $room_number, $living_status, $mobile_number);

        if ($stmt->execute()) {
            echo "<p>Student details updated successfully!</p>";
        } else {
            echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>";
        }

        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        // Delete student record
        $mobile_number = $_POST['mobile_number'];

        $sql = "DELETE FROM students WHERE mobile_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $mobile_number);

        if ($stmt->execute()) {
            echo "<p>Student deleted successfully!</p>";
        } else {
            echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>";
        }

        $stmt->close();
    } elseif (isset($_POST['leave_student'])) {
        // Mark student as leaved
        $student_id = $_POST['student_id'];

        $sql = "UPDATE students SET living_status = 'Leaved', is_leaved = 1 WHERE student_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $student_id);

        if ($stmt->execute()) {
            echo "<p>Student marked as leaved successfully!</p>";
        } else {
            echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>";
        }

        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update or Delete Student - Hostel Room Management</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Update or Delete Student</h2>
        <form method="post" action="update_delete_student.php">
            <label for="mobile-number">Mobile Number:</label>
            <input type="text" id="mobile-number" name="mobile_number" required>
            <button type="submit" name="search">Search</button>
        </form>

        <?php if (isset($row)) { ?>
        <form method="post" action="update_delete_student.php">
            <input type="hidden" name="mobile_number" value="<?php echo htmlspecialchars($row['mobile_number']); ?>">

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>

            <label for="father-name">Father's Name:</label>
            <input type="text" id="father-name" name="father_name" value="<?php echo htmlspecialchars($row['father_name']); ?>" required>

            <label for="mother-name">Mother's Name:</label>
            <input type="text" id="mother-name" name="mother_name" value="<?php echo htmlspecialchars($row['mother_name']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>

            <label for="permanent-address">Permanent Address:</label>
            <input type="text" id="permanent-address" name="permanent_address" value="<?php echo htmlspecialchars($row['permanent_address']); ?>" required>

            <label for="college-name">College Name:</label>
            <input type="text" id="college-name" name="college_name" value="<?php echo htmlspecialchars($row['college_name']); ?>" required>

            <label for="aadhaar-number">Aadhaar Number:</label>
            <input type="text" id="aadhaar-number" name="aadhaar_number" value="<?php echo htmlspecialchars($row['aadhaar_number']); ?>" required>

            <label for="room-number">Room Number:</label>
            <input type="text" id="room-number" name="room_number" value="<?php echo htmlspecialchars($row['room_number']); ?>" required>

            <label for="living-status">Living Status:</label>
            <select id="living-status" name="living_status" required>
                <option value="Living" <?php echo ($row['living_status'] == 'Living') ? 'selected' : ''; ?>>Living</option>
                <option value="Leaved" <?php echo ($row['living_status'] == 'Leaved') ? 'selected' : ''; ?>>Leaved</option>
            </select>

            <button type="submit" name="update">Update</button>
            <button type="submit" name="delete">Delete</button>
            <button type="reset">Clear</button>
        </form>
        <?php } ?>

        <h2>Leave Student</h2>
        <form method="POST" action="update_delete_student.php">
            <label for="student_id">Select Student:</label>
            <select name="student_id" required>
                <?php
                // Fetch all students who are currently living and not marked as leaved
                $sql = "SELECT student_id, name FROM students WHERE living_status = 'Living' AND is_leaved = 0";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['student_id'] . "'>" . $row['name'] . "</option>";
                    }
                } else {
                    echo "<option disabled>No students available to mark as leaved</option>";
                }

                // Debugging: Print error if query fails
                if ($conn->error) {
                    echo "Error fetching students: " . $conn->error;
                }
                ?>
            </select>
            <input type="submit" name="leave_student" value="Leave Student">
        </form>
    </div>
</body>
</html>
