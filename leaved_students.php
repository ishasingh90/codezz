<?php
 
include 'includes/db_connect.php';  

 
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Leaved Students - Hostel Room Management</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h2>Leaved Students</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Mobile Number</th>
                <th>Father's Name</th>
                <th>Mother's Name</th>
                <th>Email</th>
                <th>Permanent Address</th>
                <th>College Name</th>
                <th>Aadhaar Number</th>
                <th>Room Number</th>
                <th>Living Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
           
            $sql = "SELECT * FROM students WHERE is_leaved = 1"; // Adjusted query
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                     
                    $living_status = ($row['is_leaved'] == 1) ? 'Leaved' : 'Living';

                    echo "<tr>
                        <td>" . $row['name'] . "</td>
                        <td>" . $row['mobile_number'] . "</td>
                        <td>" . $row['father_name'] . "</td>
                        <td>" . $row['mother_name'] . "</td>
                        <td>" . $row['email'] . "</td>
                        <td>" . $row['permanent_address'] . "</td>
                        <td>" . $row['college_name'] . "</td>
                        <td>" . $row['aadhaar_number'] . "</td>
                        <td>" . $row['room_number'] . "</td>
                        <td>" . $living_status . "</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No leaved students found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
