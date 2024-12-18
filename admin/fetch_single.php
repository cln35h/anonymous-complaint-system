<?php
include('db.php');

function updateRandomPassword($length = 8) {
    $characters = '23456789abcdefghjkmnopqrstuvwxyz';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}

if(isset($_POST["member_id"])) {
    $output = array();
    $statement = $connection->prepare(
        "SELECT * FROM users3 WHERE id = :member_id LIMIT 1"
    );
    $statement->execute(array(':member_id' => $_POST["member_id"]));
    $result = $statement->fetchAll();
    foreach($result as $row) {
        $output["id"] = $row["id"];
        $output["name"] = $row["name"];
        $output["email"] = $row["email"];
        $output["role"] = $row["role"];
        $output["committee"] = $row["committee"];
    }
    
    
    // If the update password button is clicked
    if(isset($_POST["update_password"])) {
        // Generate a new random password
        $new_password = updateRandomPassword();
        
        // Update the user's password in the database
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
        $statement = $connection->prepare(
            "UPDATE users3 SET password = :password WHERE id = :member_id"
        );
        $statement->execute(array(
            ':password' => $hashedPassword,
            ':member_id' => $_POST["member_id"]
        ));
        
        // Include the new password in the output
        echo json_encode(array('email' => $output["email"], 'password' => $new_password));
        exit; // Exit to prevent further output
    }
    
    echo json_encode($output);
}
?>
