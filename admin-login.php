<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";
$database = "4semp";

// Database connection
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate inputs
    if (empty($username) || empty($password)) {
        echo "<script>
            alert('Please fill in all fields');
            window.location.href='adlogin.html';
        </script>";
        exit();
    }

    // Check if admin exists in the database
    $query = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        
        // Password verification (for plain text passwords)
        if ($password === $admin['password']) {
            // Save session data
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];

            echo "<script>
                window.location.href='dashboard.php';
            </script>";
            exit();
        } else {
            echo "<script>
                alert('Invalid password');
                window.location.href='adlogin.html';
            </script>";
        }
    } else {
        echo "<script>
            alert('Invalid username or password');
            window.location.href='adlogin.html';
        </script>";
    }
}
?>
