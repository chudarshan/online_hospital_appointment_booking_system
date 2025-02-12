<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "4semp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            // Redirect to the homepage
            echo "<script>
                localStorage.setItem('username', '" . $username . "');
                localStorage.setItem('user_id', '" . $user['id'] . "');
                localStorage.setItem('email', '" . $user['email'] . "');
                alert('Login successful!');
                window.location.href='home.php';
            </script>";
            exit();
        } else {
            echo "<script>
                alert('Invalid password');
                window.location.href='login.html';
            </script>";
        }
    } else {
        echo "<script>
            alert('No user found. Please sign up.');
            window.location.href='login.html';
        </script>";
    }

    $stmt->close();
} else {
    // Redirect if already logged in
    if (isset($_SESSION['username'])) {
        echo "<script>
            alert('You are already logged in!');
            window.location.href='home.php';
        </script>";
        exit();
    }
}

$conn->close();
?>
