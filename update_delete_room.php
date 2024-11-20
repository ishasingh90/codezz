<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['search'])) {
        $mobile_number = $_POST['mobile_number'];
        $sql = "SELECT * FROM students WHERE mobile_number = '$mobile_number'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            echo "No student found with this mobile number.";
        }
    } elseif (isset($_POST['update'])) {
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

        $sql = "UPDATE students SET name='$name', father_name='$father_name', mother_name='$mother_name', email='$email', permanent_address='$permanent_address', college_name='$college_name', aadhaar_number='$aadhaar_number', room_number='$room_number', living_status='$living_status' WHERE mobile_number='$mobile_number'";

        if ($conn->query($sql) === TRUE) {
            echo "Student details updated successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['delete'])) {
        $mobile_number = $_POST['mobile_number'];
        $sql = "DELETE FROM students WHERE mobile_number='$mobile_number'";

        if ($conn->query($sql) === TRUE) {
            echo "Student deleted successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<h2>Update and Delete Student</h2>
<form method="post" action="update_delete_student.php">
    <label for="mobile-number">Mobile Number:</label>
    <input type="text" id="mobile-number" name="mobile_number" required>
    <button type="submit" name="search">Search</button>
</form>

<?php if (isset($row)) { ?>
<form method="post" action="update_delete_student.php">
    <input type="hidden" name="mobile_number" value="<?php echo $row['mobile_number']; ?>">
    
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" value="<?php echo $row['name']; ?>" required>
    
    <label for="father-name">Father's Name:</label>
    <input type="text" id="father-name" name="father_name" value="<?php echo $row['father_name']; ?>" required>
    
    <label for="mother-name">Mother's Name:</label>
    <input type="text" id="mother-name" name="mother_name" value="<?php echo $row['mother_name']; ?>" required>
    
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?php echo $row['email']; ?>" required>
    
    <label for="permanent-address">Permanent Address:</label>
    <input type="text" id="permanent-address" name="permanent_address" value="<?php echo $row['permanent_address']; ?>" required>
    
    <label for="college-name">College Name:</label>
    <input type="text" id="college-name" name="college_name" value="<?php echo $row['college_name']; ?>" required>
    
    <label for="aadhaar-number">Aadhaar Number:</label>
    <input type="text" id="aadhaar-number" name="aadhaar_number" value="<?php echo $row['aadhaar_number']; ?>" required>
    
    <label for="room-number">Room Number:</label>
    <input type="text" id="room-number" name="room_number" value="<?php echo $row['room_number']; ?>" required>
    
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

