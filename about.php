<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Cloud Nine Community Hospital</title>
    <link rel="stylesheet" href="home.css">
    <style>
        .about-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .about-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('hospital-building.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 20px;
            text-align: center;
            margin-bottom: 50px;
        }

        .about-hero h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }

        .about-section {
            margin-bottom: 50px;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }

        .about-section h2 {
            color: #2ecc71;
            margin-bottom: 20px;
            font-size: 2em;
        }

        .about-section p {
            line-height: 1.8;
            color: #444;
            margin-bottom: 15px;
        }

        .mission-vision {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 40px 0;
        }

        .mission-box, .vision-box {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            border-left: 5px solid #2ecc71;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            text-align: center;
            margin: 40px 0;
        }

        .stat-box {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .stat-number {
            font-size: 2.5em;
            color: #2ecc71;
            font-weight: bold;
        }

        .stat-label {
            color: #666;
            margin-top: 10px;
        }

        .facilities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .facility-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .facility-item i {
            color: #2ecc71;
            margin-right: 15px;
            font-size: 1.5em;
        }

        @media (max-width: 768px) {
            .mission-vision {
                grid-template-columns: 1fr;
            }

            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .about-hero h1 {
                font-size: 2em;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Include your existing navigation header -->
    

    <div class="about-hero">
        <h1>About Cloud Nine Community Hospital</h1>
        <p>Providing Quality Healthcare Since 2010</p>
    </div>

    <div class="about-container">
        <div class="about-section">
            <h2>Our Story</h2>
            <p>Cloud Nine Community Hospital was established in 2010 with a vision to provide world-class healthcare services to the people of Khotang and surrounding regions. Over the years, we have grown from a small clinic to a comprehensive healthcare facility, serving thousands of patients annually.</p>
            <p>Located in the heart of Diktel, our hospital combines modern medical technology with compassionate care, ensuring that every patient receives the highest standard of treatment.</p>
        </div>

        <div class="about-section">
            <h2>Our Mission & Vision</h2>
            <div class="mission-vision">
                <div class="mission-box">
                    <h3>Our Mission</h3>
                    <p>To deliver exceptional healthcare services with compassion and expertise, ensuring the well-being of our community through innovative medical solutions and patient-centered care.</p>
                </div>
                <div class="vision-box">
                    <h3>Our Vision</h3>
                    <p>To be the leading healthcare provider in Eastern Nepal, recognized for excellence in patient care, medical innovation, and community service.</p>
                </div>
            </div>
        </div>

        <div class="about-section">
            <h2>Hospital Statistics</h2>
            <div class="stats-container">
                <div class="stat-box">
                    <div class="stat-number">30+</div>
                    <div class="stat-label">Doctors</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">100+</div>
                    <div class="stat-label">Staff Members</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">80+</div>
                    <div class="stat-label">Beds</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Emergency Service</div>
                </div>
            </div>
        </div>

        <div class="about-section">
            <h2>Our Facilities</h2>
            <div class="facilities-grid">
                <div class="facility-item">
                    <i class="fas fa-procedures"></i>
                    <span>Modern Operation Theaters</span>
                </div>
                <div class="facility-item">
                    <i class="fas fa-x-ray"></i>
                    <span>Advanced Diagnostic Center</span>
                </div>
                <div class="facility-item">
                    <i class="fas fa-ambulance"></i>
                    <span>24/7 Emergency Services</span>
                </div>
                <div class="facility-item">
                    <i class="fas fa-heartbeat"></i>
                    <span>Cardiac Care Unit</span>
                </div>
                <div class="facility-item">
                    <i class="fas fa-brain"></i>
                    <span>Neurology Department</span>
                </div>
                <div class="facility-item">
                    <i class="fas fa-baby"></i>
                    <span>Maternity Ward</span>
                </div>
            </div>
        </div>

        <div class="about-section">
            <h2>Our Values</h2>
            <p><strong>Excellence:</strong> We strive for excellence in everything we do, from patient care to medical procedures.</p>
            <p><strong>Compassion:</strong> We treat every patient with empathy, understanding, and respect.</p>
            <p><strong>Innovation:</strong> We continuously adopt new technologies and methods to improve our services.</p>
            <p><strong>Integrity:</strong> We maintain the highest standards of professional and ethical conduct.</p>
            <p><strong>Community:</strong> We are committed to serving and improving the health of our community.</p>
        </div>
    </div>

    <!-- Include your existing footer -->
    

</body>
</html> 