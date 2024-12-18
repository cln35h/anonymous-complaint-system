c<?php
// Start the session
session_start();

// Include database connection file
require_once "db_connect.php";

// Function to securely hash passwords
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);

}

// Function to verify password
function verifyPassword($password, $hashedPassword) {
    return password_verify($password, $hashedPassword);

}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    // Retrieve form inputs and sanitize them

    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);
    // Prepare SQL statement to fetch user details

    $stmt = $conn->prepare("SELECT id, name, email, password, role, committee FROM users3 WHERE email = ?");

    $stmt->execute([$email]); // You can directly pass an array to execute()

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if user exists

    if ($result) {
        // Verify password

        if (verifyPassword($password, $result["password"])) {

            // Password is correct, set session variables
            $_SESSION["user_id"] = $result["id"];
            $_SESSION["name"] = $result["name"]; // Store the name in the session
            $_SESSION["email"] = $result["email"];
            $_SESSION["role"] = $result["role"];
            $_SESSION["committee"] = $result["committee"];
            // Redirect to appropriate folder based on role
            switch ($result["role"]) {
                case 'admin':
                    header("Location: /admin/index.php");
                    break;
                case 'cc':
                    header("Location: /cc/index.php");
                    break;
                case 'cm':
                    header("Location: /cm/index.php");
                    break;
                default:
                    // Handle unknown role
                    break;
            }
            exit;
        } else {
            // Password is incorrect

            $error = "Invalid email or password";
        }
        } else {
            // User does not exist
            $error = "Invalid email or password";
            // Redirect to Google.com for invalid email
            header("Location: https://www.google.com/");
            exit;
    }
}
include "header.php";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
    <title>Dinesh</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        #map {
            height: 400px;
        }
        .coordinates-container {
            margin-top: 10px;
        }
        #info {
            margin: auto; 
            width: 50%; 
        }
    </style>
</head>
<body>
    <div id="hdr">
        <div class="row navbar " style="background-color: #cddaf2;">
            <div class="col">&nbsp;</div>
            <div class="col"><a href="#" class="nav-link" id="fileComplaint">File Complaint</a></div>
            <div class="col"><a href="./status.php" class="nav-link" target="_blank" >Complaint Status</a></div>
            <div class="col"><a href="#" class="nav-link" id="complaintStatus">Login</a></div>
            <div class="col"><a href="/report/manual.php" class="nav-link" target="_blank">Manual</a></div>
        </div>
    </div>
