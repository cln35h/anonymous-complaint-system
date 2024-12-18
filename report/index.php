<html>
    <head>
        <title>Anonymous Complaint System</title>
        <style>
             @media print {
                /* Hide elements with the "no-print" class */
                .no-print {
                    display: none !important;
                    }
                    }
</style>

    </head>
<body>
<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: /"); // Redirect to login page if not logged in
    exit;
}
if (!isset($_SESSION["role"]) || ($_SESSION["role"] !== "cm" && $_SESSION["role"] !== "cc")) {
    header("Location: /"); // Redirect unauthorized users
    exit;
}
include 'header.php';
?>
<h5 style="text-align: center">Reports will be generated for  
    <?php 
    if(isset($_SESSION["committee"])) {
      // Check if the user is an admin and not associated with any committee
      if($_SESSION["role"] === 'admin' && $_SESSION["committee"] === null) {
        echo 'You are an admin and are not associated with any committee. Be responsible.';
      } else {
        // Otherwise, display the committee description based on the committee code
        switch($_SESSION["committee"]) {
          case 'icc': echo 'Internal Complaint Committee';
            break;
          case 'ggc': echo 'Grievance Redressal Committee';
            break;
          case 'exc': echo 'Examination Committee';
            break;
          default:
            // For other committees, display the committee code as it is
            echo $_SESSION["committee"];
            break;
        }
      }
    }
    ?>
</h5>
<hr>
<div  class="container-xl table table-striped" style="max-width: fit-content; border: 1px solid black; margin: 0 auto">
    <form id="reportForm">
        <table class="table table-stripped">
           
        </tr>
        <tr>
            <td>
                <label for="statusSelect">Select Status:</label>
            </td>
            <td>
                <select id="statusSelect" name="status">
                    <option value="all">All Statuses</option>
                    <option value="Posted">Posted</option>
                    <option value="Acknowledged">Acknowledged</option>
                    <option value="Closed">Closed</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <label for="startDate">From Date:</label>
            </td>
            <td>
                <input type="date" id="startDate" name="startDate">
            </td>

        </tr>
        <tr>
            <td>
                <label for="endDate">To Date:</label>
            </td>
            <td>
                <input type="date" id="endDate" name="endDate">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button type="button" class="btn btn-primary" style="display: block; margin: 0 auto;" onclick="generateReport()">Generate Report</button>
                

            </td>
        </tr>
    </table>
</form>
<div class="container-xl table table-striped" id="reportResult"></div>

<script>
function generateReport() {
    var status = document.getElementById("statusSelect").value;
    var startDate = document.getElementById("startDate").value;
    var endDate = document.getElementById("endDate").value;

    // AJAX request
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                document.getElementById("reportResult").innerHTML = xhr.responseText;
            } else {
                console.error('Error: ' + xhr.status);
            }
        }
    };
    // Get committee from session
    var committee = "<?php echo isset($_SESSION['committee']) ? $_SESSION['committee'] : ''; ?>";
    xhr.open('GET', 'generateReport.php?committee=' + committee + '&status=' + status + '&startDate=' + startDate + '&endDate=' + endDate, true);
    xhr.send();
}

function printReport() {
    var printContent = document.getElementById("reportResult").innerHTML;
    var committeeDescription = "<?php 
        if(isset($_SESSION["committee"])) {
            if($_SESSION["role"] === 'admin' && $_SESSION["committee"] === null) {
                echo 'You are an admin and are not associated with any committee. Be responsible.';
            } else {
                switch($_SESSION["committee"]) {
                    case 'icc': echo 'Internal Complaint Committee';
                        break;
                    case 'ggc': echo 'Grievance Redressal Committee';
                        break;
                    case 'exc': echo 'Examination Committee';
                        break;
                    default:
                        echo $_SESSION["committee"];
                        break;
                }
            }
        }
    ?>";

    var printWindow = window.open('', '_blank');
    printWindow.document.open();
    printWindow.document.write('<html><head><title>Print Report</title></head><body>');
    printWindow.document.write('<h5>Reports is generated for ' + committeeDescription + '</h5>');
    printWindow.document.write(printContent);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}
</script>

</div>
</body>
</html>