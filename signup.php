<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "4semp");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate that all fields are present and not empty
    $required_fields = ['fullname', 'email', 'username', 'password', 'confirm_password'];
    $all_fields_present = true;
    
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            $all_fields_present = false;
            break;
        }
    }

    if (!$all_fields_present) {
        echo "<script>
            alert('Please fill in all required fields');
            window.location.href='login.html';
        </script>";
        exit();
    }

    // Get and sanitize form data
    $fullname = mysqli_real_escape_string($conn, trim($_POST['fullname']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>
            alert('Passwords do not match');
            window.location.href='login.html';
        </script>";
        exit();
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Check if email or username already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
            alert('Email or Username already exists');
            window.location.href='login.html';
        </script>";
    } else {
        // Insert the user into the database
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, username, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullname, $email, $username, $password_hash);

        if ($stmt->execute()) {
            echo "<script>
                alert('Sign-Up successful! Please login.');
                window.location.href='login.html';
            </script>";
        } else {
            echo "<script>
                alert('Error: " . $stmt->error . "');
                window.location.href='login.html';
            </script>";
        }
    }
    $stmt->close();
}

$conn->close();
?>