$(document).ready(function () {
    const checkbox = $("#checkbox");
    const submitButton = $("#submitButton");
    const divInfo = $("#info");
    const flecompltDiv = $(".flecomplt");
    const loginFormDiv = $(".loginForm");
    flecompltDiv.hide();
    loginFormDiv.hide();
    checkbox.click(function () {

        if (checkbox.prop("checked")) {
            submitButton.prop("disabled", false);
        } else {
            submitButton.prop("disabled", true);
        }
    });
    setTimeout(function () {
        checkbox.prop("disabled", false);
    }, 30000);

    $("#complaintStatus").click(function (event) {
        event.preventDefault();
        divInfo.hide();
        flecompltDiv.hide();
        loginFormDiv.show();
    });
    submitButton.click(function () {
        divInfo.hide();
        flecompltDiv.show();
    });
    $("#fileComplaint").click(function (event) {
        event.preventDefault();
        divInfo.show();
        flecompltDiv.hide();
        loginFormDiv.hide();
    });
});


function showSuccessMessage(message, complaintNumber) {
        const successModal = document.createElement("div");
        successModal.className = "success-modal";
    
        successModal.innerHTML = `
            <h2>${message}</h2>
            <p>Your complaint number is: ${complaintNumber}</p>
            <p>Redirecting to Google.com in <span id="countdown">30</span> seconds...</p>
        `;
    
        document.body.appendChild(successModal);
    
        let countdown = 30;
        const countdownElement = document.getElementById("countdown");
        const countdownTimer = setInterval(function() {
            countdownElement.textContent = countdown; 
            if (countdown <= 0) {
                clearInterval(countdownTimer);
                successModal.remove(); 
    
                window.location.replace("https://www.google.com");
    
            }
            countdown--;
        }, 1000);
    }
    
    function resetForm() {
        const form = document.getElementById("complaintForm");
        form.reset();
    }
    
    const fileInput = document.getElementById("file_upload");
    const fileList = document.getElementById("fileList");
    const maxFileSize = 10 * 1024 * 1024; 
    
fileInput.addEventListener("change", function (event) {
        const selectedFiles = event.target.files;
        fileList.innerHTML = "";
        for (const file of selectedFiles) {
            if (file.size > maxFileSize) {
                alert("File size should be less than 10MB.");
                fileInput.value = "";
            }
    
            const fileNameWithoutSpaces = file.name.replace(/\s/g, "_");
    
            const listItem = document.createElement("li");
            listItem.textContent = fileNameWithoutSpaces;
    
            const removeButton = document.createElement("button");
            removeButton.textContent = "Remove";
            removeButton.addEventListener("click", function () {
                listItem.remove();
                const index = Array.from(fileInput.files).indexOf(file);
                if (index !== -1) {
                    const newFiles = Array.from(fileInput.files);
                    newFiles.splice(index, 1);
                    fileInput.files = new FileList(newFiles, fileInput);
                }
            });
    
            listItem.appendChild(removeButton);
            fileList.appendChild(listItem);
        }
    });
    

    
function populateYears() {
            const departmentSelect = document.getElementById("dept");
            const yearSelect = document.getElementById("year");
            const selectedDepartment = departmentSelect.value;
            const departments = {
                baf: ["FYBAF", "SYBAF", "TYBAF"],
                bbi: ["FYBBI", "SYBBI", "TYBBI"],
                bcom: ["FYBCom", "SYBCom", "TYBCom"],
                bscit: ["FYBScIT", "SYBScIT", "TYBScIT"],
                bmm: ["FYBMM", "SYBMM", "TYBMM"],
                bms: ["FYBMS", "SYBMS", "TYBMS"],
            };
            yearSelect.innerHTML = "<option value=''>Select a Year</option>";
            yearSelect.disabled = !(selectedDepartment in departments);
            if (selectedDepartment in departments) {
                const yearList = departments[selectedDepartment];
                for (const year of yearList) {
                    const option = new Option(year, year);
                    yearSelect.appendChild(option);
                }
            }
    }
    
const allowedCharsPattern = /^[a-zA-Z0-9,.\-_()\/@:&?\\% ]*$/;

function validateAndRestrictInput(inputElement) {
    inputElement.addEventListener("input", function (event) {
        const inputValue = event.target.value;
        if (!allowedCharsPattern.test(inputValue)) {
            const sanitizedValue = inputValue.replace(/[^a-zA-Z0-9,.\-_()\/@:&?\\% ]/g, '');
            event.target.value = sanitizedValue;
        }
    });
}

