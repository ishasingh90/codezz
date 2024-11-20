<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        // Example query to update a student's status back to 'Living'
        // This can be customized based on specific needs
        $sql = "UPDATE students SET living_status = 'Living' WHERE mobile_number = 'student_mobile_number'";
        
        if ($conn->query($sql) === TRUE) {
            echo "Database updated successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>
