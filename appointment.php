<?php
require_once 'get_doctors.php';

// Function to get booked slots count
function getBookedSlotsCount($conn, $date, $time) {
    $sql = "SELECT COUNT(*) as count FROM appointments WHERE appointment_date = ? AND appointment_time = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $date, $time);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Get available time slots for selected date
if (isset($_POST['check_slots'])) {
    $selected_date = $_POST['date'];
    $conn = new mysqli("localhost", "root", "", "4semp");
    
    $time_slots = array(
        '10:00:00' => '10:00 AM',
        '10:30:00' => '10:30 AM',
        '11:00:00' => '11:00 AM',
        '11:30:00' => '11:30 AM',
        '12:00:00' => '12:00 PM',
        '14:00:00' => '2:00 PM',
        '14:30:00' => '2:30 PM',
        '15:00:00' => '3:00 PM',
        '15:30:00' => '3:30 PM',
        '16:00:00' => '4:00 PM'
    );

    $available_slots = array();
    foreach ($time_slots as $time => $display_time) {
        $booked = getBookedSlotsCount($conn, $selected_date, $time);
        $available = 10 - $booked;
        $available_slots[$time] = array(
            'display_time' => $display_time,
            'available' => $available
        );
    }
    
    echo json_encode($available_slots);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - Cloud Nine Community Hospital</title>
    <link rel="stylesheet" href="appointment.css">
    <style>
    .invalid-phone {
        border-color: #e74c3c !important;
        background-color: #fff5f5 !important;
    }

    .invalid-phone:focus {
        box-shadow: 0 0 0 2px rgba(231, 76, 60, 0.2) !important;
    }

    .form-group {
        margin-bottom: 20px;
        position: relative;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #2c3e50;
        font-weight: 500;
        font-size: 0.95em;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #dde1e5;
        border-radius: 8px;
        font-size: 1em;
        color: #2c3e50;
        background-color: white;
        transition: all 0.3s ease;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    /* Specific styles for time slot select */
    #timeSlot {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%232c3e50' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        padding-right: 40px;
    }

    #timeSlot optgroup {
        font-weight: 600;
        color: #2c3e50;
        background-color: #f8f9fa;
        padding: 8px;
    }

    #timeSlot option {
        padding: 12px;
        color: #2c3e50;
        background-color: white;
    }

    #timeSlot option:hover {
        background-color: #f8f9fa;
    }

    /* Style for available slots */
    #timeSlot option:not([disabled]) {
        color: #2c3e50;
    }

    /* Style for loading state */
    #timeSlot:disabled {
        background-color: #f8f9fa;
        cursor: not-allowed;
        color: #7f8c8d;
    }

    /* Time slot groups styling */
    #timeSlot optgroup {
        border-bottom: 1px solid #eee;
    }

    #timeSlot optgroup:last-child {
        border-bottom: none;
    }

    /* Hover and focus states */
    .form-group input:hover,
    .form-group select:hover,
    .form-group textarea:hover {
        border-color: #bdc3c7;
    }

    /* Error state */
    .form-group.error input,
    .form-group.error select,
    .form-group.error textarea {
        border-color: #e74c3c;
        background-color: #fff5f5;
    }

    /* Success state */
    .form-group.success input,
    .form-group.success select,
    .form-group.success textarea {
        border-color: #2ecc71;
        background-color: #f0fff4;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px 12px;
        }

        #timeSlot {
            background-position: right 12px center;
        }
    }

    /* Animation for focus */
    @keyframes focusIn {
        from {
            box-shadow: 0 0 0 0 rgba(52, 152, 219, 0);
        }
        to {
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        animation: focusIn 0.2s ease-out;
    }

    /* Placeholder styling */
    .form-group input::placeholder,
    .form-group select::placeholder,
    .form-group textarea::placeholder {
        color: #95a5a6;
    }

    /* Loading animation for timeSlot */
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }

    #timeSlot:disabled {
        animation: pulse 1.5s infinite;
    }

    /* Style for the form row (if you have it in your HTML) */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 15px;
        }
    }

    .invalid-email {
        border-color: #e74c3c !important;
        background-color: #fff5f5 !important;
    }

    .invalid-email:focus {
        box-shadow: 0 0 0 2px rgba(231, 76, 60, 0.2) !important;
    }
    </style>