<hr>
    <div id="info" >
        <table>
            <tr>
                <td>
                    <ol>
                        <li>This Web Portal to be used by students of <b>The SIA College</b> to file <b>Complaints/Grievances</b> online.</li>
                        <li>The fields marked <samp style="color:#FF0000"><b>*</b></samp> are mandatory while the others are optional.
                        </li>
                        <li>The text of the application may be written at the prescribed column.</li>
                        <li><b>Only alphabets A-Z a-z number 0-9 and special characters , . - _ ( ) / @ : & ? \ % </b>are allowed in Text for filing Complaints/Grievances application.</li>
                        <li><b>Do not upload/Enter Aadhar Card / PAN Card/ Mobile Number/ Email-Id/ Personal Information personal Identification.</b></li>
                        <li>Any <b>Text/Video/Image/PDF file name should not have any blank spaces</b>.</li>
                        <li>On submission of an application, a unique <b>Complaint number</b> would be issued, which may be referred by the students for references in future;<br><b>NOTEDOWN number it would be for 30 Seconds Only</b>.</li>
                        <li>The Complaints/Grievances filed through this Web Portal would reach electronically to the "Respected Teacher" of concerned Committee.</li>
                        <li>Status of the Complaint/Grievance filed online can be seen by the Student by clicking at "View Status".</li>
                    </ol>
                    <hr>
                    <input type="checkbox" id="checkbox" disabled>&nbsp; I have read and understood the above guidelines. <code style="color:#FF0000"><i>(Wait for 30 seconds and by the time read thoroughly above guidlines)</i></code><br>
                    <hr>
                    <input type="button" value="Click Here to Fill Complaint Form" id="submitButton" disabled>
                </td>
            </tr>
        </table>
    </div>
    <div class="flecomplt" style="max-width: fit-content; border: 1px solid black; margin: 0 auto">
        <h1>Complaint/Grievance Form</h1>
        <form id="complaintForm" action="complaint_handler.php" method="post" enctype="multipart/form-data" autocomplete="off">
            <table>
                <tbody>
                    <tr>
                        <th>Department: <span style="color:#FF0000"><b>*</b></span></th>
                        <td>
                            <select id="dept" onchange="populateYears()" name="dept" required >
                            <option value="">Select a Department</option>
                            <option value="baf">Bachelor Of Accounting And Finance</option>
                            <option value="bbi">Bachelor Of Commerce - Banking And Insurance</option>
                            <option value="bcom">Bachelor Of Commerce</option>
                            <option value="bscit">Bachelor Of Science - Information Technology</option>
                            <option value="bmm">Bachelor of Arts in Multimedia and Mass Communication</option>
                            <option value="bms">Bachelor Of Management Studies</option>
                            </select>
                            <br>
                            <select id="year" disabled="" name="year" required>
                            <option value="">Select a year</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Committee: <span style="color:#FF0000"><b>*</b></span></th>
                        <td>
                            <select id="cmite" name="cmite" required>
                                <option value="">Select Committee</option>
                                <option value="exc">Examination Committee</option>
                                <option value="icc">Internal Complaint Committee</option>
                                <option value="ggc">Grievance Redressal Committee</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: center" colspan="2">Details:</th>
                    </tr>
                    <tr>
                        <th>Complaint/Griverance Description: <span style="color:#FF0000"><b>*</b></span></th>
                        <td><textarea id="incident_description" name="incdnt_dscrptin" required></textarea></td>
                    </tr>
                    <tr>
                        <th>Individual involved: <span style="color:#FF0000"><b>*</b></span></th>
                        <td><input type="text" id="involved" name="indiv_inv" required></td>
                    </tr>
                    <tr>
                        <th>Date of Incident: <span style="color:#FF0000"><b>*</b></span></th>
                        <td><input type="date" id="incdnt_date" name="date" required></td>
                    </tr>
                    <tr>
                        <th>Time of Incident:</th>
                        <td><input type="time" id="incdnt_time" name="time"></td>
                    </tr>
                    <tr>
                    <th>Location of Incident: <span style="color:#FF0000"><b>*</b></span></th>
                    <td>
                        <select id="locationSelector" name="location" required onchange="showOptions()">
                            <option value="">Select Location</option>
                            <option value="inside">Inside College Campus</option>
                            <option value="outside">Outside College Campus</option>
                        </select>
                        <div id="cmap" style="display: none;">
                        <div id="map"></div>
                        <div id="coordinates" class="coordinates-container">
                            Latitude: <span id="latitude"></span>, Longitude: <span id="longitude"></span>
                            <input type="hidden" id="latitudeInput" name="latitude">
                            <input type="hidden" id="longitudeInput" name="longitude">
                        </div>
                        </div>
                        <div id="floorOptions" style="display: none;">
                            <select id="floorSelector" name="floor" onchange="showRooms()">
                                <option value="">Select Floor</option>
                                <option value="ground">Ground Floor</option>
                                <option value="first">First Floor</option>
                                <option value="second">Second Floor</option>
                                <option value="third">Third Floor</option>
                                <option value="fourth">Fourth Floor</option>
                        </select>
                        </div>
                        <div id="roomOptions" style="display: none;">
                            <select id="roomSelector" name="room">
                                <option value="">Select Room No.</option>
                                <!-- Options will be dynamically populated based on floor selection -->
                            </select>
                        </div>
                    </td>
                    </tr>
                    <tr>
                        <th>Additional Details:</th>
                        <td><textarea id="addit_dtls" name="add_dtls"></textarea></td>
                    </tr>
                    <tr>
                        <th>Upload Files (Images,Videos,Audio,PDF): <br><i>( File size shouldn't be greater then 10MB. )</i></th>
                        <td>
                            <input type="file" name="file_upload" id="file_upload" accept="image/*, video/*, audio/*, .pdf, .doc, .docx, .txt">
                            <ul id="fileList"></ul>
                        </td>
                    </tr>
                    <tr>
                        <th>CAPTCHA:<span style="color:#FF0000"><b>*</b></span>
                        <br><i>(it is HIGHLY sensitive; if DOUBT refresh CAPTCHA)</i>
                        </th>
                        <td>
                            <img src="captcha.php" alt="CAPTCHA Image" id="captchaImg"><br>
                            <a href="javascript:void(0)" onclick="refreshCaptcha()">Refresh</a><br>
                            <input type="text" id="captchaInput" name="captcha" required><br>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="submit-btn-container">
                                <input type="submit" value="Submit">
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>

    <!-- Login Form -->
    <div class="loginFormContainer">
        <div class="loginForm " style="max-width: fit-content; border: 1px solid black; margin: 0 auto">
            <?php if(isset($error)) echo "<p>$error</p>"; ?>
            <form  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <table>
                    <tbody>
                        <tr>
                            <th><label for="email">Email:</label></th>
                            <td>
                                <input type="email" id="email" name="email" required>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="password">Password:</label></th>
                        <td>
                            <input type="password" id="password" name="password" required>
                        </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="submit-btn-container">
                                    <input type="submit" value="Login">
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>





<script>

    function initializeMap() {
        var map = L.map('map').setView([19.2092927, 73.0994759], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        var selectedMarker = null;
        map.on('click', function(e) {
            if (selectedMarker) {
                map.removeLayer(selectedMarker);
            }
            var lat = e.latlng.lat.toFixed(6);
            var lng = e.latlng.lng.toFixed(6);
            selectedMarker = L.marker([lat, lng]).addTo(map);
            map.panTo([lat, lng]);
            // Update the hidden input fields with latitude and longitude values
            document.getElementById('latitude').textContent = lat;
            document.getElementById('longitude').textContent = lng;
            document.getElementById('latitudeInput').value = lat;
            document.getElementById('longitudeInput').value = lng;
            });
    }



    function showOptions() {
        var locationSelector = document.getElementById("locationSelector");
        var floorOptions = document.getElementById("floorOptions");
        var mapContainer = document.getElementById("cmap");
        var roomOptions = document.getElementById("roomOptions");
        // Reset room options
        roomOptions.style.display = "none";
        if (locationSelector.value === "inside") {
            floorOptions.style.display = "block";
            mapContainer.style.display = "none";
        } else if (locationSelector.value === "outside") {
            floorOptions.style.display = "none";
            mapContainer.style.display = "block";
            initializeMap();
        } else {
            floorOptions.style.display = "none";
            mapContainer.style.display = "none";
        }
    }

    function showRooms() {
        var floorSelector = document.getElementById("floorSelector");
        var roomOptions = document.getElementById("roomOptions");
        if (floorSelector.value !== "") {
            roomOptions.style.display = "block";
            var roomSelector = document.getElementById("roomSelector");
            roomSelector.innerHTML = "";
            if (floorSelector.value === "ground") {
                roomSelector.innerHTML += '<option value="G01">Room No. G01</option>';
                roomSelector.innerHTML += '<option value="G02">Room No. G02</option>';
                roomSelector.innerHTML += '<option value="G03">Room No. G03</option>';
                roomSelector.innerHTML += '<option value="G04">Room No. G04</option>';
                roomSelector.innerHTML += '<option value="G05">Room No. G05</option>';
                roomSelector.innerHTML += '<option value="G06">Room No. G06</option>';
            } else if (floorSelector.value === "first") {
                roomSelector.innerHTML += '<option value="BMMSR">BMM Staff Room</option>';
                roomSelector.innerHTML += '<option value="ITS">IT Staff Room</option>';
                roomSelector.innerHTML += '<option value="MgR">Management Room</option>';
                roomSelector.innerHTML += '<option value="CoSR">Common StaffRoom</option>';
                roomSelector.innerHTML += '<option value="101">Room 101</option>';
                roomSelector.innerHTML += '<option value="102">Room 102</option>';
                roomSelector.innerHTML += '<option value="103">Room 103</option>';
                roomSelector.innerHTML += '<option value="104">Room 104</option>';
                roomSelector.innerHTML += '<option value="105">Room 105</option>';
                roomSelector.innerHTML += '<option value="106">Room 106</option>';
                roomSelector.innerHTML += '<option value="107">Room 107</option>';
            } else if (floorSelector.value === "second") {
                roomSelector.innerHTML += '<option value="NCC">NCC Room</option>';
                roomSelector.innerHTML += '<option value="IOT">IOT Lab</option>';
                roomSelector.innerHTML += '<option value="SERVER">Server Room</option>';
                roomSelector.innerHTML += '<option value="LIBRARY">Library</option>';
                roomSelector.innerHTML += '<option value="MScLab">M.Sc. I.T. Lab</option>';
                roomSelector.innerHTML += '<option value="MdLab">Media Lab</option>';
                roomSelector.innerHTML += '<option value="ComLab">Commerce Lab</option>';
                roomSelector.innerHTML += '<option value="BScLab">B.Sc. I.T. Lab</option>';
            } else if (floorSelector.value === "third") {
                roomSelector.innerHTML += '<option value="IQAC">IQAC Room</option>';
                roomSelector.innerHTML += '<option value="301">Room 301</option>';
                roomSelector.innerHTML += '<option value="302">Room 302</option>';
                roomSelector.innerHTML += '<option value="303">Room 303</option>';
                roomSelector.innerHTML += '<option value="304">Room 304</option>';
                roomSelector.innerHTML += '<option value="305">Room 305</option>';
                roomSelector.innerHTML += '<option value="306">Room 306</option>';
                roomSelector.innerHTML += '<option value="307">Room 307</option>';
                roomSelector.innerHTML += '<option value="308">Room 308</option>';
                roomSelector.innerHTML += '<option value="309">Room 309</option>';
                roomSelector.innerHTML += '<option value="310">Room 310</option>';
                roomSelector.innerHTML += '<option value="311">Room 311</option>';
                roomSelector.innerHTML += '<option value="312">Room 312</option>';
                roomSelector.innerHTML += '<option value="313">Room 313</option>';
            } else if (floorSelector.value === "fourth") {
                roomSelector.innerHTML += '<option value="Auditorium">Auditorium</option>';
                roomSelector.innerHTML += '<option value="401">Room 401</option>';
                roomSelector.innerHTML += '<option value="402">Room 402</option>';
                roomSelector.innerHTML += '<option value="403">Room 403</option>';
                roomSelector.innerHTML += '<option value="404">Room 404</option>';
                roomSelector.innerHTML += '<option value="405">Room 405</option>';
            }
        } else {
            roomOptions.style.display = "none";
        }
    }
    
    
    
   

</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="script.js"></script>
</body>
</html>