<?php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "college";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch complaints data for all committees
$sql = "SELECT 
            CASE
                WHEN complaint_number LIKE '%EXC%' THEN 'Examination Committee'
                WHEN complaint_number LIKE '%ICC%' THEN 'Internal Complaint Committee'
                WHEN complaint_number LIKE '%GGC%' THEN 'Grievance Redressal Committee'
                ELSE 'Unknown'
            END AS committee,
            SUM(CASE WHEN status = 'Posted' THEN 1 ELSE 0 END) AS posted,
            SUM(CASE WHEN status = 'Acknowledged' THEN 1 ELSE 0 END) AS acknowledged,
            SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) AS closed
        FROM assignedcomplaints
        GROUP BY committee";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error in SQL query: " . mysqli_error($conn));
}

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);

mysqli_close($conn);
?>
