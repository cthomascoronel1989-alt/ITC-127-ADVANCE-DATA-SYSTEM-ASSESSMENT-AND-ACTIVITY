<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit;
}
require_once "config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Accounts Management - Equipment Management System</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
<style>
/* Background */
body.dashboard-body {
    margin: 0;
    height: 100vh;
    background: url('bg.jpg') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Montserrat', sans-serif;
    font-size: 13px; 
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Centered card */
.container {
    background: rgba(0,0,0,0.85);
    padding: 20px;
    border-radius: 10px;
    width: 90%;
    max-width: 950px;
    text-align: center;
    box-shadow: 0 0 20px rgba(0,0,0,0.6);
}

/* Headings */
h1, h4 {
    margin: 8px 0;
    font-size: 16px; 
}

/* Search form */
.search-form {
    margin: 15px 0;
    font-size: 12px;
}
.search-input {
    padding: 6px;
    border-radius: 4px;
    border: 1px solid rgba(255,255,255,0.3);
    background: rgba(255,255,255,0.1);
    color: #fff;
    font-size: 12px;
}
.search-btn, .back-link, .create-account-link {
    padding: 6px 10px;
    margin: 4px 2px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-weight: bold;
    font-size: 12px;
    background: #e74c3c; 
    color: #fff;
    transition: 0.3s;
}
.search-btn:hover, .back-link:hover, .create-account-link:hover {
    background: #c0392b; 
}

/* Accounts table */
.account-table {
    width: 100%;
    margin-top: 15px;
    border-collapse: collapse;
    background: rgba(0,0,0,0.8);
    border-radius: 8px;
    overflow: hidden;
    font-size: 12px;
    box-shadow: 0 0 15px rgba(0,0,0,0.6);
}
.account-table th, .account-table td {
    padding: 8px;
    text-align: center;
    color: #fff;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.account-table th {
    background: orange;
    color: #000;
    font-weight: bold;
    font-size: 12px;
}
.account-table tr:hover {
    background: rgba(255,165,0,0.15);
}

/* Action links */
.edit-link, .delete-link {
    margin: 0 4px;
    font-weight: bold;
    text-decoration: none;
    color: #fff;
    font-size: 12px;
}
.edit-link:hover { color: orange; }
.delete-link:hover { color: red; }

/* Modal */
.modal {
    display: none;
    position: fixed;
    top:0; left:0;
    width:100%; height:100%;
    background: rgba(0,0,0,0.7);
    justify-content:center;
    align-items:center;
    z-index: 1000;
}
.modal-content {
    background: rgba(0,0,0,0.9);
    color: #fff;
    padding: 15px;
    border-radius: 6px;
    width: 260px;
    text-align: center;
    font-size: 12px;
    box-shadow: 0 0 12px rgba(0,0,0,0.8);
}
.modal-content button {
    margin: 6px;
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    font-size: 12px;
    background: #e74c3c;
    color: #fff;
    transition: 0.3s;
}
.modal-content button:hover {
    background: #c0392b;
}
</style>
</head>
<body class="dashboard-body">

<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    <h4>Account type: <?php echo htmlspecialchars($_SESSION['usertype']); ?></h4>

    <!-- Search -->
    <form action="" method="POST" class="search-form">
        <a href="create-account.php" class="create-account-link">Create New Account</a>
        <a href="dashboard.php" class="back-link">Back</a>
        <input type="text" name="txtsearch" placeholder="Search by Username or Usertype" class="search-input">
        <input type="submit" name="btnsearch" value="Search" class="search-btn">
    </form>

    <!-- Delete Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to delete this account?</p>
            <form id="deleteForm" method="POST" action="delete-account.php">
                <input type="hidden" id="deleteUsername" name="username" value="">
                <button type="submit">Yes, Delete</button>
                <button type="button" onclick="cancelDelete()">Cancel</button>
            </form>
        </div>
    </div>

    <?php
    // Function to build the accounts table
    function buildTable($result) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table class='account-table'>";
            echo "<tr><th>Username</th><th>Usertype</th><th>Status</th><th>Created By</th><th>Date Created</th><th>Actions</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                $username = htmlspecialchars($row['username']);
                echo "<tr>";
                echo "<td>{$username}</td>";
                echo "<td>" . htmlspecialchars($row['usertype']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td>" . htmlspecialchars($row['createdby']) . "</td>";
                echo "<td>" . htmlspecialchars($row['datecreated']) . "</td>";
                echo "<td>";
                echo "<a href='update-account.php?username=" . urlencode($row['username']) . "' class='edit-link'>Edit</a>";
                echo "<a href='#' onclick='showModal(\"" . addslashes($row['username']) . "\")' class='delete-link'>Delete</a>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='text-align:center;'>No records found.</p>";
        }
    }

    // Handle search
    if (isset($_POST['btnsearch']) && !empty(trim($_POST['txtsearch']))) {
        $searchValue = '%' . $_POST['txtsearch'] . '%';
        $sql = "SELECT * FROM tblaccounts WHERE username LIKE ? OR usertype LIKE ? ORDER BY username";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $searchValue, $searchValue);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            buildTable($result);
        } else {
            echo "<p style='color:red;text-align:center;'>Error fetching data.</p>";
        }
    } else {
        $sql = "SELECT * FROM tblaccounts ORDER BY username";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            buildTable($result);
        } else {
            echo "<p style='color:red;text-align:center;'>Error fetching data.</p>";
        }
    }
    ?>
</div>

<script>
function showModal(username) {
    document.getElementById("confirmationModal").style.display = "flex";
    document.getElementById("deleteUsername").value = username;
}
function cancelDelete() {
    document.getElementById("confirmationModal").style.display = "none";
}
document.addEventListener("keydown", function(e) {
    if (e.key === "Escape") cancelDelete();
});
</script>
</body>
</html>
