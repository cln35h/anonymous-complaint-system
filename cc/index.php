<?php
// Start the session
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: /"); // Redirect to login page if not logged in
    exit;
}
if ($_SESSION["role"] !== "cc") {
    header("Location: /"); // Redirect unauthorized users
    exit;
}
include "header.php";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
    
    <title>Anonymous Complaint System</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">
</head>
<body>
  <div class="container1">
    <!--Navigation -->
<div class="row navbar" style="background-color: #cddaf2;">
    <div class="col">&nbsp;</div>
    <div class="col"><a href="#" class="nav-link" data-tab="dashboard">Dashboard</a></div>
    <div class="col"><a href="#" class="nav-link" data-tab="complaints">Complaints</a></div>
    <div class="col"><a href="/report" class="nav-link" data-tab="report">Report</a></div>
    <div class="col"><a href="/report/manual.php" class="nav-link" data-tab="manual">Manual</a></div>
</div>
<!-- End of Navigation -->
  </div>
<hr>

<div id="dashboard" class="tab-content  active">
  <!-- Dashboard content goes here -->
  <h2>Assigned Complaints to:    <?php
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
  </h2>
  <table>
    <tr>
      <td>
        <label for="startDate">From Date:</label>
      </td>
      <td>
        <input type="date" id="startDate" name="startDate">
      </td>
      <td>
        <label for="endDate">To Date:</label>
      </td>
      <td>
        <input type="date" id="endDate" name="endDate">
      </td>
      <td colspan="2">
        <button type="button" class="btn btn-primary" style="display: block; margin: 0 auto;" onclick="displayGraph()">Display Graph</button>
      </td>
    </tr>
  </table>
  <div>
    <canvas id="complaintsChart" width="fit-content" height="fit-content"></canvas>
  </div>


<script>
  document.addEventListener('DOMContentLoaded', function() {
    let complaintsChart;

    // Function to fetch and display complaints based on selected committee and date range
    function displayComplaints(committee, startDate, endDate) {
      // Fetch complaints data from server with selected committee and date range
      fetch(`fetcher.php?committee=${committee}&startDate=${startDate}&endDate=${endDate}`)
        .then(response => response.json())
        .then(data => {
          // Generate chart data
          const statusCounts = {};
          data.forEach(item => {
            if (statusCounts[item.status]) {
              statusCounts[item.status]++;
            } else {
              statusCounts[item.status] = 1;
            }
          });

          // Update or create chart
          updateChart(statusCounts);
        })
        .catch(error => console.error('Error fetching data:', error));
    }

    // Function to update or create the chart
    function updateChart(data) {
      const chartCanvas = document.getElementById('complaintsChart');
      const ctx = chartCanvas.getContext('2d');

      // Clear existing chart
      if (complaintsChart) {
        complaintsChart.destroy();
      }

      // Create new chart
      complaintsChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: Object.keys(data),
          datasets: [{
            label: 'Number of Complaints',
            data: Object.values(data),
            backgroundColor: [
              'rgba(255, 99, 132, 0.5)',
              'rgba(54, 162, 235, 0.5)',
              'rgba(255, 206, 86, 0.5)'
            ],
            borderColor: [
              'rgba(255, 99, 132, 1)',
              'rgba(54, 162, 235, 1)',
              'rgba(255, 206, 86, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true
              }
            }]
          }
        }
      });
    }

    // Function to handle report generation
    function displayGraph() {
      const committee = "<?php echo $_SESSION['committee']; ?>"; // Get the selected committee from PHP session
      const startDate = document.getElementById('startDate').value;
      const endDate = document.getElementById('endDate').value;
      displayComplaints(committee, startDate, endDate);
    }

    // Initially display complaints for the selected committee and default date range (today's date)
    const today = new Date();
    const defaultStartDate = today.toISOString().split('T')[0]; // Get today's date in yyyy-mm-dd format
    const defaultEndDate = today.toISOString().split('T')[0]; // Get today's date in yyyy-mm-dd format
    document.getElementById('startDate').value = defaultStartDate; // Set start date input field to today's date
    document.getElementById('endDate').value = defaultEndDate; // Set end date input field to today's date
    displayGraph(); // Display complaints for today initially

    // Add event listeners to date inputs for change events
    document.getElementById('startDate').addEventListener('change', displayGraph);
    document.getElementById('endDate').addEventListener('change', displayGraph);
  });
