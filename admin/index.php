<?php
// Start the session
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: /"); // Redirect to login page if not logged in
    exit;
}

if ($_SESSION["role"] !== "admin") {
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">
</head>
<body>
    <div class="container1">
    <!--Navigation -->
<div class="row navbar" style="background-color: #cddaf2;">
    <div class="col">&nbsp;</div>
    <div class="col"><a href="#" class="nav-link" data-tab="dashboard">Dashboard</a></div>
    <div class="col"><a href="#" class="nav-link" data-tab="manage-user">Manage User</a></div>
    <div class="col"><a href="#" class="nav-link" data-tab="complaints">Reports</a></div>
    <div class="col"><a href="/report/manual.php" class="nav-link" data-tab="manual">Manual</a></div>
</div>
</div>
<!-- End of Navigation -->
<hr>

<div id="dashboard" class="tab-content  active">
    <!-- Dashboard content goes here -->
    
    <div class="container">
        
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<h1>Assigned Complaints</h1>
<select id="committeeSelect">
  <option value="all">All Committees</option>
  <option value="exc">Examination Committee</option>
  <option value="icc">Internal Complaint Committee</option>
  <option value="ggc">Grievance Redressal Committee</option>
</select>

<div>
  <canvas id="complaintsChart" width="400" height="200"></canvas>
</div>
<script>
  // Place your JavaScript code at the end of the body to ensure the DOM has fully loaded
  document.addEventListener('DOMContentLoaded', function() {
    let complaintsChart;
    
    // Function to fetch complaints data
    function fetchComplaints(committee) {
        if (committee === 'all') {
            return fetch('fetcher.php')
                .then(response => response.json())
                .catch(error => console.error('Error fetching data:', error));
        } else {
            return fetch('fetch_complaints.php?committee=' + committee)
                .then(response => response.json())
                .catch(error => console.error('Error fetching data:', error));
        }
    }

    // Function to update or create the chart
    function updateChart(data, stacked, subtitleText) {
        const chartCanvas = document.getElementById('complaintsChart');
        const ctx = chartCanvas.getContext('2d');

        // Clear existing chart
        if (complaintsChart) {
            complaintsChart.destroy();
        }

        // Create new chart
        complaintsChart = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                plugins: {
                    subtitle: {
                        display: true,
                        text: subtitleText // Update subtitle dynamically
                    }
                },
                scales: {
                    xAxes: [{
                        stacked: stacked
                    }],
                    yAxes: [{
                        stacked: stacked,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    }

    // Function to display complaints based on selected committee
    function displayComplaints(committee) {
      // Fetch complaints data from server
      fetchComplaints(committee)
          .then(data => {
              let subtitleText = '';

              if (committee === 'all') {
                  subtitleText = 'All Committee Status';
                  // Generate chart data for all committees
                  const chartData = {
                      labels: data.map(committeeData => committeeData.committee),
                      datasets: [
                          {
                              label: 'Posted',
                              backgroundColor: 'rgba(255, 99, 132, 0.5)',
                              data: data.map(committeeData => committeeData.posted)
                          },
                          {
                              label: 'Acknowledged',
                              backgroundColor: 'rgba(54, 162, 235, 0.5)',
                              data: data.map(committeeData => committeeData.acknowledged)
                          },
                          {
                              label: 'Closed',
                              backgroundColor: 'rgba(255, 206, 86, 0.5)',
                              data: data.map(committeeData => committeeData.closed)
                          }
                      ]
                  };
                  updateChart(chartData, true, subtitleText); // Stack bars for all committees
              } else {
                  // Find the full name of the selected committee
                  let fullCommitteeName = '';
                  switch (committee) {
                      case 'exc':
                          fullCommitteeName = 'Examination Committee';
                          break;
                      case 'icc':
                          fullCommitteeName = 'Internal Complaint Committee';
                          break;
                      case 'ggc':
                          fullCommitteeName = 'Grievance Redressal Committee';
                          break;
                      default:
                          fullCommitteeName = 'Unknown Committee';
                          break;
                  }
                  subtitleText = 'Selected Committee: ' + fullCommitteeName;

                  // Generate chart data for the selected committee
                  const statusCounts = {};
                  data.forEach(item => {
                      if (statusCounts[item.status]) {
                          statusCounts[item.status]++;
                      } else {
                          statusCounts[item.status] = 1;
                      }
                  });

                  const chartData = {
                      labels: Object.keys(statusCounts),
                      datasets: [{
                          label: 'Number of Complaints',
                          data: Object.values(statusCounts),
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
                  };
                  updateChart(chartData, false, subtitleText); // Simple bars for individual committee
              }
          })
          .catch(error => console.error('Error fetching data:', error));
    }   


    // Event listener for committee selection
    const committeeSelect = document.getElementById('committeeSelect');
    committeeSelect.addEventListener('change', function() {
        const selectedCommittee = this.value;
        displayComplaints(selectedCommittee);
    });

    // Display complaints for the default selection (All Committees)
    displayComplaints('all');
  });

</script>


    </div>
</div>


<!-- Member Details -->
<div id="manage-user" class="tab-content">
    <!-- Manage User content goes here -->
    <div class="container-xl">
        <br />
        <h3 align="center">Add and Update Member</h3>
        <br />
        <div align="right">
            <button type="button" id="add_button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
            Add Member
            </button>
        </div>
        <br />
        <table id="member_table" class="table table-striped">  
            <thead bgcolor="#6cd8dc">
                <tr class="table-primary">
                    <th >Serial No.</th>
                    <th >Name</th>  
                    <th >Email</th>
                    <th >Role</th>
                    <th >Committee</th>
                    <th scope="col" >Edit</th>
                    <th scope="col">Delete</th>
                </tr>
            </thead>
        </table>

        <div class="modal" id="userModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body container-xl">
                        <form method="post" id="member_form" enctype="multipart/form-data">
                            <div class="modal-body">
                                <label>Enter Name</label>
                                <input type="text" name="name" id="name" class="form-control" />
                                <br />
                                <label>Enter Email</label>
                                <input type="email" name="email" id="email" pattern="[a-zA-Z0-9._%+-]+@thesiacollege.com" title="Please enter a valid email address with the domain 'thesiacollege.com'" required class="form-control" />
                                <br /> 
                                <!-- Custom selection -->
                                <label>Select Role:</label>
                                <br/>
                                <select id="roles" name="role" class="form-control" onchange="showCommittees()">
                                    <option value="admin">Admin</option>
                                    <option value="cc">Committee Convener</option>
                                    <option value="cm">Committee Member</option>
                                </select>
                                <br>
                                <!-- Committee Section initially hidden -->
                                <div id="committeeSection" style="display: none;"> 
                                    Select Committee:
                                    <select id="committees" class="form-control" name="committee">
                                        <option value="exc">Examination Committee</option>
                                        <option value="icc">Internal Complaint Committee</option>
                                        <option value="ggc">General Grievance Committee</option>
                                    </select>
                                    <br>
                                </div>
                            </div>                            
                            <div class="modal-footer">
                                <!-- Changed "id" to "member_id" here -->
                                <input type="hidden" name="member_id" id="id" />
                                <input type="hidden" name="operation" id="operation" />
                                <div id="password_update_button"></div>
                                <!-- Submit button -->
                                <input type="submit" class="btn btn-primary" value="Submit" />
                                <!-- Close button -->
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- End of Member Details -->


<div id="complaints" class="tab-content">
  <!-- Complaints content goes here -->

    <div  style="max-width: fit-content; border: 1px solid black; margin: 0 auto">
    <form id="reportForm">
        <table class="table table-stripped">
            
        <tr>
            <td>
                <label for="committeeSelect1">Select Committee:</label>
            </td>
            <td>
                <select id="committeeSelect1" name="committee">
                    <option value="all">All Committees</option>
                    <option value="exc">Examination Committee</option>
                    <option value="icc">Internal Complaint Committee</option>
                    <option value="ggc">Grievance Redressal Committee</option>
                </select>
            </td>
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
    var committee = document.getElementById("committeeSelect1").value;
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
    xhr.open('GET', 'generateReport.php?committee=' + committee + '&status=' + status + '&startDate=' + startDate + '&endDate=' + endDate, true);
    xhr.send();
}
</script>

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
<script type="text/javascript" src="temp.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
