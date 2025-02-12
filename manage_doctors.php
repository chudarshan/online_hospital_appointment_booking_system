<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "4semp");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed']);
    exit();
}

function validateEmail($email) {
    // Check basic email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    // Check for valid domain extensions
    $validDomains = array('gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com');
    $domain = substr(strrchr($email, "@"), 1);
    return in_array(strtolower($domain), $validDomains);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    switch($action) {
        case 'list':
            $sql = "SELECT * FROM Doctors ORDER BY doctor_name";
            $result = $conn->query($sql);
            $doctors = [];
            while($row = $result->fetch_assoc()) {
                $doctors[] = $row;
            }
            echo json_encode($doctors);
            break;

        case 'get':
            $id = $_POST['id'];
            $stmt = $conn->prepare("SELECT * FROM Doctors WHERE doctor_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            echo json_encode($result->fetch_assoc());
            break;

        case 'add':
            $name = $_POST['doctor_name'];
            $department = $_POST['department'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $status = $_POST['status'];

            // Validate email
            if (!validateEmail($email)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please enter a valid email address (gmail.com, yahoo.com, hotmail.com, or outlook.com domains only)'
                ]);
                exit();
            }

            // Check for existing doctor with same name, email, or phone
            $check_stmt = $conn->prepare("SELECT * FROM Doctors WHERE doctor_name = ? OR email = ? OR phone = ?");
            $check_stmt->bind_param("sss", $name, $email, $phone);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            if ($result->num_rows > 0) {
                $existing = $result->fetch_assoc();
                $duplicate_field = '';
                if ($existing['doctor_name'] == $name) $duplicate_field = 'name';
                else if ($existing['email'] == $email) $duplicate_field = 'email';
                else if ($existing['phone'] == $phone) $duplicate_field = 'phone number';
                
                echo json_encode([
                    'success' => false,
                    'message' => "A doctor with this $duplicate_field already exists."
                ]);
                exit();
            }

            // Validate Nepal phone number format
            if (!preg_match("/^(984|985|986|974|975|976|980|981|982|961|988|972|963)[0-9]{7}$/", $phone)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Please enter a valid Nepal phone number (e.g., 9841234567)'
                ]);
                exit();
            }

            $stmt = $conn->prepare("INSERT INTO Doctors (doctor_name, department, email, phone, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $name, $department, $email, $phone, $status);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => $stmt->error]);
            }
            break;

        case 'update':
            $id = $_POST['doctor_id'];
            $name = $_POST['doctor_name'];
            $department = $_POST['department'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $status = $_POST['status'];

            // Validate email
            if (!validateEmail($email)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please enter a valid email address (gmail.com, yahoo.com, hotmail.com, or outlook.com domains only)'
                ]);
                exit();
            }

            // Check for existing doctor with same name, email, or phone (excluding current doctor)
            $check_stmt = $conn->prepare("SELECT * FROM Doctors WHERE (doctor_name = ? OR email = ? OR phone = ?) AND doctor_id != ?");
            $check_stmt->bind_param("sssi", $name, $email, $phone, $id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            if ($result->num_rows > 0) {
                $existing = $result->fetch_assoc();
                $duplicate_field = '';
                if ($existing['doctor_name'] == $name) $duplicate_field = 'name';
                else if ($existing['email'] == $email) $duplicate_field = 'email';
                else if ($existing['phone'] == $phone) $duplicate_field = 'phone number';
                
                echo json_encode([
                    'success' => false,
                    'message' => "A doctor with this $duplicate_field already exists."
                ]);
                exit();
            }

            // Validate Nepal phone number format
            if (!preg_match("/^(984|985|986|974|975|976|980|981|982|961|988|972|963)[0-9]{7}$/", $phone)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Please enter a valid Nepal phone number (e.g., 9841234567)'
                ]);
                exit();
            }

            $stmt = $conn->prepare("UPDATE Doctors SET doctor_name = ?, department = ?, email = ?, phone = ?, status = ? WHERE doctor_id = ?");
            $stmt->bind_param("ssssii", $name, $department, $email, $phone, $status, $id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => $stmt->error]);
            }
            break;

        case 'delete':
            $id = $_POST['id'];
            $stmt = $conn->prepare("DELETE FROM Doctors WHERE doctor_id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => $stmt->error]);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
}

$conn->close();

// Add client-side validation in HTML
echo "
<script>
function validateDoctorForm() {
    const email = document.getElementById('doctor_email').value;
    const emailRegex = /^[a-zA-Z0-9._%+-]+@(gmail|yahoo|hotmail|outlook)\.com$/i;
    
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address (gmail.com, yahoo.com, hotmail.com, or outlook.com domains only)');
        return false;
    }
    return true;
}
</script>
";
?> 