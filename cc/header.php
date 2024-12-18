<!doctype html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <meta http-equiv="refresh" content="1800;url=index.php?logout" />
<style>
  tr:nth-child(even) 
  {
    background-color: #f2f2f2;
  }
</style>
</head>
<body>
  <div class="header">
    <div class="row">
      <div class="col">
        <br>
        <h1 style="margin-top:  2%; text-shadow: 0px 0px 25px rgba(5, 171, 149, 1); margin-left: 20%; ">Anonymous Complaint System</h1>
      </div>
      <div class="col">
        <div class="col-md-auto">
          <table border="1" class="loginDetails">
            <tr>
              <td>Name:</td>
              <td><?php echo $_SESSION["name"];?></td>
              <td rowspan="4">
              <a href="?logout" onclick="return confirm('Are you sure you want to logout?')" style="writing-mode: vertical-rl" class="btn btn-primary">Logout</a>
              </td>
            </tr>
            <tr>
              <td>Email-Id:</td>
              <td><?php echo $_SESSION["email"];?></td>
            </tr>
            <tr>
              <td>Role:</td>
              <td><?php
                        // Display the role description based on the role code
                        if(isset($_SESSION["role"])) 
                        {
                          switch($_SESSION["role"]) 
                          {
                            case 'cm':
                            echo 'Committee Member';
                            break;
                            case 'cc':
                            echo 'Committee Convener';
                            break;
                            case 'admin':
                            echo 'Admin';
                            break;
                            default:
                            // Handle unknown roles here if needed
                            echo $_SESSION["role"];
                          break;
                          }
                        }
                        ?>
                </td>
              </tr>
              <tr>
                <td>Committee:</td>
                <td><?php 
                    if(isset($_SESSION["committee"])) 
                    {
                      // Check if the user is an admin and not associated with any committee
                      if($_SESSION["role"] === 'admin' && $_SESSION["committee"] === null) 
                      {
                        echo 'You are an admin and are not associated with any committee. Be responsible.';
                      }
                      else
                      {
                        // Otherwise, display the committee description based on the committee code
                        switch($_SESSION["committee"]) 
                        {
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
                    ?></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
<hr>
<?php
function logout() {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to Google.com using JavaScript
    echo '<script>window.location.href = "/";</script>';
    exit;
}
if (!isset($_SESSION["user_id"])) {
    header("Location: /"); // Redirect to login page if not logged in
    exit;
}
if ($_SESSION["role"] !== "cc") {
    header("Location: /"); // Redirect unauthorized users
    exit;
}
// Check if the logout link is clicked
if (isset($_GET['logout'])) {
    // If clicked, show confirmation alert
    echo '<script>
            var confirmLogout = confirm("Are you sure you want to logout?");
            if (confirmLogout) {
                ' . logout() . '
            } else {
                // If cancelled, do nothing
            }
          </script>';
}
?>
</body>
</html>
