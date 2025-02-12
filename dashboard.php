<?php
session_start();

// Restrict access to logged-in admins only
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adlogin.html");
    exit();
}

// Include the database connection file
include("admin-login-d.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Cloud Nine Community Hospital</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="logo">
                <img src="logo.png" alt="Hospital Logo">
                <h2>Admin Panel</h2>
            </div>
            <ul class="nav-links">
                <li class="active"><a href="#dashboard"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
                <li><a href="show_doctors.php"><i class="fas fa-user-md"></i>Doctors</a></li>
                <li><a href="show_appointments.php"><i class="fas fa-calendar-check"></i>Appointments</a></li>
                <li><a href="show_users.php"><i class="fas fa-users"></i>Users</a></li>
                <li><a href="#settings"><i class="fas fa-cog"></i>Settings</a></li>
                <li class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <div class="header-content">
                    <h1>Dashboard</h1>
                    <div class="admin-profile">
                        <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
                        <img src="isagi.jpg" alt="Admin">
                    </div>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <i class="fas fa-user-md"></i>
                    <div class="stat-info">
                        <h3>Total Doctors</h3>
                        <span class="count" id="doctorCount">Loading...</span>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-calendar-check"></i>
                    <div class="stat-info">
                        <h3>Appointments</h3>
                        <span class="count" id="appointmentCount">Loading...</span>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <div class="stat-info">
                        <h3>Total Users</h3>
                        <span class="count" id="userCount">Loading...</span>
                    </div>
                </div>
            </div>

            <!-- Doctor Management Section -->
            <section class="doctor-management">
                <div class="section-header">
                    <h2>Doctor Management</h2>
                    <button class="add-btn" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Add New Doctor
                    </button>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Doctor Name</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="doctorsList">
                            <?php
                            try {
                                $sql = "SELECT * FROM Doctors ORDER BY doctor_name";
                                $result = $conn->query($sql);
                                
                                if ($result) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td>".htmlspecialchars($row['doctor_name'])."</td>
                                                <td>".htmlspecialchars($row['department'])."</td>
                                                <td>".($row['status'] == 1 ? '<span class="status-active">Active</span>' : '<span class="status-inactive">Inactive</span>')."</td>
                                                <td>
                                                    <button onclick='editDoctor(".$row['doctor_id'].")' class='edit-btn'>Edit</button>
                                                    <button onclick='deleteDoctor(".$row['doctor_id'].")' class='delete-btn'>Delete</button>
                                                </td>
                                            </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No doctors found</td></tr>";
                                }
                            } catch (Exception $e) {
                                echo "<tr><td colspan='4'>Error: " . $e->getMessage() . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadDoctors();
        });
    </script>
