<?php


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

// Fetch complaints data for all committees within the selected date range
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';
$committee = isset($_GET['committee']) ? $_GET['committee'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Mapping of committee abbreviations to full names
$committeeNames = array(
    'exc' => 'Examination Committee',
    'icc' => 'Internal Complaint Committee',
    'ggc' => 'Grievance Redressal Committee'
);

// Constructing the SQL query
$sql = "SELECT c.complaint_number, c.committee, c.timestamp, a.status
        FROM complaint2 c
        INNER JOIN assignedcomplaints a ON c.complaint_number = a.complaint_number
        WHERE c.timestamp BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
        AND c.committee = '$committee'";


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
