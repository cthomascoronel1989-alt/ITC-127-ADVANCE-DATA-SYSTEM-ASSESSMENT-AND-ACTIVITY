<?php
require_once "config.php";
include("session-checker.php");

// Delete all equipment logs
if (isset($_POST['delete_all_equipment_logs'])) {
    mysqli_query($link, "DELETE FROM tblequipmentslogs");
    // Log the action
    $stmt = mysqli_prepare($link, "INSERT INTO tblaccountslogs (datelog, timelog, module, action, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)");
    $date = date("d/m/Y");
    $time = date("h:i:sa");
    $module = "Equipment-Logs";
    $action = "Delete All";
    $performedto = "All Equipment Logs";
    $performedby = $_SESSION['username'];
    mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $module, $action, $performedto, $performedby);
    mysqli_stmt_execute($stmt);
}

// Delete all account/action logs
if (isset($_POST['delete_all_account_logs'])) {
    mysqli_query($link, "DELETE FROM tblaccountslogs");
    $stmt = mysqli_prepare($link, "INSERT INTO tblaccountslogs (datelog, timelog, module, action, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)");
    $date = date("d/m/Y");
    $time = date("h:i:sa");
    $module = "Account-Logs";
    $action = "Delete All";
    $performedto = "All Account Logs";
    $performedby = $_SESSION['username'];
    mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $module, $action, $performedto, $performedby);
    mysqli_stmt_execute($stmt);
}

// Fetch logs
$equipmentLogResult = mysqli_query($link, "SELECT * FROM tblequipmentslogs ORDER BY datelog DESC, timelog DESC");
$actionLogResult = mysqli_query($link, "SELECT * FROM tblaccountslogs ORDER BY datelog DESC, timelog DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logs - Dashboard Style</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: url('bg.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            margin: 0;
            padding: 0;
        }
        h1, h2 { text-align: center; margin-top: 20px; text-shadow: 1px 1px 4px rgba(0,0,0,0.6); }
        .table-section { width: 95%; margin: 20px auto; padding: 15px; background: rgba(0,0,0,0.7); border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.5); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background: rgba(255,165,0,0.9); color: #000; }
        tr:nth-child(even) { background: rgba(255,255,255,0.05); }
        tr:hover { background: rgba(255,165,0,0.2); transition: 0.3s; }
        .delete-button {
            background: red;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .delete-button:hover { background: darkred; }
        .back-button {
            display: inline-block;
            margin: 20px auto;
            padding: 8px 12px;
            background: #b58900; /* dark yellow */
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .back-button:hover { background: #e0a800; }
        .btn-container { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>

<h1>Logs Management</h1>

<div class="btn-container">
    <a href="dashboard.php" class="back-button"><i class="fa fa-home"></i> Back to Dashboard</a>
</div>

<div class="table-section">
    <h2>Equipment Logs</h2>
    <form method="POST" onsubmit="return confirm('Are you sure you want to delete all equipment logs?');">
        <input type="submit" name="delete_all_equipment_logs" value="Delete All Equipment Logs" class="delete-button">
    </form>
    <table>
        <tr>
            <th>Date</th><th>Time</th><th>Asset Number</th><th>Performed By</th><th>Action</th><th>Module</th>
        </tr>
        <?php if(mysqli_num_rows($equipmentLogResult) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($equipmentLogResult)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['datelog']) ?></td>
                    <td><?= htmlspecialchars($row['timelog']) ?></td>
                    <td><?= htmlspecialchars($row['assetnumber']) ?></td>
                    <td><?= htmlspecialchars($row['performedby']) ?></td>
                    <td><?= htmlspecialchars($row['action']) ?></td>
                    <td><?= htmlspecialchars($row['module']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No equipment logs found.</td></tr>
        <?php endif; ?>
    </table>
</div>

<div class="table-section">
    <h2>Action Logs</h2>
    <form method="POST" onsubmit="return confirm('Are you sure you want to delete all action logs?');">
        <input type="submit" name="delete_all_account_logs" value="Delete All Action Logs" class="delete-button">
    </form>
    <table>
        <tr>
            <th>Date</th><th>Time</th><th>Module</th><th>Action</th><th>Performed To</th><th>Performed By</th>
        </tr>
        <?php if(mysqli_num_rows($actionLogResult) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($actionLogResult)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['datelog']) ?></td>
                    <td><?= htmlspecialchars($row['timelog']) ?></td>
                    <td><?= htmlspecialchars($row['module']) ?></td>
                    <td><?= htmlspecialchars($row['action']) ?></td>
                    <td><?= htmlspecialchars($row['performedto']) ?></td>
                    <td><?= htmlspecialchars($row['performedby']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No action logs found.</td></tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
