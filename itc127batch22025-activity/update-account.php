<?php
require_once "config.php";
include("session-checker.php");

if (isset($_POST['btnsubmit'])) { // update account
    $sql = "UPDATE tblaccounts SET password = ?, usertype = ?, status = ? WHERE username = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $_POST['txtpassword'], $_POST['cmbtype'], $_POST['rbstatus'], $_GET['username']);
        if (mysqli_stmt_execute($stmt)) {
            $sql = "INSERT INTO tbllogs (datelog, timelog, action, module, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($link, $sql)) {
                $date = date("d/m/Y");
                $time = date("h:i:sa");
                $action = "Update";
                $module = "Accounts Management";
                mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $action, $module, $_GET['username'], $_SESSION['username']);
                if (mysqli_stmt_execute($stmt)) {
                    header("location: accounts-management.php?msg=updated");
                    exit();	
                } else {
                    echo "<p style='color:red;'>Error on insert log statement.</p>";
                }
            }
        } else {
            echo "<p style='color:red;'>Error on update statement.</p>";
        }
    }
} else { // load account data
    if (isset($_GET['username']) && !empty(trim($_GET['username']))) {
        $sql = "SELECT * FROM tblaccounts WHERE username = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $_GET['username']);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $account = mysqli_fetch_array($result, MYSQLI_ASSOC);
            } else {
                echo "<p style='color:red;'>Error loading account data.</p>";
            }
        }
    }
}
?>
<html>
<head>
<title>Update Account - Equipment Management</title>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Open Sans', sans-serif;
        background: url("bgg.jpg") no-repeat center center fixed;
        background-size: cover;
        color: #fff;
        text-align: center;
        margin-top: 80px;
    }
    .box {
        background: rgba(0, 0, 0, 0.7);
        border: 1px solid #2e2e2e;
        width: 350px;
        margin: auto;
        padding: 20px;
        border-radius: 8px;
        text-align: left;
    }
    h2 {
        text-align: center;
        margin-bottom: 15px;
        font-weight: 600;
    }
    label {
        font-size: 14px;
        display: block;
        margin-top: 10px;
    }
    input[type=text], input[type=password], select {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        border-radius: 5px;
        border: 1px solid #444;
        background: #222;
        color: #fff;
        font-size: 14px;
    }
    input[type=radio] {
        margin-right: 5px;
    }
    .actions {
        text-align: center;
        margin-top: 15px;
    }
    input[type=submit] {
        background: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }
    input[type=submit]:hover {
        background: #0056b3;
    }
    .cancel-btn {
        background: #555;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        margin-left: 10px;
        font-size: 14px;
    }
    .cancel-btn:hover {
        background: #777;
    }
    .show-pass {
        font-size: 12px;
        display: inline-block;
        margin-top: 5px;
    }
</style>
</head>
<body>
    <div class="box">
        <h2>Update Account</h2>
        <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="POST">
            <label>Username:</label>
            <p><?php echo htmlspecialchars($account['username']); ?></p>
            
            <label>Password:</label>
            <input type="password" id="txtpassword" name="txtpassword" value="<?php echo htmlspecialchars($account['password']); ?>" required>
            <br><input type="checkbox" onclick="togglePassword()" class="show-pass"> Show Password
            
            <label>Current User Type:</label>
            <p><?php echo htmlspecialchars($account['usertype']); ?></p>
            
            <label>Change User Type:</label>
            <select name="cmbtype" required>
                <option value="">--Select Account Type--</option>
                <option value="ADMINISTRATOR">Administrator</option>
                <option value="TECHNICAL">Technician</option>
                <option value="USER">User</option>
            </select>
            
            <label>Status:</label>
            <?php if ($account['status'] == 'ACTIVE') { ?>
                <input type="radio" name="rbstatus" value="ACTIVE" checked> Active
                <input type="radio" name="rbstatus" value="INACTIVE"> Inactive
            <?php } else { ?>
                <input type="radio" name="rbstatus" value="ACTIVE"> Active
                <input type="radio" name="rbstatus" value="INACTIVE" checked> Inactive
            <?php } ?>

            <div class="actions">
                <input type="submit" name="btnsubmit" value="Update">
                <a href="accounts-management.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        function togglePassword() {
            var pass = document.getElementById("txtpassword");
            pass.type = (pass.type === "password") ? "text" : "password";
        }
    </script>
</body>
</html>
