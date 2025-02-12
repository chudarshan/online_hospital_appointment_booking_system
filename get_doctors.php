<?php
header("Content-Type: text/html; charset=UTF-8");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: 0");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "4semp";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch doctor details
$sql = "SELECT doctor_id, doctor_name, department, email, phone FROM Doctors";
$result = $conn->query($sql);

$doctors = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}

$conn->close();
return $doctors;
?> 