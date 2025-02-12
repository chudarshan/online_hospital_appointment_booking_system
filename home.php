<?php
session_start();
require_once 'get_doctors.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Appointment</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body>
    <!-- Navigation Bar -->
    <header>
        <div class="navbar">
            
            <nav>
                <a href="home.php" class="logo">
                    <img src="logo.png" alt="Logo" />
                </a>
                <label class="logo">Cloud Nine Community Hospital</label>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="#list-container" id="nav-doctors">Doctors</a></li>
                    <li><a href="my_appointments.php">Appointments</a></li>
                    <li><a href="#" id="contact">contact</a></li>
                    <li id="loginNav"><a href="login.html">Login</a></li>
                    <li id="logoutNav" style="display: none;"><a href="#" onclick="logout()">Logout</a></li>
                </ul>
                <div id="doctor-list" style="display:none;">
                    <ul>
                      <li>Dr. Krishna Dhimal</li>
                      <li>Dr. Bishnu Rijal</li>
                      <li>Dr. Arjun Shrestha</li>
                    </ul>
                  </div>
            </nav>
        </div>
    </header>
    <!-- hero Section -->
    <section class="hero">
        <div class="box">
            <h2>Welcome to Cloud Nine Community Hospital</h2>
            <p>Select a Doctor and Book Your Appointment</p>
        </div>
    </section>

    <!-- Doctors List Section -->
    <section class="doctors-section">
        <div id="doctors-container" class="doctors-container">
    <!-- Doctors will be dynamically loaded here -->
</div>
<script>
    // Load doctors dynamically from the database
    function loadDoctors() {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "fetch_doctors.php", true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                document.getElementById("doctors-container").innerHTML = xhr.responseText;
            } else {
                document.getElementById("doctors-container").innerHTML = "<p>Error loading doctors. Please try again later.</p>";
            }
        };
        xhr.send();
    }

    // Call the function to load doctors when the page loads
    document.addEventListener("DOMContentLoaded", loadDoctors);
</script>

    </section>


<script>
    // Load doctors dynamically from the database
    function loadDoctors() {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "fetch_doctorslist.php", true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                document.getElementById("doctor-list-container").innerHTML = xhr.responseText;
            } else {
                document.getElementById("doctors-list-container").innerHTML = "<p>Error loading doctors. Please try again later.</p>";
            }
        };
        xhr.send();
    }

    // Call the function to load doctors when the page loads
    document.addEventListener("DOMContentLoaded", loadDoctors);
</script>

    <!-- Footer -->

    <footer id="information">
        <div class="footer-container">
            <!-- Logo and Contact Information -->
            <div class="footer-section contact-info">
                <img src="footerlogo.png" alt="Hospital Logo" class="footer-logo">
                <p><strong>Cloud Nine Community Hospital</strong></p>
                <p>Diktel Khotang-Nepal</p>
                <p><a href="tel:036-420640">036-420184</a> <li>contact us</li></p>
            </div>
    
            <!-- Centers of Excellence -->
            <div class="footer-section">
                <h3>Centers of Excellence</h3>
                <ul>
                    <li>Cancer Center</li>
                    <li>Heart & Vascular Center</li>
                    <li>The Lung Center</li>
                    <li>Neurosciences Center</li>
                    <li>Orthopaedic & Arthritis Center</li>
                    <li>Primary Care Center</li>
                    <li>Women's Health Center</li>
                </ul>
            </div>
    
            <!-- Clinical Departments -->
            <div class="footer-section">
                <h3>Clinical Departments</h3>
                <ul>
                    <li>Anesthesiology</li>
                    <li>Dermatology</li>
                    <li>Emergency Medicine</li>
                    <li>Neurology</li>
                    <li>Orthopaedic Surgery</li>
                </ul>
            </div>
    
            <!-- Quick Links -->
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="https://www.who.int/emergencies/diseases/novel-coronavirus-2019" target="_blank">COVID-19 Information</a></li>
                    <li><a href="https://www.mohp.gov.np/" target="_blank">Ministry of Health</a></li>
                    <li><a href="https://www.who.int/" target="_blank">World Health Organization</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <div class="rights">
        <p1>&copy; 2024 Cloud Nine Community Hospital | All Rights Reserved</p1>
     </div>
    
    

    <script>
    // Check login status when page loads
    document.addEventListener('DOMContentLoaded', function() {
        checkLoginStatus();
    });

    function checkLoginStatus() {
        const loggedIn = localStorage.getItem('username');
        const loginNav = document.getElementById('loginNav');
        const logoutNav = document.getElementById('logoutNav');
        const loginButton = document.getElementById('loginButton');

        if (loggedIn) {
            // User is logged in
            if (loginNav) loginNav.style.display = 'none';
            if (logoutNav) logoutNav.style.display = 'block';
            if (loginButton) loginButton.style.display = 'none';
        } else {
            // User is not logged in
            if (loginNav) loginNav.style.display = 'block';
            if (logoutNav) logoutNav.style.display = 'none';
            if (loginButton) loginButton.style.display = 'block';
        }
    }

    function checkLoginForAppointment() {
        const loggedIn = localStorage.getItem('username');
        if (!loggedIn) {
            alert('Please login to book an appointment');
            window.location.href = 'login.html';
        } else {
            window.location.href = 'appointment.html';
        }
    }

    function openAppointmentPage(doctorName, department) {
        const loggedIn = localStorage.getItem('username');
        if (!loggedIn) {
            alert('Please login to book an appointment');
            window.location.href = 'login.html';
            return;
        }
        
        const encodedDoctor = encodeURIComponent(doctorName);
        const encodedDepartment = encodeURIComponent(department);
        window.location.href = `appointment.php?doctor=${encodedDoctor}&department=${encodedDepartment}`;
    }

    function logout() {
        if (confirm('Are you sure you want to logout?')) {
            localStorage.clear();
            sessionStorage.clear();
            window.location.href = 'logout.php';
        }
    }
    </script>

<script>
    // Smooth scroll to the Doctors section
    document.getElementById('nav-doctors').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default anchor behavior
        document.getElementById('doctors-container').scrollIntoView({
            behavior: 'smooth'
        });
    });
</script>

<script>
    // Smooth scroll to the contact section
    document.getElementById('contact').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default anchor behavior
        document.getElementById('information').scrollIntoView({
            behavior: 'smooth'
        });
    });
</script>

    </body>
</html>
