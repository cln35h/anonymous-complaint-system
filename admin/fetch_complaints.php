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

// Fetching the selected committee from the query string
$committee = $_GET['committee'];

// Extracting committee information from the complaint number
$committeeAbbreviation = strtoupper($committee); // Assuming committee abbreviation is passed in uppercase
$sql = "SELECT complaint_number, status FROM assignedcomplaints WHERE complaint_number LIKE '%$committeeAbbreviation%'";

// Executing the SQL query
$result = mysqli_query($conn, $sql);

// Checking if the query was successful
if ($result) {
    $complaints = array();

    // Fetching data and adding it to the complaints array
    while ($row = mysqli_fetch_assoc($result)) {
        $complaints[] = $row;
    }

    // Sending the complaints data as JSON response
    header('Content-Type: application/json');
    echo json_encode($complaints);
} else {
    // If the query fails, returning an empty array
    echo json_encode(array());
}

// Closing the database connection
mysqli_close($conn);
?>
