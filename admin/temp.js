$(document).ready(function(){
    $('#add_button').click(function(){
        $('#member_form')[0].reset();
        $('.modal-title').text("Add New Details");
        $('#action').val("Add");
        $('#operation').val("Add");
    });
     
    var dataTable = $('#member_table').DataTable({
        "paging":true,
        "processing":true,
        "serverSide":true,
        "order": [],
        "info":true,
        "ajax":{
            url:"fetch.php",
            type:"POST"
               },
        "columnDefs":[
            {
                "targets":'_all',
                "orderable":false,
            },
        ],    
        "createdRow": function (row, data, index) {
            // Set the serial number for each row
            $('td', row).eq(0).html(index + 1);
        }, 
    });
 
     $(document).on('submit', '#member_form', function(event){
    event.preventDefault();
    var id = $('#id').val();
    var name = $('#name').val();
    var email = $('#email').val();
     
    // Check if the email matches the required pattern and domain
    if (/^[a-zA-Z0-9._%+-]+@thesiacollege.com$/.test(email)) {
        if(name != '' && email != '') {
            $.ajax({
                url:"function.php",
                method:'POST',
                data:new FormData(this),
                contentType:false,
                processData:false,
                success:function(data) {
                    $('#member_form')[0].reset();
                    $('#userModal').modal('hide');
                    dataTable.ajax.reload();
                    // Parse the JSON data
                    var response = JSON.parse(data);
                    
                    // Display the email and generated password in a custom alert dialog
                    Swal.fire({
                        title: 'User Added Successfully',
                        html: `<p>Email: ${response.email}</p><p>Password: ${response.password}</p>`,
                        icon: 'success',
                        showCloseButton: true,
                        showCancelButton: false,
                        showConfirmButton: false,
                        focusConfirm: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        customClass: {
                            popup: 'copy-to-clipboard-alert',
                        },
                        didOpen: () => {
                            // Add event listener to the copy button
                            const copyButton = document.createElement('button');
                            copyButton.textContent = 'Copy Password';
                            copyButton.classList.add('swal2-confirm');
                            copyButton.addEventListener('click', () => {
                                navigator.clipboard.writeText(response.password);
                                Swal.fire({
                                    title: 'Password Copied',
                                    text: 'You can now paste the password wherever you need it.',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            });
                            Swal.getPopup().querySelector('.swal2-actions').appendChild(copyButton);
                        }
                    });
                }
            });
        } else {
            alert("Name and email fields are required");
        }
    } else {
        alert("Email address must end with '@thesiacollege.com'");
    }
});


     
    

  $(document).on('click', '.update', function(){
    var member_id = $(this).attr("id");
    $.ajax({
        url:"fetch_single.php",
        method:"POST",
        data:{member_id:member_id},
        dataType:"json",
        success:function(data)
        {
            $('#userModal').modal('show');
            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#email').val(data.email);

            // Set the selected role
            $('#roles').val(data.role);
            // If the selected role is Committee Convener or Committee Member, display the committee section
            if (data.role === "cc" || data.role === "cm") {
                $('#committeeSection').show();
                // Set the selected committee
                $('#committees').val(data.committee);
            } else {
                $('#committeeSection').hide();
            }

            // Include the update password button in the modal
            $('#password_update_button').html('<button id="updatePasswordBtn" class="btn btn-primary">Update Password</button>');

            $('.modal-title').text("Edit Member Details");
            $('#member_id').val(member_id);
            $('#action').val("Save");
            $('#operation').val("Edit");
        }
    })
});

// Handle the "Update Password" button click
$(document).on('click', '#updatePasswordBtn', function() {
    var member_id = $('#id').val();
    $.ajax({
        url: "fetch_single.php",
        method: "POST",
        data: { member_id: member_id, update_password: true },
        dataType: "json",
        success: function(response) {
            // Display the updated email, role, and password using SweetAlert2
            Swal.fire({
                title: 'User Updated Successfully',
                html: '<p>Email: ' + response.email + '</p>' +
                      '<p>Password: ' + response.password + '</p>',
                icon: 'success'
            });
        }
    });
});



     
    $(document).on('click', '.delete', function(){
        var member_id = $(this).attr("id");
        if(confirm("Are you sure you want to delete this user?"))
        {
            $.ajax({
                url:"delete.php",
                method:"POST",
                data:{member_id:member_id},
                success:function(data)
                {
                    dataTable.ajax.reload();
                }
            });
        }
        else
        {
            return false;   
        }
    });
     
     
});
function showCommittees() {
            var role = document.getElementById("roles").value;
            var committeeSection = document.getElementById("committeeSection");

            // Hide the committee section by default
            committeeSection.style.display = "none";

            // If the selected role is Committee Convener or Committee Member, display the committee section
            if (role === "cc" || role === "cm") {
                committeeSection.style.display = "block";
            }
        }
