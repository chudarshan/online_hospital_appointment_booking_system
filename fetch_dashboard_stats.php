<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "4semp");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

try {
    // Get total doctors
    $doctors_query = "SELECT COUNT(*) as total FROM Doctors";
    $doctors_result = $conn->query($doctors_query);
    $doctors_count = $doctors_result->fetch_assoc()['total'];

    // Get total appointments
    $appointments_query = "SELECT COUNT(*) as total FROM Appointments";
    $appointments_result = $conn->query($appointments_query);
    $appointments_count = $appointments_result->fetch_assoc()['total'];

    // Get total users
    $users_query = "SELECT COUNT(*) as total FROM users";
    $users_result = $conn->query($users_query);
    $users_count = $users_result->fetch_assoc()['total'];

    echo json_encode([
        'success' => true,
        'doctors' => $doctors_count,
        'appointments' => $appointments_count,
        'users' => $users_count
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close();
?> 