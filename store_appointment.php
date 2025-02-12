<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "4semp");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed']);
    exit();
}

try {
    // Get the booking user's email from the session
    $booking_email = $_SESSION['email']; // Email of logged-in user
    
    // Get form data
    $patient_name = $_POST['patientName'];
    $patient_email = $_POST['email']; // Email entered in the form
    $phone = $_POST['phone'];
    $appointment_date = $_POST['appointmentDate'];
    $appointment_time = $_POST['timeSlot'];
    $doctor_id = $_POST['doctor'];
    $department = $_POST['department'];
    $reason = $_POST['reason'];
    $status = 'scheduled';

    // Insert appointment with both emails
    $sql = "INSERT INTO Appointments (patient_name, email, booking_email, phone, appointment_date, 
            appointment_time, doctor_id, department, reason, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", 
        $patient_name, 
        $patient_email, 
        $booking_email,
        $phone, 
        $appointment_date, 
        $appointment_time, 
        $doctor_id, 
        $department, 
        $reason, 
        $status
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Appointment booked successfully']);
    } else {
        throw new Exception('Error booking appointment');
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
