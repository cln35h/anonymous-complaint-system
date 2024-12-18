<?php
session_start();

// Create MySQL connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "college";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function handleFileUpload($fieldName, $complaintNumber){
    if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES[$fieldName]['tmp_name'];
        $fileName = $_FILES[$fieldName]['name'];
        $fileType = $_FILES[$fieldName]['type'];

        // Generate a unique file name by concatenating the complaint number
        $fileNameWithComplaintNumber = "{$complaintNumber}_{$fileName}";

        // Choose the destination folder based on the file type
        $destinationFolder = '';
        if (strpos($fileType, 'image/') === 0) {
            $destinationFolder = 'uploads/images/';
        } elseif (strpos($fileType, 'video/') === 0) {
            $destinationFolder = 'uploads/videos/';
        } elseif (strpos($fileType, 'audio/') === 0) {
            $destinationFolder = 'uploads/audios/';
        } else {
            $destinationFolder = 'uploads/documents/';
        }

        // Create the destination folder if it doesn't exist
        if (!file_exists($destinationFolder)) {
            mkdir($destinationFolder, 0755, true);
        }

        $fileDestination = $destinationFolder . $fileNameWithComplaintNumber;

        if (move_uploaded_file($fileTmpPath, $fileDestination)) {
            return $fileDestination;
        } else {
            // Return an error message if file upload fails
            return "Error uploading $fieldName.";
        }
    }
    return null;
}


function verifyCaptcha($userInput) {
    if (isset($_SESSION['captcha']) && !empty($userInput)) {
        $storedCaptcha = $_SESSION['captcha'];
        return $userInput === $storedCaptcha;
    }
    return false;
}

function generateComplaintNumber($dept, $committee) {
    $currentYear = date('Y');
    $randomDigits = str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
    $complaintNumber = strtoupper("$dept/$committee/$currentYear/$randomDigits");
    // Sanitize the complaint number to remove any special characters
    $sanitizedComplaintNumber = preg_replace('/[^a-zA-Z0-9_]/', '_', $complaintNumber);
    return $sanitizedComplaintNumber;
}

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize user input here (implement your validation logic)
    $dept = isset($_POST["dept"]) ? sanitizeInput($_POST["dept"]) : "";
    $year = isset($_POST["year"]) ? sanitizeInput($_POST["year"]) : "";
    $committee = isset($_POST["cmite"]) ? sanitizeInput($_POST["cmite"]) : "";
    $incdnt_dscrptin = isset($_POST["incdnt_dscrptin"]) ? sanitizeInput($_POST["incdnt_dscrptin"]) : "";
    $indiv_inv = isset($_POST["indiv_inv"]) ? sanitizeInput($_POST["indiv_inv"]) : "";
    $date = isset($_POST["date"]) ? sanitizeInput($_POST["date"]) : "";
    $time = isset($_POST["time"]) ? sanitizeInput($_POST["time"]) : "";
    $location = isset($_POST["location"]) ? sanitizeInput($_POST["location"]) : "";
    $add_dtls = isset($_POST["add_dtls"]) ? sanitizeInput($_POST["add_dtls"]) : "";
    
     // Concatenate room number and coordinates based on user selection
    if ($location === "inside") {
        $room_no = isset($_POST["room"]) ? sanitizeInput($_POST["room"]) : ""; // Assuming room number is selected
        $location_value = $room_no; // Store room number as location value for inside location
    } elseif ($location === "outside") {
        $latitude = isset($_POST["latitude"]) ? sanitizeInput($_POST["latitude"]) : "";
        $longitude = isset($_POST["longitude"]) ? sanitizeInput($_POST["longitude"]) : "";
        $location_value = $latitude . ', ' . $longitude; // Combine latitude and longitude as location value for outside location
    }

    // If location value is still empty, display an error message (you can customize this as needed)
    if (empty($location_value)) {
        $response = array('success' => false, 'message' => 'Location value is required.');
        echo json_encode($response);
        exit; // Stop further execution
    }

    
    // CAPTCHA verification
    $captchaValue = isset($_POST['captcha']) ? $_POST['captcha'] : '';
    if (!verifyCaptcha($captchaValue)) {
        // CAPTCHA verification failed
        $response = array('success' => false, 'message' => 'Incorrect CAPTCHA! Please try again.');
        
        echo json_encode($response);
        unset($_SESSION['captcha']);
        exit; // Stop further execution
    }

    // Generate complaint number
    $complaint_number = generateComplaintNumber($dept, $committee);

    // Handle file uploads and get the file paths
   $file_upload = handleFileUpload('file_upload', $complaint_number);


   $location_value = "";

    // Concatenate room number and coordinates based on user selection
if ($location === "inside") {
    $room_no = isset($_POST["room"]) ? sanitizeInput($_POST["room"]) : ""; // Assuming room number is selected
    $location_value = $room_no; // Store room number as location value for inside location
} elseif ($location === "outside") {
    $latitude = isset($_POST["latitude"]) ? sanitizeInput($_POST["latitude"]) : "";
    $longitude = isset($_POST["longitude"]) ? sanitizeInput($_POST["longitude"]) : "";
    
    
    
    $location_value = $latitude . ', ' . $longitude; // Combine latitude and longitude as location value for outside location
}


    // If location value is still empty, display an error message (you can customize this as needed)
    if (empty($location_value)) {
        $response = array('success' => false, 'message' => 'Location value is required.');
        echo json_encode($response);
        exit; // Stop further execution
    }

    // Prepare and execute the SQL statement using prepared statements
    $stmt = $conn->prepare("INSERT INTO complaint2 (dept, year, committee, incdnt_dscrptin, indiv_inv, date, time, location, add_dtls, file_upload, complaint_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param(
            "sssssssssss",
            $dept,
            $year,
            $committee,
            $incdnt_dscrptin,
            $indiv_inv,
            $date,
            $time,
            $location_value,
            $add_dtls,
            $file_upload,
            $complaint_number
        );

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();


            header('Content-Type: application/json');
            // Show success message as a JSON response
            $response = array('success' => true, 'message' => 'Complaint submitted successfully!', 'complaintNumber' => $complaint_number);
            echo json_encode($response);
            exit; // Stop further execution
        } else {
            // Show error message as a JSON response
            $response = array('success' => false, 'message' => 'Error executing query: ' . $stmt->error);
            echo json_encode($response);
            exit; // Stop further execution
        }
    } else {
        // Show error message as a JSON response
        $response = array('success' => false, 'message' => 'Error preparing statement: ' . $conn->error);
        echo json_encode($response);
        exit; // Stop further execution
    }
}
