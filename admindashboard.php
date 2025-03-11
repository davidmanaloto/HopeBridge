<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="adminpage.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div id="toast" data-message="Your message here" data-type="success" style="display: none;"></div>
    
    <nav class="nav-menu">
        <div class="logo-container">
            <a href="adminpage.html">
                <img src="hopebridge.jpg" alt="Company Logo" class="logo">
            </a>
            <h1 class="site-title">HopeBridge</h1>
        </div>
        <div class="menu-sidebar">
            <a href="admindashboard.php" class="nav-link home"><ion-icon name="home-outline"></ion-icon> Home</a>
            <a href="donations.php" class="nav-link user-management"><ion-icon name="people-outline"></ion-icon>Donations</a>
            <a href="verifyuser.php" class="nav-link user-management"><ion-icon name="people-outline"></ion-icon> Verify</a>
            <a href="user-management.php" class="nav-link user-management"><ion-icon name="people-outline"></ion-icon> User Management</a>
            <!--<a href="reports.html" class="nav-link reports"><ion-icon name="document-text-outline"></ion-icon> Reports</a>-->
            <a href="addorgs.php" class="nav-link reports"><ion-icon name="document-text-outline"></ion-icon> Add Orgs</a><!-- Added -->
            <a href="organizations.php" class="nav-link reports"><ion-icon name="document-text-outline"></ion-icon> Organizations</a>
            <!--<a href="settings.html" class="nav-link settings"><ion-icon name="settings-outline"></ion-icon> Settings</a>-->
            <a href="logout.php" class="nav-link logout"><ion-icon name="log-out-outline"></ion-icon> Log Out</a>
            </div>
    </nav>

    <div class="dashboard-container">
        <main>
            <div class="overview-container"></div>
            
            <!-- <div class="chart-container">
                <h2 style="text-align: center;">Disaster Statistics</h2>
                <div id="chartContainer">
                    <div class="chart-wrapper">
                        <canvas id="barChart"></canvas>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>
            </div> -->

            <!--<div class="donation-container">
                <div class="header">
                    <h2>Recent Activity</h2>
                    <a href="#">View all</a>
                </div>
                <table id="activityTable">
                    <thead>
                        <tr>
                            <th>Page</th>
                            <th>Date & Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="activityTableBody">
                        Recent activities will be dynamically inserted here
                    </tbody>
                </table>
            </div>-->

            <!--<footer class="footer">
                <div class="footer-content">
                    <div class="about-us">
                        <h3>About Us</h3>
                        <p>We are dedicated to providing timely information and support during disasters. Our mission is to help communities prepare for and respond to emergencies effectively.</p>
                        <h4>Contact Us</h4>
                        <div class="contact-links">
                            <p>
                                <a href="mailto:info@example.com" class="contact-link">
                                    <ion-icon name="mail-outline"></ion-icon> info@example.com
                                </a>
                            </p>
                            <p>
                                <a href="tel:+1234567890" class="contact-link">
                                    <ion-icon name="call-outline"></ion-icon> +1 234 567 890
                                </a>
                            </p>
                            <p>
                                <a href="#" class="contact-link">
                                    <ion-icon name="logo-facebook"></ion-icon> Facebook
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="feedback">
                        <h3>Feedback</h3>
                        <p>We value your feedback! Please let us know how we can improve our services.</p>
                        <a href="feedback.html" class="feedback-link">Give Feedback</a>
                    </div>
                </div>
            </footer>-->
    
    <script src="main.js"></script>
</body>
</html>