<?php
session_start();

if (isset($_POST['btnlogin'])) {
    require_once "config.php";

    $sql = "SELECT * FROM tblaccounts WHERE username = ? AND password = ? AND status = 'Active'";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $_POST['txtusername'], $_POST['txtpassword']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $account = mysqli_fetch_assoc($result);
            session_regenerate_id(true);
            $_SESSION['username'] = $_POST['txtusername'];
            $_SESSION['usertype'] = $account['usertype'];
            header("location:dashboard.php");
            exit();
        } else {
            $error_message = "Incorrect login details or account is inactive.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Equipment Management System</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
<style>
body {
    margin: 0;
    height: 100vh;
    font-family: 'Montserrat', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    background: url('bg.jpg') no-repeat center center fixed;
    background-size: cover;
}

/* Compact login container */
.login-container {
    display: flex;
    width: 650px; /* smaller width */
    height: 350px; /* smaller height */
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0,0,0,0.5);
}

/* Left portrait - smaller */
.login-left {
    flex: 0.6; /* smaller proportion */
    background: url('port.jpg') no-repeat center center;
    background-size: cover;
    position: relative;
}

.login-left .overlay-text {
    position: absolute;
    bottom: 10px;
    left: 10px;
    font-size: 1rem;
    font-weight: 700;
    color: #fff;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.6);
}

/* Right login form */
.login-right {
    flex: 1; /* slightly bigger to balance */
    background: rgba(255, 140, 0, 0.7);
    padding: 25px 15px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* Avatar */
.login-right .avatar {
    width: 55px;
    height: 55px;
    margin: 0 auto 15px auto;
    border-radius: 50%;
    background: url('avatar.png') no-repeat center center;
    background-size: cover;
    border: 2px solid #fff;
}

/* Heading */
.login-right h1 {
    text-align: center;
    color: #fff;
    margin-bottom: 15px;
    font-size: 1.1rem;
    font-weight: 700;
}

/* Inputs */
.login-right label {
    display: block;
    margin-bottom: 4px;
    font-weight: 500;
    color: #fff;
    font-size: 0.85rem;
}

.login-right input[type="text"],
.login-right input[type="password"] {
    width: 100%;
    padding: 7px;
    margin-bottom: 10px;
    border-radius: 5px;
    border: 1px solid rgba(255,255,255,0.3);
    font-size: 0.85rem;
    background: rgba(255,255,255,0.1);
    color: #fff;
    box-sizing: border-box;
}

/* Submit button */
.login-right input[type="submit"] {
    width: 100%;
    padding: 9px;
    border-radius: 6px;
    border: none;
    font-size: 0.85rem;
    background-color: #e74c3c;
    color: #fff;
    cursor: pointer;
    transition: background 0.3s;
}

.login-right input[type="submit"]:hover {
    background-color: #c0392b;
}

/* Error message */
.error-message {
    background: rgba(255,255,255,0.1);
    color: #fff;
    padding: 6px;
    margin-bottom: 8px;
    border-radius: 5px;
    border: 1px solid rgba(255,255,255,0.2);
    font-size: 0.8rem;
}
</style>
</head>
<body>

<div class="login-container">
    <!-- Left portrait -->
    <div class="login-left">
        <div class="overlay-text">ITC127-ASSESSMENT<br>BSCS BATCH 2</div>
    </div>

    <!-- Right login form -->
    <div class="login-right">
        <div class="avatar"></div>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <h1>Welcome Back Josh</h1>

            <label for="txtusername">Username</label>
            <input type="text" name="txtusername" id="txtusername" required>

            <label for="txtpassword">Password</label>
            <input type="password" name="txtpassword" id="txtpassword" required>

            <input type="submit" name="btnlogin" value="Sign In">
        </form>
    </div>
</div>

</body>
</html>
