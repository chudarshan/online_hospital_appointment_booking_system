<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adlogin.html");
    exit();
}

// Add database connection
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
    <title>Users List - Cloud Nine Community Hospital</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <main class="main-content">
            <header>
                <div class="header-content">
                    <h1>Users List</h1>
                </div>
            </header>

            <!-- Users Section -->
            <section class="users">
                <div class="section-header">
                    <h2>All Users</h2>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // SQL query for important fields
                            $sql = "SELECT id, fullname, email FROM users ORDER BY created_at DESC";

                            $result = $conn->query($sql);

                            // Check if the query was successful
                            if ($result === false) {
                                echo "<tr><td colspan='3'>Error: " . htmlspecialchars($conn->error) . "</td></tr>";
                            } else if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>" . htmlspecialchars($row['id']) . "</td>
                                            <td>" . htmlspecialchars($row['fullname']) . "</td>
                                            <td>" . htmlspecialchars($row['email']) . "</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3'>No users found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
<?php
$conn->close();
?>