</head>
<body>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check login status before showing appointment form
    const loggedIn = localStorage.getItem('username');
    if (!loggedIn) {
        alert('Please login to book an appointment');
        window.location.href = 'login.html';
        return;
    }

    // Set minimum date to today for appointmentDate
    const appointmentDateInput = document.getElementById('appointmentDate');
    const today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
    appointmentDateInput.setAttribute('min', today);

    // Add phone number validation
    const phoneInput = document.getElementById('phone');
    const appointmentForm = document.getElementById('appointmentForm');

    function isValidNepaliPhone(phone) {
        const nepaliPhonePattern = /^(98[456]\d{7}|97[45]\d{7}|01\d{7}|0[2-9]\d{7})$/;
        return nepaliPhonePattern.test(phone);
    }

    // Add input event listener for real-time validation
    phoneInput.addEventListener('input', function() {
        const phone = this.value.replace(/\s/g, ''); // Remove spaces
        
        if (!isValidNepaliPhone(phone)) {
            this.setCustomValidity('Please enter a valid Nepali phone number');
            phoneInput.classList.add('invalid-phone');
        } else {
            this.setCustomValidity('');
            phoneInput.classList.remove('invalid-phone');
        }
    });

    // Add form submit validation
    appointmentForm.addEventListener('submit', function(e) {
        const phone = phoneInput.value.replace(/\s/g, '');
        
        if (!isValidNepaliPhone(phone)) {
            e.preventDefault();
            alert('Please enter a valid Nepali phone number:\n\n' +
                  '• Mobile numbers: 984XXXXXXX, 985XXXXXXX, 986XXXXXXX\n' +
                  '• Landline numbers: 01XXXXXXX, 021XXXXXX, etc.');
            phoneInput.focus();
        }
    });

    // Add email validation
    const emailInput = document.getElementById('email');

    function isValidEmail(email) {
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return emailPattern.test(email);
    }

    // Add input event listener for real-time validation
    emailInput.addEventListener('input', function() {
        const email = this.value.trim();
        
        if (!isValidEmail(email)) {
            this.setCustomValidity('Please enter a valid email address (e.g., name@example.com)');
            emailInput.classList.add('invalid-email');
        } else {
            this.setCustomValidity('');
            emailInput.classList.remove('invalid-email');
        }
    });

    // Add form submit validation
    appointmentForm.addEventListener('submit', function(e) {
        const email = emailInput.value.trim();
        
        if (!isValidEmail(email)) {
            e.preventDefault();
            alert('Please enter a valid email address (e.g., name@example.com)');
            emailInput.focus();
        }
    });
});
</script>

    <div class="container">
        <div class="form-wrapper">
            <div class="form-header">
                <img src="footerlogo.png" alt="Hospital Logo" class="logo">
                <h1>Book Your Appointment</h1>
                <p>Cloud Nine Community Hospital</p>
            </div>
            
            <form id="appointmentForm" action="store_appointment.php" method="POST">
                <div class="form-group">
                    <label for="patientName">Full Name</label>
                    <input type="text" id="patientName" name="patientName" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               required 
                               pattern="[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}"
                               title="Please enter a valid email address (e.g., name@example.com)"
                               placeholder="name@example.com">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required 
                               placeholder="e.g., 9841234567 or 014123456"
                               pattern="(98[456]\d{7}|97[45]\d{7}|01\d{7}|0[2-9]\d{7})"
                               title="Please enter a valid Nepali phone number">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="appointmentDate">Preferred Date</label>
                        <input type="date" id="appointmentDate" name="appointmentDate" required>
                    </div>
                    <div class="form-group">
                        <label for="timeSlot">Preferred Time</label>
                        <select id="timeSlot" name="timeSlot" required>
                            <option value="">Select Time Slot</option>
                            <optgroup label="Morning">
                                <option value="10:00:00">10:00 AM</option>
                                <option value="10:30:00">10:30 AM</option>
                                <option value="11:00:00">11:00 AM</option>
                                <option value="11:30:00">11:30 AM</option>
                                <option value="12:00:00">12:00 PM</option>
                            </optgroup>
                            <optgroup label="Afternoon">
                                <option value="14:00:00">2:00 PM</option>
                                <option value="14:30:00">2:30 PM</option>
                                <option value="15:00:00">3:00 PM</option>
                                <option value="15:30:00">3:30 PM</option>
                                <option value="16:00:00">4:00 PM</option>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="department">Department:</label>
                    <select id="department" name="department" required>
                        <option value="">Choose a department</option>
                        <?php
                        $departments = array();
                        foreach ($doctors as $doctor) {
                            if (!in_array($doctor['department'], $departments)) {
                                $departments[] = $doctor['department'];
                                echo "<option value='" . htmlspecialchars($doctor['department']) . "'>" . 
                                     ucfirst(htmlspecialchars($doctor['department'])) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="doctor">Select Doctor:</label>
                    <select id="doctor" name="doctor" required>
                        <option value="">Choose a doctor</option>
                        <?php
                        foreach ($doctors as $doctor) {
                            echo "<option value='" . htmlspecialchars($doctor['doctor_id']) . "' data-department='" . 
                                 htmlspecialchars($doctor['department']) . "'>" . 
                                 htmlspecialchars($doctor['doctor_name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="reason">Reason for Visit</label>
                    <textarea id="reason" name="reason" rows="4" required></textarea>
                </div>

                <button type="submit" class="submit-btn">Confirm Appointment</button>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const doctorSelect = document.getElementById('doctor');
        const departmentSelect = document.getElementById('department');

        // When department changes
        departmentSelect.addEventListener('change', function() {
            const selectedDepartment = this.value;
            const doctorOptions = doctorSelect.options;

            // Reset doctor selection
            doctorSelect.value = '';

            // Show/hide doctors based on department
            for (let i = 0; i < doctorOptions.length; i++) {
                const option = doctorOptions[i];
                if (option.dataset.department === selectedDepartment || option.value === '') {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            }
        });

        // When doctor changes
        doctorSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value !== '') {
                departmentSelect.value = selectedOption.dataset.department;
            }
        });

        // Handle URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const selectedDoctor = urlParams.get('doctor');
        const selectedDepartment = urlParams.get('department');

        if (selectedDoctor && selectedDepartment) {
            departmentSelect.value = selectedDepartment.toLowerCase();
            // Find and select the matching doctor
            Array.from(doctorSelect.options).forEach(option => {
                if (option.text === selectedDoctor) {
                    doctorSelect.value = option.value;
                }
            });
        }
    });

    document.getElementById('appointmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Create FormData object
        const formData = new FormData(this);

        // Submit form using fetch
        fetch('store_appointment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = 'home.php';
            } else {
                alert(data.message || 'Error booking appointment');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while booking the appointment');
        });
    });

    // Add this for debugging
    console.log('Form script loaded');
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const appointmentDateInput = document.getElementById('appointmentDate');
        const timeSlotSelect = document.getElementById('timeSlot');
        const originalOptions = timeSlotSelect.innerHTML; // Store original options

        // Update time slots based on date selection
        appointmentDateInput.addEventListener('change', function() {
            const selectedDate = this.value;
            if (!selectedDate) return;

            // Show loading state
            timeSlotSelect.disabled = true;
            timeSlotSelect.innerHTML = '<option value="">Loading available slots...</option>';

            // Fetch available slots
            fetch('appointment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'check_slots=1&date=' + selectedDate
            })
            .then(response => response.json())
            .then(slots => {
                // Reset select element
                timeSlotSelect.innerHTML = '<option value="">Select Time Slot</option>';
                
                // Create morning and afternoon groups
                let morningGroup = document.createElement('optgroup');
                morningGroup.label = 'Morning';
                let afternoonGroup = document.createElement('optgroup');
                afternoonGroup.label = 'Afternoon';

                // Populate time slots
                Object.entries(slots).forEach(([time, data]) => {
                    if (data.available > 0) {
                        const option = document.createElement('option');
                        option.value = time;
                        option.textContent = `${data.display_time} (${data.available} slots available)`;
                        
                        // Add to appropriate group
                        if (time < '12:00:00') {
                            morningGroup.appendChild(option);
                        } else {
                            afternoonGroup.appendChild(option);
                        }
                    }
                });

                // Add groups to select
                if (morningGroup.children.length > 0) timeSlotSelect.appendChild(morningGroup);
                if (afternoonGroup.children.length > 0) timeSlotSelect.appendChild(afternoonGroup);

                // Enable select
                timeSlotSelect.disabled = false;

                // If no slots available
                if (timeSlotSelect.options.length <= 1) {
                    timeSlotSelect.innerHTML = '<option value="">No available slots for this date</option>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                timeSlotSelect.innerHTML = '<option value="">Error loading time slots</option>';
                timeSlotSelect.disabled = false;
            });
        });

        // Add form submit validation
        const appointmentForm = document.getElementById('appointmentForm');
        appointmentForm.addEventListener('submit', function(e) {
            const selectedTime = timeSlotSelect.value;
            if (!selectedTime) {
                e.preventDefault();
                alert('Please select an available time slot');
                timeSlotSelect.focus();
            }
        });
    });
    </script>
</body>
</html>
