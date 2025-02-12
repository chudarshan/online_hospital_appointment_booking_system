<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adlogin.html");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "4semp");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments List - Cloud Nine Community Hospital</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .main-content {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header-content h1 {
            color: #333;
        }

        .header-content button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .header-content button:hover {
            background-color: #0056b3;
        }

        /* Appointments Table */
        .appointments {
            margin-top: 20px;
        }

        .section-header h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        td button {
            padding: 6px 12px;
            background-color: #ffc107;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }

        td button:hover {
            background-color: #e0a800;
        }

        /* Status Change Form */
        #statusChangeForm {
            width: 300px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        #statusChangeForm select, #statusChangeForm textarea {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        #statusChangeForm button {
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }

        #statusChangeForm button:hover {
            background-color: #0056b3;
        }

        /* Reason Field (visible only when 'Cancelled' is selected) */
        #reasonField {
            display: none;
        }

    </style>
</head>
<body>
    <div class="dashboard-container">
        <main class="main-content">
            <header>
                <div class="header-content">
                    <h1>Appointments List</h1>
                    <button onclick="showAddAppointmentForm()">Add Appointment</button>
                </div>
            </header>

            <section class="appointments">
                <div class="section-header">
                    <h2>All Appointments</h2>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Appointment ID</th>
                                <th>Patient Name</th>
                                <th>Appointment Date</th>
                                <th>Appointment Time</th>
                                <th>Department</th>
                                <th>Doctor ID</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="appointmentsList">
                            <?php
                            // Query to fetch all appointments
                            $sql = "SELECT appointment_id, patient_name, appointment_date, appointment_time, department, doctor_id, status FROM appointments ORDER BY appointment_date DESC";
                            $result = $conn->query($sql);
                            if ($result === false) {
                                echo "<tr><td colspan='8'>Error: " . htmlspecialchars($conn->error) . "</td></tr>";
                            } else if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr id='appointment_" . htmlspecialchars($row['appointment_id']) . "'>
                                            <td>" . htmlspecialchars($row['appointment_id']) . "</td>
                                            <td>" . htmlspecialchars($row['patient_name']) . "</td>
                                            <td>" . htmlspecialchars($row['appointment_date']) . "</td>
                                            <td>" . htmlspecialchars($row['appointment_time']) . "</td>
                                            <td>" . htmlspecialchars($row['department']) . "</td>
                                            <td>" . htmlspecialchars($row['doctor_id']) . "</td>
                                            <td id='status_" . htmlspecialchars($row['appointment_id']) . "'>" . htmlspecialchars($row['status']) . "</td>
                                            <td>
                                                <button onclick='editAppointment(" . htmlspecialchars($row['appointment_id']) . ")'>Edit</button>
                                                <button onclick='deleteAppointment(" . htmlspecialchars($row['appointment_id']) . ")'>Delete</button>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8'>No appointments found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

  <!-- Status Change Form -->
<div id="statusChangeForm" style="display: none;">
    <select id="statusSelect" onchange="toggleReasonField()">
        <option value="Completed">Completed</option>
        <option value="Cancelled">Cancelled</option>
    </select>
    <div id="reasonField" style="display: none;">
        <label for="cancelReason">Cancellation Reason:</label>
        <textarea id="cancelReason"></textarea>
    </div>
    <button onclick="updateStatus()">Update Status</button>
    <button onclick="closeStatusChangeForm()">Cancel</button>
</div>

    <script>
         var currentAppointmentId = null;

// Function to toggle the reason field based on selected status
function toggleReasonField() {
    var status = document.getElementById('statusSelect').value;
    var reasonField = document.getElementById('reasonField');
    
    if (status === 'Cancelled') {
        reasonField.style.display = 'block';
    } else {
        reasonField.style.display = 'none';
    }
}

// Function to edit an appointment and show the status change form
function editAppointment(appointmentId) {
    currentAppointmentId = appointmentId;
    document.getElementById('statusChangeForm').style.display = 'block';
    
    // Get current status and set it in the form
    var currentStatus = document.getElementById('status_' + appointmentId).innerText.trim();
    document.getElementById('statusSelect').value = currentStatus;
    toggleReasonField(); // Show/hide reason field based on current status
}

// Function to update the status
function updateStatus() {
    if (!currentAppointmentId) return;

    var status = document.getElementById('statusSelect').value;
    var reason = document.getElementById('cancelReason').value;

    // Validate reason if status is Cancelled
    if (status === 'Cancelled' && reason.trim() === '') {
        alert('Please provide a cancellation reason');
        return;
    }

    // Create form data
    var formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('appointment_id', currentAppointmentId);
    formData.append('status', status);
    if (status === 'Cancelled') {
        formData.append('reason', reason);
    }

    // Send update request
    fetch('manage_appointments.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Status updated successfully');
            location.reload(); // Reload to show changes
        } else {
            alert(data.message || 'Error updating status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating status');
    });

    // Close the form
    closeStatusChangeForm();
}

// Function to close the status change form
function closeStatusChangeForm() {
    document.getElementById('statusChangeForm').style.display = 'none';
    document.getElementById('cancelReason').value = '';
    currentAppointmentId = null;
}

// Function to delete appointment
function deleteAppointment(appointmentId) {
    if (confirm('Are you sure you want to delete this appointment?')) {
        fetch('manage_appointments.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=delete&appointment_id=' + appointmentId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || 'Error deleting appointment');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting appointment');
        });
    }
}
    </script>

</body>
</html>
