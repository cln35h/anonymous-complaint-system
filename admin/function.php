<?php
$connection = new PDO('mysql:host=localhost;dbname=college', 'root', '');


function RandomPassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}


function get_total_all_records()
{
    include('db.php');
    $statement = $connection->prepare("SELECT * FROM users3");
    $statement->execute();
    $result = $statement->fetchAll();
    return $statement->rowCount();
}


if (isset($_POST["operation"])) {

    if ($_POST["operation"] == "Add") {
        $role = $_POST["role"];
        $committee = ($role === "admin") ? null : $_POST["committee"];
    
        // Generate random password
        $password = RandomPassword(); 

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute the SQL statement to insert user data
        $statement = $connection->prepare("
            INSERT INTO users3 (name, email, role, committee, password) VALUES (:name, :email, :role, :committee, :password)");
        $result = $statement->execute(
            array(
                ':name' => $_POST["name"],
                ':email' => $_POST["email"],
                ':role' => $role,
                ':committee' => $committee,
                ':password' => $hashedPassword
            )
        );

        // Check if insertion was successful
    echo json_encode(array('email' => $_POST["email"], 'password' => $password));
    }


    if ($_POST["operation"] == "Edit") {
    $role = $_POST["role"];
    $committee = ($role === "admin") ? null : $_POST["committee"];
    
    // If role is 'admin', reset committee to null
    if ($role === "admin") {
        $committee = null;
    }
    
    $statement = $connection->prepare("
        UPDATE users3
        SET name = :name, email = :email, role = :role, committee = :committee WHERE id = :id");
    $result = $statement->execute(
        array(
            ':name' => $_POST["name"],
            ':email' => $_POST["email"],
            ':role' => $_POST["role"],
            ':committee' => $committee, // Use the modified $committee value
            ':id' => $_POST["member_id"]
        )
    );
}

}

?>