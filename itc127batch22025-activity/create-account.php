<?php
require_once "config.php";
include("session-checker.php");

if (isset($_POST['btnsubmit'])) {
    // Check if username already exists
    $sql_check = "SELECT * FROM tblaccounts WHERE username = ?";
    if ($stmt_check = mysqli_prepare($link, $sql_check)) {
        mysqli_stmt_bind_param($stmt_check, "s", $_POST['txtusername']);
        if (mysqli_stmt_execute($stmt_check)) {
            $result = mysqli_stmt_get_result($stmt_check);

            if (mysqli_num_rows($result) == 0) {
                // Insert new account
                $sql_insert = "INSERT INTO tblaccounts (username, password, usertype, status, createdby, datecreated) 
                               VALUES (?, ?, ?, ?, ?, ?)";
                if ($stmt_insert = mysqli_prepare($link, $sql_insert)) {
                    $status = "ACTIVE";
                    $date = date("Y-m-d"); // MySQL-friendly
                    $hashed_password = password_hash($_POST['txtpassword'], PASSWORD_DEFAULT);

                    mysqli_stmt_bind_param(
                        $stmt_insert,
                        "ssssss",
                        $_POST['txtusername'],
                        $hashed_password,
                        $_POST['cmbtype'],
                        $status,
                        $_SESSION['username'],
                        $date
                    );

                    if (mysqli_stmt_execute($stmt_insert)) {
                        // Insert into logs
                        $sql_log = "INSERT INTO tbllogs (datelog, timelog, action, module, performedto, performedby) 
                                    VALUES (?, ?, ?, ?, ?, ?)";
                        if ($stmt_log = mysqli_prepare($link, $sql_log)) {
                            $date = date("Y-m-d");
                            $time = date("h:i:s A"); // 12hr format
                            $action = "Create";
                            $module = "Accounts Management";

                            mysqli_stmt_bind_param(
                                $stmt_log,
                                "ssssss",
                                $date,
                                $time,
                                $action,
                                $module,
                                $_POST['txtusername'],
                                $_SESSION['username']
                            );

                            if (mysqli_stmt_execute($stmt_log)) {
                                header("Location: accounts-management.php?msg=created");
                                exit();
                            } else {
                                echo "<p style='color:red;'>Error inserting log.</p>";
                            }
                            mysqli_stmt_close($stmt_log);
                        }
                    } else {
                        echo "<p style='color:red;'>Error inserting account.</p>";
                    }
                    mysqli_stmt_close($stmt_insert);
                }
            } else {
                echo "<p style='color:red;'>Username already in use.</p>";
            }
        } else {
            echo "<p style='color:red;'>Error validating username.</p>";
        }
        mysqli_stmt_close($stmt_check);
    }
}
?>
<html>
<head>
<title>Create Account - Equipment Management</title>
<style>
    body {
        font-family: 'Open Sans', sans-serif;
        background: url("bgg.jpg") no-repeat center center fixed;
        background-size: cover;
        text-align: center;
        margin-top: 80px;
        color: #fff;
    }
    .box {
        background: rgba(0,0,0,0.7);
        padding: 25px;
        width: 350px;
        margin: auto;
        border-radius: 10px;
        border: 2px solid #654321; /* dark brown */
        text-align: left;
    }
    h2 {
        text-align: center;
        margin-bottom: 15px;
        color: #fff;
        font-size: 20px;
    }
    label {
        font-size: 13px;
        display: block;
        margin-top: 8px;
    }
    input[type=text], input[type=password], select {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 13px;
    }
    .show-pass {
        margin-top: 5px;
        font-size: 12px;
        color: #ccc;
    }
    input[type=submit] {
        background: darkorange;
        border: none;
        padding: 10px;
        width: 100%;
        color: white;
        border-radius: 5px;
        margin-top: 15px;
        cursor: pointer;
        font-size: 14px;
    }
    input[type=submit]:hover {
        background: #b37400;
    }
    a {
        color: #ccc;
        text-decoration: none;
        font-size: 12px;
        display: block;
        margin-top: 10px;
        text-align: center;
    }
    a:hover {
        color: white;
    }
</style>
</head>
<body>
    <div class="box">
        <h2>Create Account</h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <label>Username</label>
            <input type="text" name="txtusername" required>

            <label>Password</label>
            <input type="password" name="txtpassword" id="password" required>
            <input type="checkbox" onclick="togglePassword()" class="show-pass"> Show Password

            <label>Account Type</label>
            <select name="cmbtype" required>
                <option value="">--Select account type--</option>
                <option value="ADMINISTRATOR">Administrator</option>
                <option value="TECHNICAL">Technical</option>
                <option value="USER">User</option>
            </select>

            <input type="submit" name="btnsubmit" value="Create Account">
            <a href="accounts-management.php">Cancel</a>
        </form>
    </div>

    <script>
        function togglePassword() {
            var x = document.getElementById("password");
            x.type = (x.type === "password") ? "text" : "password";
        }
    </script>
</body>
</html>
