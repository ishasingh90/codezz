 
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

// Handle form submission for leaving a student
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get student ID from POST
    $student_id = $_POST['student_id'];

    // Update student to mark them as leaved
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

// Fetch all students who are currently living and not marked as leaved
$sql = "SELECT student_id, name FROM students WHERE living_status = 'Living' AND is_leaved = 0";
$result = $conn->query($sql);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Student - Hostel Room Management</title>
    <link rel="stylesheet" type="text/css" href="css/DJ.css">
</head>
<body>
    <div class="container">
        <h2>Leave Student</h2>
        <form method="POST" action="leave_student.php">
            <label for="student_id">Select Student:</label>
            <select name="student_id" required>
                <?php
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
