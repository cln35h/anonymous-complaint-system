<?php
include('db.php');
include('function.php');

if(isset($_POST["member_id"])) {
    $output = array();
    $statement = $connection->prepare("
        SELECT 
            c.*, 
            ac.remarks1, 
            ac.complaint_number, 
            ac.status, 
            IFNULL(CONCAT(c.date, ' ', c.time), c.date) AS dateTime
        FROM 
            complaint2 c 
        JOIN 
            assignedcomplaints ac ON c.complaint_number = ac.complaint_number 
        WHERE 
            c.id = :id 
        LIMIT 
            1

    ");
    $statement->execute(array(':id' => $_POST["member_id"]));
    $result = $statement->fetchAll();

    foreach($result as $row) {
        $output["id"] = $row["id"];
        // Translate department code to full department name
        switch ($row["dept"]) {
            case "baf":
                $output["dept"] = "Bachelor Of Accounting And Finance";
                break;
            case "bbi":
                $output["dept"] = "Bachelor Of Commerce - Banking And Insurance";
                break;
            case "bcom":
                $output["dept"] = "Bachelor Of Commerce";
                break;
            case "bscit":
                $output["dept"] = "Bachelor Of Science - Information Technology";
                break;
            case "bmm":
                $output["dept"] = "Bachelor of Arts in Multimedia and Mass Communication";
                break;
            case "bms":
                $output["dept"] = "Bachelor Of Management Studies";
                break;
            default:
                $output["dept"] = $row["dept"]; // Use department code as is if not found in the translation
                break;
        }
        switch ($row["committee"]) {
            case "exc":
                $output["committee"] = "Examination Committee";
                break;
            case "icc":
                $output["committee"] = "Internal Complaint Committee";
                break;
            case "ggc":
                $output["committee"] = "Grievance Redressal Committee";
                break;
            default:
                $output["committee"] = $row["committee"]; // Use committee code as is if not found in the translation
                break;
        }
        switch ($row["location"]) {
     case "G01":
                $output["location"] = "Room No. G01";
                break;
            case "401":
                $output["location"] = "Room No. G02";
                break;
            case "403":
                $output["location"] = "Room No. G03";
                break;
            case "404":
                $output["location"] = "Room No. G04";
                break;
            case "405":
                $output["location"] = "Room No. G05";
                break;
                 case "G06":
                $output["location"] = "Room No. G06";
                break;
            case "BMMSR":
                $output["location"] = "BMM Staff Room";
                break;
            case "ITS":
                $output["location"] = "IT Staff Room";
                break;
            case "MgR":
                $output["location"] = "Management Room";
                break;
            case "CoSR":
                $output["location"] = "Common StaffRoom";
                break;
                 case "101":
                $output["location"] = "Room No. 101";
                break;
            case "102":
                $output["location"] = "Room No. 102";
                break;
            case "103":
                $output["location"] = "Room No. 103";
                break;
            case "104":
                $output["location"] = "Room No. 104";
                break;
            case "105":
                $output["location"] = "Room No. 105";
                break;
                 case "106":
                $output["location"] = "Room No. 106";
                break;
            case "107":
                $output["location"] = "Room No. 107";
                break;
            
            case "NCC":
                $output["location"] = "NCC Room";
                break;
            case "IOT":
                $output["location"] = "IOT Lab";
                break;
            case "SERVER":
                $output["location"] = "Server Room";
                break;
            case "LIBRARY":
                $output["location"] = "Library";
                break;
            case "MScLab":
                $output["location"] = "M.Sc. I.T. Lab";
                break;
            case "ComLab":
                $output["location"] = "Commerce Lab";
                break;
            case "MdLab":
                $output["location"] = "Media Lab";
                break;
            case "BScLab":
                $output["location"] = "B.Sc. I.T. Lab";
                break;
            case "IQAC":
                $output["location"] = "IQAC Room";
                break;
            case "301":
                $output["location"] = "Room No. 301";
                break;
            case "302":
                $output["location"] = "Room No. 302";
                break;
            case "303":
                $output["location"] = "Room No. 303";
                break;
            case "304":
                $output["location"] = "Room No. 304";
                break;
            case "305":
                $output["location"] = "Room No. 305";
                break;
            case "306":
                $output["location"] = "Room No. 306";
                break;
            case "307":
                $output["location"] = "Room No. 307";
                break;
            case "308":
                $output["location"] = "Room No. 308";
                break;
            case "309":
                $output["location"] = "Room No. 309";
                break;
            case "310":
                $output["location"] = "Room No. 310";
                break;
            case "311":
                $output["location"] = "Room No. 311";
                break;
            case "312":
                $output["location"] = "Room No. 312";
                break;
            case "313":
                $output["location"] = "Room No. 313";
                break;
            case "Auditorium":
                $output["location"] = "Auditorium";
                break;
            case "401":
                $output["location"] = "Room No. 401";
                break;
            case "401":
                $output["location"] = "Room No. 402";
                break;
            case "403":
                $output["location"] = "Room No. 403";
                break;
            case "404":
                $output["location"] = "Room No. 404";
                break;
            case "405":
                $output["location"] = "Room No. 405";
                break;
            default:
                $output["location"] = $row["location"]; // Use committee code as is if not found in the translation
                break;
        }
        $output["complaint_number_label"] = $row["complaint_number"];
        $output["incdnt_dscrptin"] = $row["incdnt_dscrptin"];
        $output["indiv_inv"] = $row["indiv_inv"];
        $output["year"] = $row["year"];
        $output["add_dtls"] = isset($row["add_dtls"]) ? $row["add_dtls"] : "Not Found";
        $output["dateTime"] = $row["dateTime"]; 
        $output["file_upload"] = $row["file_upload"];
        $output["timestamp"] = $row["timestamp"];
        $output["remarks"] = isset($row["remarks1"]) ? $row["remarks1"] : "Not Found";
        $output["complaint_number"] = $row["complaint_number"];
        $output["status"] = $row["status"];   
          
    }
    echo json_encode($output);
}
?>
