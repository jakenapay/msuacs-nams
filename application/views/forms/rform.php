<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Registration Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 900px;
            max-width: 95%;
        }

        .form-title {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: bold;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.2);
        }

        .button-group {
            text-align: center;
            margin-top: 30px;
            grid-column: span 2;
        }

        button {
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 0 10px;
            transition: background-color 0.3s;
        }

        #submitBtn {
            background-color: #4CAF50;
            color: white;
        }

        #submitBtn:hover {
            background-color: #45a049;
        }

        #cancelBtn {
            background-color: #f44336;
            color: white;
        }

        #cancelBtn:hover {
            background-color: #da190b;
        }

        .error {
            color: #f44336;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="form-title">Visitor Registration Form</h1>
        <form id="visitorForm">
            <div class="form-grid">
                <!-- Left Column -->
                <div class="left-column">
                    <div class="form-group">
                        <label for="firstName">First Name:</label>
                        <input type="text" id="firstName" name="firstName" required>
                        <div class="error" id="firstNameError">Please enter your first name</div>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name:</label>
                        <input type="text" id="lastName" name="lastName" required>
                        <div class="error" id="lastNameError">Please enter your last name</div>
                    </div>
                    <div class="form-group">
                        <label for="idType">ID Type:</label>
                        <select id="idType" name="idType" required>
                            <option value="">Select ID Type</option>
                            <option value="A">Type A</option>
                            <option value="B">Type B</option>
                            <option value="C">Type C</option>
                        </select>
                        <div class="error" id="idTypeError">Please select an ID type</div>
                    </div>
                    <div class="form-group">
                        <label for="idNumber">ID Number:</label>
                        <input type="text" id="idNumber" name="idNumber" required>
                        <div class="error" id="idNumberError">Please enter your ID number</div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                        <div class="error" id="emailError">Please enter a valid email address</div>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="tel" id="phone" name="phone" required>
                        <div class="error" id="phoneError">Please enter a valid phone number</div>
                    </div>
                    <div class="form-group">
                        <label for="company">Company:</label>
                        <input type="text" id="company" name="company">
                    </div>
                </div>

                <!-- Right Column -->
                <div class="right-column">
                    <div class="form-group">
                        <label for="purpose">Purpose of Visit:</label>
                        <input type="text" id="purpose" name="purpose" required>
                        <div class="error" id="purposeError">Please enter the purpose of your visit</div>
                    </div>
                    <div class="form-group">
                        <label for="hostName">Host Name:</label>
                        <input type="text" id="hostName" name="hostName" required>
                        <div class="error" id="hostNameError">Please enter the host name</div>
                    </div>
                    <div class="form-group">
                        <label for="hostDepartment">Host Department:</label>
                        <input type="text" id="hostDepartment" name="hostDepartment" required>
                        <div class="error" id="hostDepartmentError">Please enter the host department</div>
                    </div>
                    <div class="form-group">
                        <label for="visitDate">Visit Date:</label>
                        <input type="datetime-local" id="visitDate" name="visitDate" required>
                        <div class="error" id="visitDateError">Please select a visit date and time</div>
                    </div>
                    <div class="form-group">
                        <label for="visitDuration">Visit Duration:</label>
                        <select id="visitDuration" name="visitDuration" required>
                            <option value="">Select duration</option>
                            <option value="1">1 hour</option>
                            <option value="2">2 hours</option>
                            <option value="3">3 hours</option>
                            <option value="4">4 hours</option>
                            <option value="8">Full day (8 hours)</option>
                        </select>
                        <div class="error" id="visitDurationError">Please select a visit duration</div>
                    </div>
                </div>

                <div class="button-group">
                    <button type="button" id="cancelBtn">Cancel</button>
                    <button type="submit" id="submitBtn">Submit</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('visitorForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Your validation function here
            if (!validateForm()) {
                return;
            }

            const formData = {
                firstName: document.getElementById('firstName').value,
                lastName: document.getElementById('lastName').value,
                idType: document.getElementById('idType').value,
                idNumber: document.getElementById('idNumber').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                company: document.getElementById('company').value,
                purpose: document.getElementById('purpose').value,
                hostName: document.getElementById('hostName').value,
                hostDepartment: document.getElementById('hostDepartment').value,
                visitDate: document.getElementById('visitDate').value,
                visitTime: document.getElementById('visitDate').value,
                visitDuration: document.getElementById('visitDuration').value
            };

            console.log('Request data:', formData);

            fetch('VisitorsFormController/registore', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => {
                    // Check if the response status is 200 (OK)
                    console.log('Response Status:', response.status);
                    return response.json(); // Get the raw response as text
                })
                .then(function (data) {
                    console.log(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again. Error details: ' + error.message);
                });
        });



        document.getElementById('cancelBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to cancel? All entered data will be lost.')) {
                document.getElementById('visitorForm').reset();
                hideAllErrors();
            }
        });

        function validateForm() {
            let isValid = true;
            hideAllErrors();

            // Required field validation
            const requiredFields = [
                'firstName', 'lastName', 'idType', 'idNumber', 'email',
                'phone', 'purpose', 'hostName', 'hostDepartment',
                'visitDate', 'visitDuration'
            ];

            requiredFields.forEach(field => {
                const element = document.getElementById(field);
                if (!element.value.trim()) {
                    showError(field);
                    isValid = false;
                }
            });

            // Email validation
            const email = document.getElementById('email').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError('email');
                isValid = false;
            }

            // Phone validation
            const phone = document.getElementById('phone').value;
            const phoneRegex = /^\+?[\d\s-]{10,14}$/;
            if (!phoneRegex.test(phone)) {
                showError('phone');
                isValid = false;
            }

            // Visit date validation
            const visitDateInput = document.getElementById('visitDate').value;
            const visitDate = new Date(visitDateInput);
            const now = new Date();
            if (!visitDateInput || visitDate < now) {
                showError('visitDate');
                isValid = false;
            }

            return isValid;
        }


        function showError(fieldId) {
            const errorElement = document.getElementById(fieldId + 'Error');
            if (errorElement) {
                errorElement.style.display = 'block';
            }
        }

        function hideAllErrors() {
            const errorElements = document.querySelectorAll('.error');
            errorElements.forEach(element => {
                element.style.display = 'none';
            });
        }

        // Set minimum date for visit date picker
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('visitDate').min = today + 'T00:00';
    </script>
</body>

</html>