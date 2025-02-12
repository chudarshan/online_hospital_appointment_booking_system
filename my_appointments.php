<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - Cloud Nine Community Hospital</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="appointments-container">
        <div class="header-actions">
            <h2>My Appointments</h2>
            <button onclick="window.print()" class="print-button">
                <i class="fas fa-print"></i> Print Appointments
            </button>
        </div>
        <div class="appointments-list">
            <?php
            // Database connection
            $conn = new mysqli("localhost", "root", "", "4semp");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Get email directly from session
            $user_email = $_SESSION['email'];

            // Get all appointments where:
            // 1. User is the patient (email matches) OR
            // 2. User booked for someone else (booking_email matches)
            $sql = "SELECT a.*, d.doctor_name 
                   FROM appointments a 
                   LEFT JOIN doctors d ON a.doctor_id = d.doctor_id 
                   WHERE a.email = ? OR a.booking_email = ?
                   ORDER BY a.appointment_date DESC, a.appointment_time DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $user_email, $user_email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $isBookedForOthers = ($row['email'] !== $user_email);
                    ?>
                    <div class="appointment-card">
                        <div class="appointment-header">
                            <span class="appointment-date">
                                <?php echo date('F j, Y', strtotime($row['appointment_date'])); ?>
                            </span>
                            <span class="appointment-time">
                                <?php echo date('g:i A', strtotime($row['appointment_time'])); ?>
                            </span>
                            <?php if ($isBookedForOthers) { ?>
                                <span class="booked-for-badge">
                                    Booked for <?php echo htmlspecialchars($row['patient_name']); ?>
                                </span>
                            <?php } ?>
                            <span class="appointment-status status-<?php echo strtolower($row['status'] ?? 'scheduled'); ?>">
                                <?php echo ucfirst($row['status'] ?? 'Scheduled'); ?>
                            </span>
                        </div>
                        <div class="appointment-details">
                            <div class="detail-group">
                                <div class="detail-label">Appointment ID</div>
                                <div class="detail-value">#<?php echo htmlspecialchars($row['appointment_id']); ?></div>
                            </div>
                            <div class="detail-group">
                                <div class="detail-label">Patient Name</div>
                                <div class="detail-value"><?php echo htmlspecialchars($row['patient_name']); ?></div>
                            </div>
                            <div class="detail-group">
                                <div class="detail-label">Patient Email</div>
                                <div class="detail-value"><?php echo htmlspecialchars($row['email']); ?></div>
                            </div>
                            <?php if ($isBookedForOthers) { ?>
                                <div class="detail-group">
                                    <div class="detail-label">Booked By</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($row['booking_email']); ?></div>
                                </div>
                            <?php } ?>
                            <div class="detail-group">
                                <div class="detail-label">Phone</div>
                                <div class="detail-value"><?php echo htmlspecialchars($row['phone']); ?></div>
                            </div>
                            <div class="detail-group">
                                <div class="detail-label">Department</div>
                                <div class="detail-value"><?php echo htmlspecialchars($row['department']); ?></div>
                            </div>
                            <div class="detail-group">
                                <div class="detail-label">Doctor</div>
                                <div class="detail-value">
                                    <?php echo htmlspecialchars($row['doctor_name'] ?? 'Not assigned'); ?>
                                </div>
                            </div>
                            <div class="detail-group">
                                <div class="detail-label">Reason for Visit</div>
                                <div class="detail-value"><?php echo htmlspecialchars($row['reason']); ?></div>
                            </div>
                            <?php if ($row['status'] === 'Cancelled' && !empty($row['cancel_reason'])) { ?>
                                <div class="detail-group cancellation-details">
                                    <div class="detail-label">Cancellation Reason</div>
                                    <div class="detail-value cancel-reason">
                                        <?php echo htmlspecialchars($row['cancel_reason']); ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="detail-group">
                                <div class="detail-label">Booked On</div>
                                <div class="detail-value">
                                    <?php echo date('F j, Y g:i A', strtotime($row['created_at'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<div class='no-appointments'><p>No appointments found.</p></div>";
            }
            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>

    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .appointments-container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            height: 85vh; /* Set container height */
            overflow: hidden; /* Hide overflow */
            display: flex;
            flex-direction: column;
        }

        .appointments-container h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
            font-size: 24px;
            text-align: center;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .appointments-list {
            overflow-y: auto; /* Enable vertical scrolling */
            padding-right: 15px; /* Space for scrollbar */
            margin-right: -15px; /* Compensate for padding */
            flex: 1; /* Take remaining space */
        }

        /* Custom scrollbar */
        .appointments-list::-webkit-scrollbar {
            width: 8px;
        }

        .appointments-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .appointments-list::-webkit-scrollbar-thumb {
            background: #3498db;
            border-radius: 4px;
        }

        .appointments-list::-webkit-scrollbar-thumb:hover {
            background: #2980b9;
        }

        .appointment-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid #edf2f7;
        }

        .appointment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .appointment-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #edf2f7;
        }

        .appointment-date {
            font-weight: 600;
            color: #2d3748;
            font-size: 1.1em;
            background: #e3f2fd;
            padding: 8px 12px;
            border-radius: 8px;
        }

        .appointment-time {
            color: #4a5568;
            font-weight: 500;
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 8px;
        }

        .appointment-status {
            margin-left: auto;
            padding: 8px 16px;
            border-radius: 20px;
            color: white;
            font-weight: 500;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-scheduled {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }

        .status-completed {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
        }

        .status-cancelled {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .detail-group {
            margin-bottom: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #edf2f7;
        }

        .detail-group:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 6px;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            color: #2d3748;
            font-size: 1.05em;
        }

        .booked-for-badge {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 500;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(52, 152, 219, 0.2);
        }

        .cancellation-details {
            background: #fff5f5;
            padding: 20px;
            border-radius: 12px;
            margin-top: 15px;
            border-left: 4px solid #e74c3c;
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.1);
        }

        .cancel-reason {
            color: #c0392b;
            font-weight: 500;
            font-size: 1.05em;
            line-height: 1.5;
        }

        .no-appointments {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 12px;
            margin: 20px 0;
            color: #718096;
            font-size: 1.1em;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }

        /* Medical icons for different sections */
        .detail-label::before {
            font-family: "Font Awesome 5 Free";
            margin-right: 8px;
            color: #3498db;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .appointments-container {
                margin: 10px;
                padding: 15px;
                height: 90vh;
            }

            .appointment-header {
                flex-wrap: wrap;
                gap: 10px;
            }

            .appointment-status {
                width: 100%;
                text-align: center;
                margin: 10px 0 0 0;
            }
        }

        /* Animation for new appointments */
        @keyframes slideIn {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .appointment-card {
            animation: slideIn 0.3s ease-out;
        }

        /* New and modified styles */
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }

        .print-button {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .print-button:hover {
            background: linear-gradient(135deg, #2980b9, #2475a7);
            transform: translateY(-2px);
        }

        .appointments-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(600px, 1fr));
            gap: 20px;
            padding: 20px;
            overflow-y: auto;
        }

        .appointment-card {
            height: fit-content;
            break-inside: avoid;
        }

        /* Print styles */
        @media print {
            body {
                background: white;
                margin: 0;
                padding: 20px;
            }

            .appointments-container {
                height: auto;
                box-shadow: none;
                margin: 0;
                padding: 0;
            }

            .print-button {
                display: none;
            }

            .appointments-list {
                display: block;
                overflow: visible;
            }

            .appointment-card {
                break-inside: avoid;
                page-break-inside: avoid;
                margin-bottom: 20px;
                border: 1px solid #ddd;
            }

            .appointment-status {
                border: 1px solid #000;
            }

            .status-scheduled {
                background: #fff !important;
                color: #3498db !important;
                border-color: #3498db !important;
            }

            .status-completed {
                background: #fff !important;
                color: #2ecc71 !important;
                border-color: #2ecc71 !important;
            }

            .status-cancelled {
                background: #fff !important;
                color: #e74c3c !important;
                border-color: #e74c3c !important;
            }

            .booked-for-badge {
                background: #fff !important;
                color: #3498db !important;
                border: 1px solid #3498db !important;
            }

            .cancellation-details {
                border: 1px solid #e74c3c !important;
            }

            /* Ensure good contrast for printing */
            .detail-label {
                color: #000 !important;
            }

            .detail-value {
                color: #333 !important;
            }

            /* Add page information */
            @page {
                size: landscape;
                margin: 2cm;
            }

            /* Add header and footer for printed pages */
            .appointment-card::after {
                content: "Cloud Nine Community Hospital - Appointment Details";
                font-size: 8pt;
                color: #666;
                text-align: center;
                width: 100%;
                position: absolute;
                bottom: 0;
                left: 0;
            }
        }

        /* Responsive design updates */
        @media (max-width: 1200px) {
            .appointments-list {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .header-actions {
                flex-direction: column;
                gap: 15px;
            }

            .print-button {
                width: 100%;
                justify-content: center;
            }

            .appointments-list {
                padding: 10px;
            }

            .appointment-card {
                margin-bottom: 15px;
            }
        }

        /* Animation for print button */
        .print-button i {
            transition: transform 0.3s ease;
        }

        .print-button:hover i {
            transform: translateY(-2px);
        }

        /* Additional styles for better landscape layout */
        .appointment-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .detail-group {
            margin: 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: none;
        }

        .cancellation-details {
            grid-column: 1 / -1;
        }

        /* Hover effect for details */
        .detail-group:hover {
            background: #e3f2fd;
            transition: background-color 0.3s ease;
        }

        /* Status indicator improvements */
        .appointment-status {
            position: relative;
            overflow: hidden;
        }

        .appointment-status::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .appointment-status:hover::before {
            transform: translateX(0);
        }
    </style>
</body>
</html> 
