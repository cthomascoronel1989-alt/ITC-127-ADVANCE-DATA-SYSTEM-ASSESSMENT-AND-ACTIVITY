<?php
require_once "config.php";
include ("session-checker.php");

if (isset($_POST['btnsubmit'])) {
    if ($_POST['txtpassword'] !== $_POST['txtpassword_confirm']) {
        echo "<p style='color:red;text-align:center;'>Passwords do not match.</p>";
    } else {
        $sql = "SELECT * FROM tblaccounts WHERE username = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $_POST['txtusername']);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) == 0) {
                    $sql = "INSERT INTO tblaccounts (username, password, usertype, status, createdby, datecreated) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                    if ($stmt = mysqli_prepare($link, $sql)) {
                        $status = "ACTIVE";
                        $date = date("d/M/Y");
                        mysqli_stmt_bind_param($stmt, "ssssss", 
                            $_POST['txtusername'], 
                            $_POST['txtpassword'], 
                            $_POST['cmbtype'], 
                            $status, 
                            $_SESSION['username'], 
                            $date
                        );
                        if (mysqli_stmt_execute($stmt)) {
                            header("location: accounts-management.php");
                            exit();
                        }
                    }
                } else {
                    echo "<p style='color:red;text-align:center;'>Username already in use.</p>";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create New Account - Equipment Management System</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
body {
    font-family: Arial, sans-serif;
    background: url('bg.jpg') no-repeat center center fixed;
    background-size: cover;
    color: white;
}

form {
    width: 360px;
    margin: 60px auto;
    padding: 20px;
    background: rgba(0,0,0,0.85);
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.5);
    font-size: 13px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    font-size: 13px;
}

/* Uniform input and select alignment */
input[type="text"],
input[type="password"],
select {
    width: 100%;
    padding: 8px;
    border: none;
    border-radius: 5px;
    font-size: 13px;
    display: block;
    box-sizing: border-box;
    margin-bottom: 12px;
}

.password-wrapper {
    position: relative;
    margin-bottom: 12px;
}

.password-wrapper span {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #aaa;
}

.button-group {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-top: 15px;
}

.button-group input,
.button-group a {
    flex: 1;
    padding: 7px;
    border-radius: 5px;
    font-weight: bold;
    font-size: 13px;
    text-align: center;
    text-decoration: none;
}

.button-group input {
    background: red;
    color: white;
    border: none;
    cursor: pointer;
}

.button-group input:hover {
    background: darkred;
}

.button-group .cancel-btn {
    background: orange;
    color: white;
}

.button-group .cancel-btn:hover {
    background: darkorange;
}
</style>
</head>
<body>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" onsubmit="return validatePasswords();">
    <label>Username:</label>
    <input type="text" name="txtusername" required>

    <label>Password:</label>
    <div class="password-wrapper">
        <input type="password" name="txtpassword" id="txtpassword" required>
        <span id="eye-icon"><i class="fa fa-eye-slash"></i></span>
    </div>

    <label>Repeat Password:</label>
    <div class="password-wrapper">
        <input type="password" name="txtpassword_confirm" id="txtpassword_confirm" required>
        <span id="eye-icon-confirm"><i class="fa fa-eye-slash"></i></span>
    </div>

    <label>Account Type:</label>
    <select name="cmbtype" required>
        <option value="">--Select account type--</option>
        <option value="ADMINISTRATOR">Administrator</option>
        <option value="TECHNICAL">Technical</option>
        <option value="USER">User</option>
    </select>

    <div class="button-group">
        <input type="submit" name="btnsubmit" value="Submit">
        <a href="accounts-management.php" class="cancel-btn">Cancel</a>
    </div>
</form>

<script>
// Toggle password visibility
function togglePassword(fieldId, iconId) {
    const passwordField = document.getElementById(fieldId);
    const icon = document.getElementById(iconId).querySelector("i");
    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    } else {
        passwordField.type = "password";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    }
}
document.getElementById("eye-icon").addEventListener("click", () => togglePassword("txtpassword", "eye-icon"));
document.getElementById("eye-icon-confirm").addEventListener("click", () => togglePassword("txtpassword_confirm", "eye-icon-confirm"));

// Validate passwords match
function validatePasswords() {
    const pass = document.getElementById("txtpassword").value;
    const confirm = document.getElementById("txtpassword_confirm").value;
    if (pass !== confirm) {
        alert("Passwords do not match!");
        return false;
    }
    return true;
}
</script>
</body>
</html>
