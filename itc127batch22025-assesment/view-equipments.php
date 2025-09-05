<?php
require_once "config.php";
include("session-checker.php");

// Access control: only allow USER role
if ($_SESSION['usertype'] !== 'USER') {
    header("Location: dashboard.php");
    exit;
}

// Initialize search variables
$assetnumber = $_POST['assetnumber'] ?? '';
$serialnumber = $_POST['serialnumber'] ?? '';
$type = $_POST['type'] ?? '';
$department = $_POST['department'] ?? '';

// SQL query with filters
$sql = "SELECT assetnumber, serialnumber, type, manufacturer, yearmodel, description, branch, department, status, datecreated 
        FROM tblequipments 
        WHERE assetnumber LIKE ? 
        AND serialnumber LIKE ? 
        AND type LIKE ? 
        AND department LIKE ? 
        ORDER BY datecreated DESC";

$stmt = mysqli_prepare($link, $sql);
$assetTerm = "%$assetnumber%";
$serialTerm = "%$serialnumber%";
$typeTerm = "%$type%";
$departmentTerm = "%$department%";

mysqli_stmt_bind_param($stmt, 'ssss', $assetTerm, $serialTerm, $typeTerm, $departmentTerm);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Equipments</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body.dashboard-body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background: url('bg.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #fff;
            margin-bottom: 20px;
        }

        /* Search form */
        .search-form {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .search-input {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        .search-input::placeholder {
            color: #ddd;
        }

        .search-btn {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            background: orange;
            color: #000;
            font-weight: bold;
            cursor: pointer;
        }

        .search-btn:hover {
            background: #ffb84d;
        }

        /* Dashboard buttons */
        .dashboard-buttons {
            text-align: center;
            margin-bottom: 20px;
        }

        .dashboard-buttons button {
            padding: 10px 20px;
            background: red;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .dashboard-buttons button:hover {
            background: darkred;
        }

        /* Table styling */
        table {
            width: 95%;
            margin: 0 auto;
            border-collapse: collapse;
            background: rgba(0, 32, 91, 0.8); /* dark blue with transparency */
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }

        table thead {
            background: orange;
            color: #000;
            font-weight: bold;
        }

        table thead th {
            padding: 12px;
            text-align: left;
        }

        table tbody td {
            padding: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            color: #fff;
        }

        table tbody tr:hover {
            background: rgba(255, 165, 0, 0.2); /* soft orange highlight */
        }

        table tbody tr:last-child td {
            border-bottom: none;
        }
    </style>
</head>
<body class="dashboard-body">

    <h1>View Equipments</h1>

    <!-- Search Form -->
    <form method="POST" action="" class="search-form">
        <input type="text" name="assetnumber" placeholder="Search Asset Number" value="<?php echo htmlspecialchars($assetnumber); ?>" class="search-input">
        <input type="text" name="serialnumber" placeholder="Search Serial Number" value="<?php echo htmlspecialchars($serialnumber); ?>" class="search-input">
        <input type="text" name="type" placeholder="Search Type" value="<?php echo htmlspecialchars($type); ?>" class="search-input">
        <input type="text" name="department" placeholder="Search Department" value="<?php echo htmlspecialchars($department); ?>" class="search-input">
        <input type="submit" value="Search" class="search-btn">
    </form>

    <!-- Back Button -->
    <div class="dashboard-buttons">
        <button onclick="location.href='dashboard.php'">⬅ Back to Dashboard</button>
    </div>

    <!-- Equipment Table -->
    <table>
        <thead>
            <tr>
                <th>Asset Number</th>
                <th>Serial Number</th>
                <th>Type</th>
                <th>Manufacturer</th>
                <th>Year Model</th>
                <th>Description</th>
                <th>Branch</th>
                <th>Department</th>
                <th>Status</th>
                <th>Date Created</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['assetnumber']); ?></td>
                        <td><?php echo htmlspecialchars($row['serialnumber']); ?></td>
                        <td><?php echo htmlspecialchars($row['type']); ?></td>
                        <td><?php echo htmlspecialchars($row['manufacturer']); ?></td>
                        <td><?php echo htmlspecialchars($row['yearmodel']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['branch']); ?></td>
                        <td><?php echo htmlspecialchars($row['department']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['datecreated']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" style="text-align:center;">No equipment found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
