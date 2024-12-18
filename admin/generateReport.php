<?php

// Database configuration
$dbHost = "localhost";
$dbName = "college";
$dbUser = "root";
$dbPass = "";



// Establish database connection
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

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
        WHERE c.timestamp BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";

// If a specific committee is selected, add a condition to filter by that committee
if ($committee != 'all') {
    $sql .= " AND c.committee LIKE '%$committee%'";
}

// If a specific status is selected, add a condition to filter by that status
if ($status != 'all') {
    $sql .= " AND a.status = '$status'";
}

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error executing query: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0) {
    echo "<table class='table table-stripped'>";
    echo "<tr><th>Serial No.</th><th>Complaint Number</th><th>Committee Name</th><th>Timestamp</th><th>Status</th></tr>";
    $serialNumber = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>".$serialNumber."</td>";
        echo "<td>".$row["complaint_number"]."</td>";
        // Display full name of the committee
        echo "<td>".$committeeNames[$row["committee"]]."</td>";
        echo "<td>".$row["timestamp"]."</td>";
        echo "<td>".$row["status"]."</td>";
        echo "</tr>";
        $serialNumber++;
    }
    echo "</table>";
} else {
    echo "No results found.";
}

mysqli_close($conn);
?>