const formInputs = document.querySelectorAll('#complaintForm input, #complaintForm textarea, #complaintForm select, .loginForm       ');
formInputs.forEach(input => {
    validateAndRestrictInput(input);
});
   


/*
Advanced Validation
function validateAndRestrictInput(inputElement) {
    inputElement.addEventListener("input", function (event) {
        const inputValue = event.target.value;

        // If the input is an email input with id "email"
        if (inputElement.type === 'email' && inputElement.id === 'email') {
            // Define allowedCharsPattern for email input
            const allowedCharsPatternEmail = /^[a-zA-Z0-9@.]*$/;
            if (!allowedCharsPatternEmail.test(inputValue)) {
                // If invalid characters are entered, sanitize the input
                const sanitizedValue = inputValue.replace(/[^a-zA-Z0-9@.]/g, '');
                event.target.value = sanitizedValue;
            }
        } else {
            // Apply general character validation for other input elements
            const allowedCharsPattern = /^[a-zA-Z0-9,.\-@ ]*$/;
            if (!allowedCharsPattern.test(inputValue)) {
                // If invalid characters are entered, sanitize the input
                const sanitizedValue = inputValue.replace(/[^a-zA-Z0-9,.\-@ ]/g, '');
                event.target.value = sanitizedValue;
            }
        }
    });
}

// Apply the validation and restriction logic to all relevant input elements
const formInputs = document.querySelectorAll('#complaintForm input, #complaintForm textarea, #complaintForm select, .loginForm ');
formInputs.forEach(input => {
    validateAndRestrictInput(input);
});





*/


   
function refreshCaptcha() {
        const captchaImg = document.getElementById("captchaImg");
        if (captchaImg) {
            captchaImg.src = "captcha.php?" + new Date().getTime();
        }
    }
    
function validateForm() {
        const departmentSelect = document.getElementById("dept");
        const yearSelect = document.getElementById("year");
        const committeeSelect = document.getElementById("cmite");
        const incidentDescriptionTextarea = document.getElementById("incident_description");
        const involvedInput = document.getElementById("involved");
        const incidentDateInput = document.getElementById("incdnt_date");
        const captchaInput = document.getElementById("captchaInput");

        const inputsToValidate = [
            { element: departmentSelect, message: "Please select a department." },
            { element: yearSelect, message: "Please select a year." },
            { element: committeeSelect, message: "Please select a committee." },
            { element: incidentDescriptionTextarea, message: "Please enter the incident description." },
            { element: involvedInput, message: "Please enter the individual/organization involved." },
            { element: incidentDateInput, message: "Please select the incident date." },
            { element: captchaInput, message: "Please enter the CAPTCHA." },
        ];

        for (const input of inputsToValidate) {
            if (input.element.value.trim() === "") {
                alert(input.message);
                return false;
            }
        }
        return true;
}


        
    document.getElementById("complaintForm").addEventListener("submit", function (event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        if (validateForm()) {
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const message = "Complaint submitted successfully!";
                    const complaintNumber = data.complaintNumber;
                    showSuccessMessage(message, complaintNumber);
                } else {
                    alert(data.message);
                }
            })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while submitting the form.');
                });
        
                resetForm();
        
                window.history.replaceState(null, null, window.location.href);
        }
    });
        
    document.addEventListener("DOMContentLoaded", function () {
        resetForm();
        populateYears();
    });




//Date constraint function started here
  // Get current date
var today = new Date();

// Calculate the date limits
var minDate = new Date(today.getFullYear() - 2, today.getMonth(), today.getDate());
var maxDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());

// Format the date strings
var minDateString = formatDate(minDate);
var maxDateString = formatDate(maxDate);

// Set the minimum and maximum dates for the input field
document.getElementById("incdnt_date").setAttribute("min", minDateString);
document.getElementById("incdnt_date").setAttribute("max", maxDateString);

// Function to format date as YYYY-MM-DD
function formatDate(date) {
  var year = date.getFullYear();
  var month = (date.getMonth() + 1).toString().padStart(2, '0');
  var day = date.getDate().toString().padStart(2, '0');
  return year + "-" + month + "-" + day;
}

//ended here


