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
    <title>Doctors List - Cloud Nine Community Hospital</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <main class="main-content">
            <header>
                <div class="header-content">
                    <h1>Doctors List</h1>
                </div>
            </header>

            <!-- Doctor Management Section -->
            <section class="doctor-management">
                <div class="section-header">
                    <h2>Doctors</h2>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Doctor Name</th>
                                <th>Department</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM Doctors ORDER BY doctor_name";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>" . htmlspecialchars($row['doctor_name']) . "</td>
                                            <td>" . htmlspecialchars($row['department']) . "</td>
                                            <td>" . ($row['status'] == 1 ? 'Active' : 'Inactive') . "</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3'>No doctors found</td></tr>";
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
