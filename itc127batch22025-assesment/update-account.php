<?php
require_once "config.php";
include("session-checker.php");

$account = [];

if (isset($_GET['username']) && !empty(trim($_GET['username']))) {
    $username = trim($_GET['username']);
    $sql = "SELECT * FROM tblaccounts WHERE username = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $account = mysqli_fetch_assoc($result);
        } else {
            echo "<p style='color:red;text-align:center;'>Account not found.</p>";
            exit;
        }
    }
}

if (isset($_POST['btnsubmit'])) {
    if ($_POST['txtpassword'] === $_POST['txtpassword_confirm']) {
        $sql = "UPDATE tblaccounts SET password=?, usertype=?, status=? WHERE username=?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $_POST['txtpassword'],
                $_POST['cmbtype'],
                $_POST['rbstatus'],
                $username
            );
            if (mysqli_stmt_execute($stmt)) {
                // Log action
                $logSql = "INSERT INTO tblaccountslogs (datelog, timelog, action, module, performedto, performedby)
                           VALUES (?, ?, ?, ?, ?, ?)";
                if ($logStmt = mysqli_prepare($link, $logSql)) {
                    $date = date("d/m/Y");
                    $time = date("h:i:sa");
                    $action = "Update";
                    $module = "Accounts Management";
                    mysqli_stmt_bind_param(
                        $logStmt,
                        "ssssss",
                        $date,
                        $time,
                        $action,
                        $module,
                        $username,
                        $_SESSION['username']
                    );
                    mysqli_stmt_execute($logStmt);
                }
                header("Location: accounts-management.php");
                exit;
            } else {
                echo "<p style='color:red;text-align:center;'>Error updating account.</p>";
            }
        }
    } else {
        echo "<p style='color:red;text-align:center;'>Passwords do not match!</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Update Account</title>
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
    text-align: left;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    font-size: 13px;
}

input[type="text"],
input[type="password"],
select {
    width: 100%;
    padding: 7px 35px 7px 7px; /* padding-right to make room for eye icon */
    margin-bottom: 12px;
    border: none;
    border-radius: 5px;
    font-size: 13px;
    box-sizing: border-box;
    color: black;
}

.password-wrapper {
    position: relative;
    width: 100%;
}

.password-wrapper span {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #333;
}

.radio-container {
    margin-bottom: 12px;
    display: flex;
    gap: 20px;
}

.radio-container label {
    font-weight: normal;
}

.button-group {
    display: flex;
    gap: 10px;
    justify-content: space-between;
}

.button-group input,
.button-group a {
    flex: 1;
    padding: 7px 0;
    font-weight: bold;
    font-size: 13px;
    border-radius: 5px;
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

.button-group a {
    background: orange;
    color: white;
}

.button-group a:hover {
    background: darkorange;
}
</style>
</head>
<body>
<form method="POST" action="" onsubmit="return validatePasswords();">
    <label>Username:</label>
    <input type="text" value="<?php echo htmlspecialchars($account['username'] ?? $username); ?>" readonly>

    <label>Password:</label>
    <div class="password-wrapper">
        <input type="password" name="txtpassword" id="txtpassword" value="<?php echo htmlspecialchars($account['password'] ?? ''); ?>" required>
        <span id="eye-icon"><i class="fa fa-eye-slash"></i></span>
    </div>

    <label>Repeat Password:</label>
    <div class="password-wrapper">
        <input type="password" name="txtpassword_confirm" id="txtpassword_confirm" required>
        <span id="eye-icon-confirm"><i class="fa fa-eye-slash"></i></span>
    </div>

    <label>Account Type:</label>
    <select name="cmbtype" required>
        <option value="">--Select--</option>
        <option value="ADMINISTRATOR" <?php if (($account['usertype'] ?? '') === 'ADMINISTRATOR') echo 'selected'; ?>>Administrator</option>
        <option value="TECHNICAL" <?php if (($account['usertype'] ?? '') === 'TECHNICAL') echo 'selected'; ?>>Technical</option>
        <option value="USER" <?php if (($account['usertype'] ?? '') === 'USER') echo 'selected'; ?>>User</option>
    </select>

    <label>Status:</label>
    <div class="radio-container">
        <label><input type="radio" name="rbstatus" value="ACTIVE" <?php if (($account['status'] ?? '') === 'ACTIVE') echo 'checked'; ?>> Active</label>
        <label><input type="radio" name="rbstatus" value="INACTIVE" <?php if (($account['status'] ?? '') === 'INACTIVE') echo 'checked'; ?>> Inactive</label>
    </div>

    <div class="button-group">
        <input type="submit" name="btnsubmit" value="Update">
        <a href="accounts-management.php" class="cancel-btn">Cancel</a>
    </div>
</form>

<script>
const togglePassword = (iconId, inputId) => {
    const icon = document.getElementById(iconId);
    icon.addEventListener('click', () => {
        const input = document.getElementById(inputId);
        const i = icon.querySelector('i');
        if (input.type === "password") {
            input.type = "text";
            i.classList.replace("fa-eye-slash", "fa-eye");
        } else {
            input.type = "password";
            i.classList.replace("fa-eye", "fa-eye-slash");
        }
    });
};

togglePassword("eye-icon", "txtpassword");
togglePassword("eye-icon-confirm", "txtpassword_confirm");

function validatePasswords() {
    const pwd = document.getElementById("txtpassword").value;
    const confirmPwd = document.getElementById("txtpassword_confirm").value;
    if (pwd !== confirmPwd) {
        alert("Passwords do not match!");
        return false;
    }
    return true;
}
</script>
</body>
</html>
