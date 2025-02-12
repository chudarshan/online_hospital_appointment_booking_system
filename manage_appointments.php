<?php
session_start();
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "4semp");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_status':
                if (!isset($_POST['appointment_id']) || !isset($_POST['status'])) {
                    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
                    exit();
                }

                $appointment_id = intval($_POST['appointment_id']);
                $status = $_POST['status'];
                $reason = isset($_POST['reason']) ? trim($_POST['reason']) : null;

                // Validate status
                if ($status === 'Cancelled' && empty($reason)) {
                    echo json_encode(['success' => false, 'message' => 'Cancellation reason is required']);
                    exit();
                }

                try {
                    if ($status === 'Cancelled') {
                        $sql = "UPDATE appointments SET status = ?, cancel_reason = ? WHERE appointment_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssi", $status, $reason, $appointment_id);
                    } else {
                        $sql = "UPDATE appointments SET status = ?, cancel_reason = NULL WHERE appointment_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("si", $status, $appointment_id);
                    }

                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
                    } else {
                        throw new Exception($stmt->error);
                    }
                    $stmt->close();
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()]);
                }
                break;

            case 'delete':
                if (!isset($_POST['appointment_id'])) {
                    echo json_encode(['success' => false, 'message' => 'Missing appointment ID']);
                    exit();
                }

                $appointment_id = intval($_POST['appointment_id']);
                $sql = "DELETE FROM appointments WHERE appointment_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $appointment_id);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Appointment deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error deleting appointment']);
                }
                $stmt->close();
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                break;
        }
    }
}

$conn->close();
?>
