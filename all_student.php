<?php
 
$servername = "localhost";
$username = "root";
$password = "rishikesh@astha";
$dbname = "hostel_room_management";

$conn = new mysqli($servername, $username, $password, $dbname);
 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<h2>All Students</h2>
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
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT * FROM students";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
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
                </tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No students found.</td></tr>";
        }
        ?>
    </tbody>
</table>
