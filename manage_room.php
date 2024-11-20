<?php
// manage_room.php

// Ensure session is only started if it hasn't been already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
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

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle room booking logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'book') {
    // Fetch logged-in student ID from session
    $student_id = $_POST['student_id'] ?? null;
    $room_number = $_POST['room_number'];
    $booking_date = date("Y-m-d H:i:s");

    // Check if the room is already booked
    $check_sql = "SELECT * FROM room_bookings WHERE room_number = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $room_number);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows == 0) {  // Room is not booked yet
        // Update room status to 'Booked' (1 for Booked)
        $update_sql = "UPDATE rooms SET available = 1 WHERE room_number = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("s", $room_number);
        
        if ($update_stmt->execute()) {
            // Insert booking details into room_bookings table
            $booking_sql = "INSERT INTO room_bookings (student_id, room_number, booking_date) VALUES (?, ?, ?)";
            $booking_stmt = $conn->prepare($booking_sql);
            $booking_stmt->bind_param("iss", $student_id, $room_number, $booking_date);
            $booking_stmt->execute();

            echo "Room booked successfully!";
        } else {
            echo "Error: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        echo "Room is already booked!";
    }
    $check_stmt->close();
}

// Handle adding, updating, deleting rooms
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'add') {
        $room_number = $_POST['room_number'];
        $room_type = $_POST['room_type'];
        $available = $_POST['activate'] == '1' ? 0 : 1; // 1 for "Booked", 0 for "Not Booked"

        $sql = "INSERT INTO rooms (room_number, room_type, available) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $room_number, $room_type, $available);

        if ($stmt->execute()) {
            echo "Room added successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    if ($action == 'update') {
        $room_number = $_POST['room_number'];
        $room_type = $_POST['room_type'];
        $available = $_POST['activate'] == '1' ? 0 : 1; // 1 for "Booked", 0 for "Not Booked"

        $sql = "UPDATE rooms SET room_type = ?, available = ? WHERE room_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $room_type, $available, $room_number);

        if ($stmt->execute()) {
            echo "Room updated successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    if ($action == 'delete') {
        $room_number = $_POST['room_number'];

        $sql = "DELETE FROM rooms WHERE room_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $room_number);

        if ($stmt->execute()) {
            echo "Room deleted successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch all rooms to display in the table
$sql = "SELECT * FROM rooms";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Room</title>
</head>
<body>
    <h2>Manage Room</h2>
    
    <!-- Form to add room -->
    <form method="post" action="manage_room.php">
        <label for="room-number">Room Number:</label>
        <input type="text" id="room-number" name="room_number" required>

        <label for="room-type">Room Type:</label>
        <input type="text" id="room-type" name="room_type" required>

        <label for="activate">Activate:</label>
        <select id="activate" name="activate">
            <option value="1">Not Booked</option>
            <option value="0">Booked</option>
        </select>

        <button type="submit" name="action" value="add">Add Room</button>
    </form>

    <!-- Form to update/delete room -->
    <h3>Update/Delete Room</h3>
    <form method="post" action="manage_room.php">
        <label for="room-search">Search Room Number:</label>
        <input type="text" id="room-search" name="room_search" required>
        <button type="submit" name="action" value="search">Search</button>
    </form>

    <!-- Display all rooms -->
    <h3>All Rooms</h3>
    <table border="1">
        <tr>
            <th>Room Number</th>
            <th>Room Type</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['room_number']; ?></td>
                <td><?php echo $row['room_type']; ?></td>
                <td><?php echo $row['available'] == 1 ? 'Booked' : 'Not Booked'; ?></td>
                <td>
                    <form method="post" action="manage_room.php" style="display:inline;">
                        <input type="hidden" name="room_number" value="<?php echo $row['room_number']; ?>">
                        <button type="submit" name="action" value="book">Book</button>
                    </form>
                    <form method="post" action="manage_room.php" style="display:inline;">
                        <input type="hidden" name="room_number" value="<?php echo $row['room_number']; ?>">
                        <button type="submit" name="action" value="edit">Edit</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

    <?php $conn->close(); ?>
</body>
</html>