</body>
</html>

    <!-- Add Doctor Modal -->
    <div id="addDoctorModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddModal()">&times;</span>
            <h2>Add New Doctor</h2>
            <form id="addDoctorForm">
                <div class="form-group">
                    <label>Doctor Name</label>
                    <input type="text" name="doctor_name" required>
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <select name="department" required>
                        <option value="">Select Department</option>
                        <option value="cardiology">Cardiology</option>
                        <option value="neurology">Neurology</option>
                        <option value="pediatrics">Pediatrics</option>
                        <option value="orthopedics">Orthopedics</option>
                        <option value="dermatology">Dermatology</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="submit-btn">Add Doctor</button>
            </form>
        </div>
    </div>

    <!-- Add Edit Doctor Modal -->
    <div id="editDoctorModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Doctor</h2>
            <form id="editDoctorForm">
                <input type="hidden" id="edit_doctor_id" name="doctor_id">
                <div class="form-group">
                    <label>Doctor Name</label>
                    <input type="text" id="edit_doctor_name" name="doctor_name" required>
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <select id="edit_department" name="department" required>
                        <option value="cardiology">Cardiology</option>
                        <option value="neurology">Neurology</option>
                        <option value="pediatrics">Pediatrics</option>
                        <option value="orthopedics">Orthopedics</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select id="edit_status" name="status" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="submit-btn">Update Doctor</button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addDoctorModal').style.display = 'block';
        }

        function closeAddModal() {
            document.getElementById('addDoctorModal').style.display = 'none';
        }

        // Load dashboard statistics
        function loadDashboardStats() {
            fetch('fetch_dashboard_stats.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('doctorCount').textContent = data.doctors;
                    document.getElementById('appointmentCount').textContent = data.appointments;
                    document.getElementById('userCount').textContent = data.users;
                });
        }

        // Load doctors list
        function loadDoctors() {
            fetch('manage_doctors.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=list'
            })
            .then(response => response.json())
            .then(doctors => {
                const tbody = document.getElementById('doctorsList');
                tbody.innerHTML = '';
                doctors.forEach(doctor => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${doctor.doctor_name}</td>
                            <td>${doctor.department}</td>
                            <td>${doctor.email}</td>
                            <td>
                                <button onclick="editDoctor(${doctor.doctor_id})">Edit</button>
                                <button onclick="deleteDoctor(${doctor.doctor_id})">Delete</button>
                            </td>
                        </tr>
                    `;
                });
            });
        }

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardStats();
            loadDoctors();
        });

        // Doctor management functions
        function editDoctor(doctorId) {
            // Fetch doctor details
            fetch('manage_doctors.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get&id=' + doctorId
            })
            .then(response => response.json())
            .then(doctor => {
                // Populate the edit form
                document.getElementById('edit_doctor_id').value = doctor.doctor_id;
                document.getElementById('edit_doctor_name').value = doctor.doctor_name;
                document.getElementById('edit_department').value = doctor.department;
                document.getElementById('edit_email').value = doctor.email;
                document.getElementById('edit_status').value = doctor.status;
                
                // Show the modal
                document.getElementById('editDoctorModal').style.display = 'block';
            });
        }

        function deleteDoctor(doctorId) {
            if(confirm('Are you sure you want to delete this doctor?')) {
                fetch('manage_doctors.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=delete&id=' + doctorId
                })
                .then(response => response.json())
                .then(result => {
                    if(result.success) {
                        alert('Doctor deleted successfully');
                        loadDoctors(); // Refresh the list
                        loadDashboardStats(); // Update the stats
                    } else {
                        alert('Error deleting doctor: ' + result.message);
                    }
                });
            }
        }

        function closeEditModal() {
            document.getElementById('editDoctorModal').style.display = 'none';
        }

        // Function to load doctors list
        function loadDoctors() {
            fetch('manage_doctors.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=list'
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('doctorsList');
                tbody.innerHTML = '';
                data.forEach(doctor => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${doctor.doctor_name}</td>
                            <td>${doctor.department}</td>
                            <td>${doctor.status == 1 ? '<span class="status-active">Active</span>' : '<span class="status-inactive">Inactive</span>'}</td>
                            <td>
                                <button onclick="editDoctor(${doctor.doctor_id})" class="edit-btn">Edit</button>
                                <button onclick="deleteDoctor(${doctor.doctor_id})" class="delete-btn">Delete</button>
                            </td>
                        </tr>
                    `;
                });
            });
        }

        // Handle edit form submission
        document.getElementById('editDoctorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'update');

            fetch('manage_doctors.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert('Doctor updated successfully');
                    closeEditModal();
                    loadDoctors();
                    loadDashboardStats();
                } else {
                    alert('Error updating doctor: ' + result.message);
                }
            });
        });

        // Handle add form submission
        document.getElementById('addDoctorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'add');

            fetch('manage_doctors.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if(result.success) {
                    alert('Doctor added successfully');
                    this.reset();
                    closeAddModal();
                    loadDoctors();
                    loadDashboardStats();
                } else {
                    alert('Error adding doctor: ' + result.message);
                }
            });
        });

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardStats();
            loadDoctors();
        });

        // When clicking outside the modal, close it
        window.onclick = function(event) {
            if (event.target == document.getElementById('addDoctorModal')) {
                closeAddModal();
            }
            if (event.target == document.getElementById('editDoctorModal')) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>

<?php
// Close the connection at the end of the file
$conn->close();
?>     

