<?php
header('Content-Type: text/html; charset=utf-8');

// Database connection
$conn = new mysqli("localhost", "root", "", "4semp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch active doctors
$sql = "SELECT * FROM Doctors WHERE status = 1 ORDER BY doctor_name";
$result = $conn->query($sql);

echo "
<style>
    .doctors-title {
        text-align: center;
        font-size: 2.5em;
        color: #2c3e50;
        margin: 40px 0;
        font-weight: bold;
        width: 100%;
        display: block;
    }

    .appointment-specialists {
        background: linear-gradient(135deg, #f6f9fc 0%, #f1f4f9 100%);
        padding: 20px;
        height: 100vh;
        overflow: hidden;
    }

    .appointment-container {
        max-width: 1200px;
        margin: 0 auto;
        height: calc(100vh - 40px);
        display: flex;
        flex-direction: column;
    }
        

    .appointment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
        padding: 20px;
        overflow-y: auto;
        align-items: start;
        margin-bottom: 20px; /* Add space at bottom of grid */
    }

    .appointment-card {
        background: white;
        border-radius: 15px;
        overflow: visible;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        position: relative;
        height: 295px; /* Fixed height including space for button */
        margin-top: 60px;
        display: flex;
        flex-direction: column;
    }

    .appointment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }

    .appointment-header {
        background: #3498db;
        height: 71px;
        border-radius: 15px 15px 0 0;
    }

    .doctor-img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: white;
        border: 4px solid white;
        margin: -60px auto -19px;
        position: relative;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }

    .doctor-img i {
        font-size: 50px;
        color: #3498db;
    }

    .doctor-info {
        padding: 20px;
        text-align: center;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .doctor-name {
        font-size: 1.4em;
        color: black;
        margin: 10px 0;
        font-weight: 600;
    }

    .doctor-dept {
        color: #3498db; 
        font-size: 1.1em;
        font-weight: 500;
        margin: 5px 0;
        padding: 5px 15px;
        background: rgba(52, 152, 219, 0.12);
        border-radius: 15px;
        display: inline-block;
    }

    .book-appointment-btn {
        background: #2ecc71;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 25px;
        cursor: pointer;
        font-size: 1em;
        transition: all 0.3s ease;
        margin: 0px auto;
        width: 80%;
        position: relative;
        bottom: 10px;
    }

    .book-appointment-btn:hover {
        background: #3498db;
        transform: translateY(-2px);
    }

    /* Custom scrollbar */
    .appointment-grid::-webkit-scrollbar {
        width: 8px;
    }

    .appointment-grid::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .appointment-grid::-webkit-scrollbar-thumb {
        background: #2ecc71;
        border-radius: 4px;
    }

    .appointment-grid::-webkit-scrollbar-thumb:hover {
        background: #27ae60;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .appointment-card {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    @media (max-width: 768px) {
        .appointment-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 10px;
        }

        .appointment-card {
            height: 260px;
        }

        .doctor-img {
            width: 100px;
            height: 100px;
            margin: -50px auto 10px;
        }

        .doctor-img i {
            font-size: 40px;
        }
    }

    .specialist-header {
        background: #2ecc71; /* Changed from #3498db to green */
        height: 80px;
        border-radius: 15px 15px 0 0;
    }

    .specialist-img i {
        font-size: 50px;
        color: #2ecc71; /* Changed from #3498db to green */
    }

    .specialist-dept {
        color: #27ae60; /* Changed from #3498db to darker green */
        font-size: 1.1em;
        font-weight: 500;
        margin: 5px 0;
        padding: 5px 15px;
        background: rgba(46, 204, 113, 0.1); /* Changed from rgba(52, 152, 219, 0.1) to light green */
        border-radius: 15px;
        display: inline-block;
    }

    /* Custom scrollbar colors */
    .specialists-grid::-webkit-scrollbar-thumb {
        background: #2ecc71; /* Changed from #3498db to green */
        border-radius: 4px;
    }

    .specialists-grid::-webkit-scrollbar-thumb:hover {
        background: #27ae60; /* Changed from #2980b9 to darker green */
    }
</style>

<section class='appointment-specialists'>
    <div class='appointment-container'>
        <h2 class='doctors-title'>Our Doctors</h2>
        <div class='appointment-grid'>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='appointment-card'>
                <div class='appointment-header'></div>
                <div class='doctor-img'>
                    <i class='fas fa-user-md'></i>
                </div>
                <div class='doctor-info'>
                    <h3 class='doctor-name'>" . htmlspecialchars($row['doctor_name']) . "</h3>
                    <span class='doctor-dept'>" . htmlspecialchars($row['department']) . "</span>
                    <button class='book-appointment-btn' onclick='openAppointmentPage(\"" . 
                        htmlspecialchars($row['doctor_name']) . "\", \"" . 
                        htmlspecialchars($row['department']) . "\")'>
                        Book Appointment
                    </button>
                </div>
              </div>";
    }
} else {
    echo "<p style='text-align: center;'>No doctors available at the moment.</p>";
}

echo "</div></div></section>";

$conn->close();
?>
