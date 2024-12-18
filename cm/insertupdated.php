<?php
include('db.php');
include('function.php');
if(isset($_POST["operation"]))
{
    if($_POST["operation"] == "Add")
    {
        $statement = $connection->prepare("
            INSERT INTO member (name, email, phone) VALUES (:name, :email, :phone)");
        $result = $statement->execute(
            array(
                ':name' =>   $_POST["name"],
                ':email'    =>   $_POST["email"],
                ':phone'    =>   $_POST["phone"]
            )
        );
    }
   elseif ($_POST["operation"] == "Edit") {
    // Check if "additional_remarks" is set in $_POST
    if (isset($_POST["additional_remarks"]) && isset($_POST["complaint_number"]) && isset($_POST["status"])) {
            // Serialize the remarks array
            $serializedRemarks = json_encode($_POST["additional_remarks"]);

            // Update the remarks and status in the database
            $statement = $connection->prepare("
                UPDATE assignedcomplaints 
                SET remarks1 = :remarks, status = :status 
                WHERE complaint_number = :complaint_number");
            $result = $statement->execute(
                array(
                    ':remarks' => $serializedRemarks,
                    ':status' => $_POST["status"], // Use the status received from the form
                    ':complaint_number' => $_POST["complaint_number"]
                )
            );

            if (!$result) {
                echo "Error: " . $statement->errorInfo()[2];
            } else {
                echo "Remarks and status updated successfully.";
            }
        } else {
            // Handle the case when "additional_remarks", "complaint_number", or "status" is not set
            echo "Additional remarks, complaint number, or status field is missing in the form submission.";
        }
    }



}
?>