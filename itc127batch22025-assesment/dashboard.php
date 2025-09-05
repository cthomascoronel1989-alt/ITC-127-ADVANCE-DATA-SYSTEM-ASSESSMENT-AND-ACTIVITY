<?php
include("session-checker.php"); // Ensures session is started and user is logged in
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard - Equipment Management System</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* Body and background */
body.dashboard-body {
    margin: 0;
    height: 100vh;
    background: url('bg.jpg') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Montserrat', sans-serif;
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center; 
    overflow-x: hidden;
    font-size: 0.8rem; /* smaller base font */
}

/* Stars animation */
.custom-container {
    position: absolute;
    width: 100%;
    height: 100%;
    z-index: 0;
}

.custom-circle-container {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    animation: twinkle 5s infinite;
}

@keyframes twinkle {
    0%,100% { opacity: 0.3; }
    50% { opacity: 1; }
}

/* Slide-in animations */
@keyframes slideFadeIn {
    0% { transform: translateY(-30px); opacity: 0; }
    100% { transform: translateY(0); opacity: 1; }
}

/* Dashboard card */
.dashboard-content {
    position: relative;
    z-index: 1;
    background: rgba(0,0,0,0.85); 
    padding: 20px 25px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 0 25px rgba(0,0,0,0.6);
    max-width: 900px;
    width: 90%;
    font-size: 0.8rem; /* smaller font inside */
    animation: slideFadeIn 1s ease forwards;
}

/* Headings */
.dashboard-content h1 {
    margin: 0 0 6px 0;
    font-size: 1.2rem; /* smaller */
    color: #fff;
}

.dashboard-content h2 {
    margin: 0 0 14px 0;
    font-weight: 400;
    color: #ddd;
    font-size: 0.95rem; /* smaller */
}

/* Buttons */
.dashboard-buttons {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
    margin: 15px 0;
}

.dashboard-buttons button {
    flex: 1 1 160px; /* uniform smaller buttons */
    padding: 8px 12px;
    font-size: 0.8rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    background-color: #e74c3c; 
    color: #fff;
    transition: 0.3s;
}

.dashboard-buttons button:hover {
    background-color: #c0392b; 
}

/* Logout link */
.logout-link {
    display: inline-block;
    margin-top: 15px;
    text-decoration: none;
    color: #fff;
    font-size: 0.8rem;
    font-weight: 500;
    transition: 0.2s;
}

.logout-link:hover {
    text-decoration: underline;
    color: #e74c3c;
}

/* Table styling */
.dashboard-table {
    width: 100%;
    margin-top: 10px;
    border-collapse: collapse;
    background: rgba(0,0,0,0.8);
    border-radius: 12px;
    font-size: 0.75rem; /* smaller font for table */
    box-shadow: 0 0 15px rgba(0,0,0,0.6);
    overflow: hidden;
}

.dashboard-table th, .dashboard-table td {
    padding: 8px 10px; /* tighter */
    text-align: left;
    color: #fff;
    border-bottom: 1px solid rgba(255,255,255,0.15);
}

.dashboard-table th {
    background: orange;
    font-weight: 600;
    color: #000; 
    font-size: 0.8rem;
}

.dashboard-table tr:nth-child(even) {
    background: rgba(255, 255, 255, 0.05); 
}

.dashboard-table tr:hover {
    background: rgba(255, 165, 0, 0.2);
    transition: 0.3s;
}
</style>
</head>
<body class="dashboard-body">

<div class="custom-container"></div>

<div class="dashboard-content">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <h2>Dashboard</h2>

    <div class="dashboard-buttons">
        <?php if ($_SESSION['usertype'] === 'ADMINISTRATOR'): ?>
            <button onclick="location.href='accounts-management.php'">Accounts</button>
            <button onclick="location.href='equipments.php'">Equipments</button>
            <button onclick="location.href='logs.php'">Logs</button>
        <?php elseif ($_SESSION['usertype'] === 'TECHNICAL'): ?>
            <button onclick="location.href='equipments.php'">Equipments</button>
        <?php elseif ($_SESSION['usertype'] === 'USER'): ?>
            <button onclick="location.href='view-equipments.php'">View Equipments</button>
        <?php endif; ?>
    </div>

    <!-- Table -->
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Equipment Name</th>
                <th>Category</th>
                <th>Status</th>
                <th>Location</th>
                <th>Last Checked</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Projector</td>
                <td>Electronics</td>
                <td>Available</td>
                <td>Main Hall</td>
                <td>2025-08-01</td>
            </tr>
            <tr>
                <td>Laptop</td>
                <td>Computers</td>
                <td>In Use</td>
                <td>Office 2</td>
                <td>2025-08-03</td>
            </tr>
            <tr>
                <td>Printer</td>
                <td>Electronics</td>
                <td>Maintenance</td>
                <td>Lab 1</td>
                <td>2025-07-28</td>
            </tr>
        </tbody>
    </table>

    <a href="logout.php" class="logout-link">Logout</a>
</div>

<script>
// Generate stars
const container = document.querySelector('.custom-container');
for (let i = 0; i < 100; i++) {
    const star = document.createElement('div');
    star.classList.add('custom-circle-container');
    const size = Math.random() * 2 + 1;
    star.style.width = `${size}px`;
    star.style.height = `${size}px`;
    star.style.left = `${Math.random() * 100}vw`;
    star.style.top = `${Math.random() * 100}vh`;
    star.style.animationDelay = `${Math.random() * 5}s`;
    container.appendChild(star);
}
</script>

</body>
</html>
