<?php
include('db.php');
include('function.php');

$query = '';
$output = array();
$query .= "SELECT id, name, email, role, committee FROM users3 "; // Include 'role' and 'committee' columns in the query

if(isset($_POST["search"]["value"])) {
    $query .= 'WHERE name LIKE "%'.$_POST["search"]["value"].'%" ';
    $query .= 'OR email LIKE "%'.$_POST["search"]["value"].'%" ';
}

if(isset($_POST["order"])) {
    $query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
} else {
    $query .= 'ORDER BY id ASC ';
}

if($_POST["length"] != -1) {
    $query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connection->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$data = array();
$filtered_rows = $statement->rowCount();

// Mapping array for abbreviations to full names
$role_mapping = array(
    'admin' => 'Admin',
    'cm' => 'Committee Member',
    'cc' => 'Committee Convener',
    'icc' => 'Internal Complaint Committee',
    'ggc' => 'Grivernace Redressal Committee',
    'exc' => 'Examination Committee'
);

// Mapping array for committee abbreviations to full names
$committee_mapping = array(
    'exc' => 'Examination Committee',
    'icc' => 'Internal Complaint Committee',
    'ggc' => 'Grivernace Redressal Committee',
);

foreach($result as $row) {
    $sub_array = array();
    $sub_array[] = $row["id"];
    $sub_array[] = $row["name"];
    $sub_array[] = $row["email"];
    
    // Convert role abbreviation to full name using mapping array
    $role = isset($role_mapping[$row["role"]]) ? $role_mapping[$row["role"]] : $row["role"];
    // Convert committee abbreviation to full name using mapping array
    $committee = isset($committee_mapping[$row["committee"]]) ? $committee_mapping[$row["committee"]] : $row["committee"];

    $sub_array[] = $role;
    $sub_array[] = $committee;
    $sub_array[] = '<button type="button" name="update" id="'.$row["id"].'" class="btn btn-primary btn-sm update"><i class="glyphicon glyphicon-pencil"></i>Edit</button>'; // Removed extra closing button tag
    $sub_array[] = '<button type="button" name="delete" id="'.$row["id"].'" class="btn btn-danger btn-sm delete">Delete</button>';
    $data[] = $sub_array;
}


$output = array(
    "draw"              =>   intval($_POST["draw"]),
    "recordsTotal"      =>   $filtered_rows,
    "recordsFiltered"   =>   get_total_all_records(),
    "data"              =>   $data
);

echo json_encode($output);

?>
