<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include 'includes/db_connect.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Hostel Room Management</title>
    <link rel="stylesheet" type="text/css" href="css/DJ.css">
    <style>
        @font-face {
            font-family: 'Bella Stella';
            src: url('css/fonts/Bella_Stella.ttf') format('truetype');
        }

        .hostel-title {
            font-family: 'Bella Stella', sans-serif;
            font-size: 36px;
            color: #4b4b4b;
            text-align: center;
            margin-top: 20px;
            text-shadow: 2px 2px 4px #000000;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h2>Navigator</h2>
            <ul>
                <li><a href="admin_dashboard.php?section=manage_room">Manage Room</a></li>
                <li><a href="admin_dashboard.php?section=new_student">New Student</a></li>
                <li><a href="admin_dashboard.php?section=update_delete_student">Update and Delete Student</a></li>
                <li><a href="admin_dashboard.php?section=student_fees">Student Fees</a></li>
                <li><a href="admin_dashboard.php?section=all_students">All Students</a></li>
                <li><a href="admin_dashboard.php?section=leaved_students">Leaved Students</a></li>
                <li><a href="leave_student.php">Leave Student</a></li>
                <li><a href="admin_dashboard.php?section=add_facilities">Add Facilities</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="content">

            <!-- Title for Hostel -->
            <h1 class="hostel-title"> WELCOME TO SWARN JAYANTI BOYS HOSTEL 🚩🚩🚩🚩</h1>

            <!-- Main Dashboard Title -->
            <h1 style="margin-top: 20px;">Admin Dashboard</h1>

            <!-- Fetch and display facilities -->
            <div class="facilities">
                <h3>Hostel Facilities</h3>
                <ul>
                    <?php
                    $sql = "SELECT facility_name, description FROM facilities";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<li><strong>" . htmlspecialchars($row['facility_name']) . ":</strong> " . htmlspecialchars($row['description']) . "</li>";
                        }
                    } else {
                        echo "<li>No facilities found.</li>";
                    }
                    ?>
                </ul>
            </div>

            <!-- Section handling -->
            <?php
            if (isset($_GET['section'])) {
                $section = basename($_GET['section']);
                $section_file = 'C:/Apache24/htdocs/hostel_management/' . $section . '.php';
                
                if (file_exists($section_file)) {
                    include $section_file;
                } else {
                    echo "<p style='color: red;'>Sorry, the requested section <strong>" . htmlspecialchars($section) . "</strong> does not exist or cannot be found.</p>";
                }
            } else {
                echo "<p>Welcome to the Admin Dashboard. Use the sidebar to manage the hostel.</p>";
            }

            // Close the database connection if it's not already closed
        #    if ($conn) {
          #      $conn->close();
          #  }
            ?>
        </div>
    </div>
</body>
</html>
