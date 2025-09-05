<?php
require_once "config.php";
include("session-checker.php");

// Fetch all equipment from database
$sql = "SELECT * FROM tblequipments ORDER BY AssetNumber ASC";
$result = mysqli_query($link, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Equipment Management - Dashboard Style</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    background: url('bg.jpg') no-repeat center center fixed;
    background-size: cover;
    color: #fff;
}

h1 {
    text-align: center;
    margin: 20px 0;
    font-size: 20px;
    text-shadow: 1px 1px 4px rgba(0,0,0,0.6);
}

.btn-container {
    text-align: center;
    margin-bottom: 15px;
}

.btn-add, .btn-back {
    padding: 8px 14px;
    font-weight: bold;
    border-radius: 5px;
    text-decoration: none;
    transition: 0.3s;
    margin-right: 10px;
    display: inline-block;
}

.btn-add {
    background: #fff;
    color: #000;
}

.btn-add:hover {
    background: #ddd;
}

.btn-back {
    background: #b58900; /* dark yellow */
    color: #000;
}

.btn-back:hover {
    background: #e0a800;
}

/* Search bar */
.search-container {
    text-align: center;
    margin-bottom: 20px;
}

.search-container input[type="text"] {
    padding: 6px 10px;
    width: 250px;
    border-radius: 4px;
    border: 1px solid #000;
}

/* Table container */
.table-container {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto 50px auto;
    padding: 15px;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

th, td {
    padding: 8px;
    text-align: center;
    border: 1px solid #000;
    color: #fff;
}

th {
    background: rgba(255,165,0,0.9);
    color: #000;
    font-weight: bold;
}

tr:nth-child(even) {
    background: rgba(0,0,0,0.3);
}

tr:hover {
    background: rgba(255,165,0,0.15);
    transition: 0.3s;
}

/* Action Buttons */
.btn {
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 12px;
    font-weight: bold;
    text-decoration: none;
    transition: 0.3s;
}

.btn-edit {
    background: #ffc107;
    color: #000;
}

.btn-edit:hover {
    background: #e0a800;
}

.btn-delete {
    background: #e74c3c;
    color: #fff;
}

.btn-delete:hover {
    background: #c0392b;
}

/* Responsive */
@media (max-width: 768px) {
    th, td { font-size: 11px; padding: 6px; }
    h1 { font-size: 18px; }
}
</style>
</head>
<body>

<h1>Equipment Management</h1>

<div class="btn-container">
    <a href="dashboard.php" class="btn-back"><i class="fa fa-home"></i> Back to Dashboard</a>
    <a href="add_equipment.php" class="btn-add"><i class="fa fa-plus"></i> Add Equipment</a>
</div>

<div class="search-container">
    <input type="text" id="searchInput" placeholder="Search equipment...">
</div>

<div class="table-container">
    <table id="equipmentTable">
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
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$row['AssetNumber']}</td>";
                echo "<td>{$row['SerialNumber']}</td>";
                echo "<td>{$row['Type']}</td>";
                echo "<td>{$row['Manufacturer']}</td>";
                echo "<td>{$row['YearModel']}</td>";
                echo "<td>{$row['Description']}</td>";
                echo "<td>{$row['Branch']}</td>";
                echo "<td>{$row['Department']}</td>";
                echo "<td>{$row['Status']}</td>";
                echo "<td>
                        <a class='btn btn-edit' href='update-equipments.php?AssetNumber={$row['AssetNumber']}'>Edit</a>
                        <a class='btn btn-delete' href='delete_equipment.php?AssetNumber={$row['AssetNumber']}' onclick='return confirm(\"Are you sure?\");'>Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>No equipment found.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<script>
// Live search functionality
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('keyup', function() {
    const filter = searchInput.value.toLowerCase();
    const table = document.getElementById('equipmentTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) { // skip header row
        const tds = tr[i].getElementsByTagName('td');
        let match = false;
        for (let j = 0; j < tds.length - 1; j++) { // exclude actions column
            if (tds[j].textContent.toLowerCase().indexOf(filter) > -1) {
                match = true;
                break;
            }
        }
        tr[i].style.display = match ? '' : 'none';
    }
});
</script>

</body>
</html>
