<?php
function get_total_all_records()
{
    include('db.php');
    $statement = $connection->prepare("SELECT * FROM complaint2");
    $statement->execute();
    $result = $statement->fetchAll();
    return $statement->rowCount();
}

function fetch_complaints_based_on_role_and_committee($role, $committee) {
            global $connection;

            $stmt = $connection->prepare("SELECT * FROM complaint2 WHERE role = :role AND committee = :committee");
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':committee', $committee);
            $stmt->execute();

            if ($stmt->errorCode() != '00000') {
                $errorInfo = $stmt->errorInfo();
                echo "Error executing query: " . $errorInfo[2];
                return array();
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
?>