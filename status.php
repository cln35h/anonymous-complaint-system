<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "college";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the complaint number from the form
    $txtI = $_POST["txtI"];

    // Prepare and execute SQL query to retrieve complaint details
    $sql = "SELECT * FROM assignedcomplaints WHERE complaint_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $txtI);
    $stmt->execute();

    $result = $stmt->get_result();

    // Check if any rows are returned
    if ($result->num_rows > 0) {
        // Output data of the row
        $row = $result->fetch_assoc();
        $complaint_number = $row["complaint_number"];
        $status = $row["status"];
        $remarks1 = $row["remarks1"];
    } else {
        $error_message = "Complaint not found";
    }

    // Close statement
    $stmt->close();
    $conn->close();
    
}
?>
<html>
    <head>

    </head>
    <?php
    include 'header.php';
    ?>
<div class="flest" style="max-width: fit-content; border: 1px solid black; margin: 0 auto">
<form  method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <?php if (!isset($complaint_number) && !isset($error_message)): ?>
    <table border="1">
        <tbody>
            <tr>
                <th scope="row">Enter Complaint Number:</th>
                <td><input type="text" name="txtI"></td>
            </tr>
            <tr>
                <th scope="row" colspan="2"><input type="submit" value="Submit"></th>
            </tr>
        </tbody>
    </table>
    <?php endif; ?>

    <?php if (isset($complaint_number)): ?>
    <table border="1">
        <tbody>
            <tr>
                <th scope="row">Entered Complaint Number:</th>
                <td><?php echo $complaint_number; ?></td>
            </tr>
            <tr>
                <th scope="row">Status:</th>
                <td><?php echo $status; ?></td>
            </tr>
            <tr>
                <th scope="row">Remarks:</th>
                <td><?php echo $remarks1; ?></td>
            </tr>
        </tbody>
    </table>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
    <table border="1">
        <tbody>
            <tr>
                <th scope="row">Enter Complaint Number:</th>
                <td><input type="text" name="txtI"></td>
            </tr>
            <tr>
                <div class="submit-btn-container">
                <th scope="row" colspan="2"><input type="submit" value="Submit"></th>
                </div>
            </tr>
            <tr style="color: red;">
                <th scope="row" colspan="2"><?php echo $error_message; ?></th>
            </tr>
        </tbody>
    </table>
    <?php endif; ?>
</form>

</div>
</html>