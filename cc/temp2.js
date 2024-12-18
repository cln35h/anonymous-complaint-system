$(document).ready(function()
{
    $('#add_button').click(function()
    {
        $('#member_form')[0].reset();
        $('.modal-title').text("Add New Details");
        $('#action').val("Last");
        $('#operation').val("Last");
    });
    
    var dataTable = $('#member_table').DataTable
    ({
        "paging":true,
        "processing":true,
        "serverSide":true,
        "order": [],
        "info":true,
        "ajax":{
            url:"fetchAll.php",
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
    var complaint_number = $('#complaint_number').val();
    var remarks = $('#additional_remarks').val();
    var status = ($('#action').val() === "Add Remarks and Save") ? "Posted" : "Closed";
    
    if( complaint_number != '') {
        var formData = new FormData(this);
        formData.append('complaint_number', complaint_number);
        formData.append('remarks', remarks);
        formData.append('status', status); // Add status to the form data

        $.ajax({
            url: "insertupdated.php",
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                $('#member_form')[0].reset();
                $('#userModal').modal('hide');
                dataTable.ajax.reload();
                alert("Form submitted successfully!");
            }
        });
    } else {
        alert("Complaint number is a required field");
    }
});




    $(document).on('click', '.update', function() {
    var member_id = $(this).attr("id");
    $.ajax({
        url: "fetch_single.php",
        method: "POST",
        data: {member_id: member_id},
        dataType: "json",
        success: function(data) {
            // Populate the form fields
            $('#complaint_number').val(data.complaint_number); // Set the complaint number
            $('#userModal').modal('show');
           $('#complaint_number_label').text(data.complaint_number); // Set the text content of the label
            $('#id').text(data.id);
            $('#dept').text(data.dept);
            $('#year').text(data.year);
            $('#committee').text(data.committee);
            $('#incdnt_dscrptin').text(data.incdnt_dscrptin);
            $('#indiv_inv').text(data.indiv_inv);
            $('#dateTime').text(data.dateTime); // Set the text content of the span with id "dateTime"
            $('#year').text(data.year);
            $('#location').text(data.location);
            $('#add_dtls').text(data.add_dtls);
            $('#timestamp').text(data.timestamp);
            $('#remarks1').text(data.remarks);
            $('#status').text(data.status);
            // Clear the value of additional_remarks field
            $('#additional_remarks').val('');

            // Display files if available
            if (data.file_upload != null) {
    var files = data.file_upload.split(',');
    var filesHtml = '';
    files.forEach(function(file) {
        // Remove '/cm/' from the file path if present
        var filePath = file.replace('/cc/', '/');
        // Construct the correct URL based on the file's location outside of 'cm' folder
        var fileUrl = 'https://dev.cln35h.in/' + filePath;
        var fileName = file.split('/').pop(); // Extract file name
        var fileNameWithoutPrefix = fileName.substring(fileName.lastIndexOf('_') + 1); // Remove prefix
        filesHtml += '<a href="' + fileUrl + '" target="_blank">' + fileNameWithoutPrefix + '</a><br>';
    });
    $('#displayFiles').html(filesHtml);
} else {
    $('#displayFiles').html('No files available');
}

            $('.modal-title').text("Complaint Details");
            $('#member_id').val(member_id);
            $('#action').val("Close Status");
            $('#operation').val("Edit");
        }
    });
});


    
});
