<?php
include('db.php');
include('function.php');
 
if(isset($_POST["member_id"]))
{
    $statement = $connection->prepare(
        "DELETE FROM users3 WHERE id = :id"
    );
    $result = $statement->execute(
 
        array(':id' =>   $_POST["member_id"])
         
        );
}
?>