</script>

</div>

</div>
</div>
<!--Initializer -->
<div id="complaints" style="max-width: fit-content;" class="tab-content container-xl">
  <br />
  <table id="member_table" class="table table-striped">  
            <thead bgcolor="#6cd8dc">
                <tr class="table-primary">
                    <th width="5">Serial No.</th>
                    <th width="5">Year</th> 
                    <th width="10">Complaint Number</th>  
                    <th width="40">Incident Description</th>
                    <th width="30">Individual Involved</th>
                    <th scope="col" width="5">Remarks</th>
                    <th scope="col" width="5">Status</th>
                </tr>
            </thead>
        </table>
        
        <div class="modal " id="userModal" tabindex="-1">
        <div class="modal-dialog " style="max-width: fit-content;">
            <div class="modal-content container-xl" >
            <div class="modal-body">
                <form method="post" id="member_form" enctype="multipart/form-data">
                  <label for="complaint_number_label">Complaint Number:</label>
                         <span id="complaint_number_label" class="form-control"></span>
                        <label for="dept">Department:</label>
                          <span id="dept" class="form-control"></span>
                        <label for="year">Year:</label>
                          <span id="year" class="form-control"></span>
                       <label for="committee">Committee:</label>
                         <span id="committee" class="form-control"></span>
                        <label for="incdnt_dscrptin">Incident Description:</label>
                          <span id="incdnt_dscrptin" class="form-control"></span>
                        <label>Enter Individual Involved</label>
                          <span id="indiv_inv" class="form-control"></span>
                        <label for="dateTime">Date and Time:</label>
                          <span id="dateTime" class="form-control"></span>
                        <label for="location">Location of Incident:</label><span id="location" class="form-control"></span>
                      
                          <label for="add_dtls">Additional Description:</label>
                          <span id="add_dtls" class="form-control"></span>
                       <label>Complaint posted at:</label> <span id="timestamp" class="form-control"></span>
                        <label>View Files</label>
                          <span id="displayFiles" class="form-control"></span>
                        <label for="remarks1">Remarks:</label>
                          <span id="remarks1"  class="form-control" style="word-wrap: anywhere;"></span>
                         <br>
                          <textarea  name="additional_remarks" placeholder="Add Remarks"  id="additional_remarks" class="form-control" required></textarea></td>
                          
                      <hr>
                         <label for="status">Complaint Current Status:</label>
                         <span id="status" class="form-control"></span>

<!-- Modal Footer -->
    <div class="modal-footer">
    
            <input type="hidden" name="member_id" id="member_id" />
                        <input type="hidden" name="operation" id="operation" />
                        <input type="hidden" name="complaint_number" id="complaint_number" value="">
                        
                        <input type="submit" name="action" id="update" class="btn btn-primary" value="Add Remarks and Close Complaint" />
    
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
        
 </div>
                </form>
            </div>
            </div>
        </div>
        </div>
    </div>
        </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
   $(document).ready(function () {
        $('.nav-link').on('click', function (e) {
            e.preventDefault();
            // Hide all tab contents
            $('.tab-content').removeClass('active');
            // Show the selected tab content
            var tabId = $(this).data('tab');
            if (tabId === 'manual') {
                window.open($(this).attr('href'), '_blank'); // Open in a new tab
            } else if (tabId === 'report') {
                window.location.href = $(this).attr('href');
            } else {
                $('#' + tabId).addClass('active');
            }
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
<script src="temp2.js"text/javascript" language="javascript" >

</script>             
</body>
</html>