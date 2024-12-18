<?php
// Start session
session_start();

include('db.php');

// Function to fetch total records based on committee
function get_total_all_records($committee) {
    global $connection;
    $statement = $connection->prepare("SELECT * FROM complaint2 WHERE committee = ?");
    $statement->execute([$committee]);
    $result = $statement->fetchAll();
    return $statement->rowCount();
}

// Check if specific user from committee conveners is logged in
if(isset($_SESSION["committee"])) {
    $total_records = get_total_all_records($_SESSION["committee"]);
} else {
    // Handle the scenario where the committee is not set
    $total_records = 0; // Default value for total records
}

$query = '';
$output = array();
$query .= "SELECT c.*, a.status FROM complaint2 c LEFT JOIN assignedcomplaints a ON c.complaint_number = a.complaint_number WHERE c.committee = ?";

// Add search condition if search value is provided
if(isset($_POST["search"]["value"])) {
    $query .= " AND c.complaint_number LIKE '%" . $_POST["search"]["value"] . "%' ";
}

// Order complaints by status: Posted -> Acknowledged -> Closed
$query .= ' ORDER BY FIELD(a.status, "Posted", "Acknowledged", "Closed"), c.timestamp ASC';

// Apply pagination
if($_POST["length"] != -1) {
    $query .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connection->prepare($query);
$statement->execute([$_SESSION["committee"]]); // Pass committee from session
$result = $statement->fetchAll();
$data = array();
$filtered_rows = $statement->rowCount();

foreach($result as $row) {
    $sub_array = array();
     
    $sub_array[] = $row["id"];
    $sub_array[] = $row["year"];
    $sub_array[] = $row["complaint_number"];
    $sub_array[] = $row["incdnt_dscrptin"];
    $sub_array[] = $row["indiv_inv"];
    $sub_array[] = '<button type="button" name="update" id="'.$row["id"].'" class="btn btn-primary btn-sm update"><i class="glyphicon glyphicon-pencil"> </i>Add Remarks</button></button>';
    
    // Fetch status from the database based on complaint number
    $status = fetch_status_from_database($row["complaint_number"]); // Use complaint_number instead of id
    
    // Display status as a colored div
    $status_html = '<div class="status-div" style="background-color: '.$status['color'].'; text-align: center;">'.$status['text'].'</div>';
    $sub_array[] = $status_html;
    
    $data[] = $sub_array;
}

$output = array(
    "draw"              =>   intval($_POST["draw"]),
    "recordsTotal"      =>   $filtered_rows,
    "recordsFiltered"   =>   $total_records, // Use the value obtained from get_total_all_records() function
    "data"              =>   $data
);

echo json_encode($output);

// Function to fetch status from the database based on complaint ID
function fetch_status_from_database($complaint_number) {
    global $connection;

    // Prepare and execute query
    $statement = $connection->prepare("SELECT status FROM assignedcomplaints WHERE complaint_number = :complaint_number");
    $statement->bindParam(':complaint_number', $complaint_number);
    $statement->execute();

    // Check for errors
    if ($statement->errorCode() != '00000') {
        $errorInfo = $statement->errorInfo();
        echo "Error executing query: " . $errorInfo[2]; // Output the error message
        return array('color' => 'gray', 'text' => 'Unknown'); // Return default status
    }

    // Fetch the status
    $status = $statement->fetch(PDO::FETCH_ASSOC);

    // If status is found
    if ($status) {
        // Determine color and text based on status
        switch ($status['status']) {
            case 'Posted':
                $result = array('color' => '#9ad0f5', 'text' => 'Posted');
                break;
            case 'Acknowledged':
                $result = array('color' => '#ffb1c1', 'text' => 'Acknowledged');
                break;
            case 'Closed':
                $result = array('color' => '#ffe6aa', 'text' => 'Closed');
                break;
            default:
                $result = array('color' => 'gray', 'text' => 'Unknown');
                break;
        }
    } else {
        // If no status found, assume default
        $result = array('color' => 'red', 'text' => 'Unknown');
    }

    return $result;
}

?>

