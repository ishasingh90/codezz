manage roromm
<h2>Manage Room</h2>
<form method="post" action="manage_room.php">
    <label for="room-number">Room Number:</label>
    <input type="text" id="room-number" name="room_number" required>
    
    <label for="activate">Activate:</label>
    <select id="activate" name="activate">
        <option value="1">Yes</option>
        <option value="0">No</option>
    </select>
    
    <button type="submit" name="action" value="add">Add Room</button>
</form>

<h3>Update/Delete Room</h3>
<form method="post" action="update_delete_room.php">
    <label for="room-search">Search Room Number:</label>
    <input type="text" id="room-search" name="room_search" required>
    <button type="submit" name="action" value="search">Search</button>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_number = $_POST['room_number'];
    $activate = $_POST['activate'];

    if ($_POST['action'] == 'add') {
        $sql = "INSERT INTO rooms (room_number, available) VALUES ('$room_number', $activate)";
        if ($conn->query($sql) === TRUE) {
            echo "Room added successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>


session start 
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
