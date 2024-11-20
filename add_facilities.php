<?php
// Include database connection
include 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $facility_name = $_POST['facility_name'];
    $description = $_POST['description'];

    // Insert the facility into the database
    $sql = "INSERT INTO facilities (facility_name, description) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $facility_name, $description);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Facility added successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Facilities</title>
    <link rel="stylesheet" type="text/css" href="css/DJ.css">
</head>
<body>
    <h2>Add New Facility</h2>
    <form method="POST" action="">
        <label for="facility_name">Facility Name:</label>
        <input type="text" id="facility_name" name="facility_name" required><br><br>
        
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea><br><br>
        
        <input type="submit" value="Add Facility">
    </form>
</body>
</html>
