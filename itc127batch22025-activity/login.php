<?php
if(isset($_POST['btnlogin'])) {
    require_once "config.php";
    $sql = "SELECT * FROM tblaccounts WHERE username = ? AND password = ? AND status = 'ACTIVE'";
    if($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $_POST['txtusername'], $_POST['txtpassword']);
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($result) > 0) {
                $account = mysqli_fetch_array($result, MYSQLI_ASSOC);
                session_start();
                $_SESSION['username'] = $_POST['txtusername'];
                $_SESSION['usertype'] = $account['usertype'];
                header("location:accounts-management.php");
            } else {
                echo "<p style='color:#b71c1c; text-align:center;'>Incorrect login details or account is inactive.</p>";
            }
        }
    }
}
?>
<html>
<head>
<title>Login Page</title>
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: url("bg.jpg") no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Clock at top-left */
    #clock {
        position: absolute;
        top: 15px;
        left: 20px;
        font-family: 'Poppins', sans-serif;
        font-size: 24px;
        font-weight: 600;
        color: white;
        background: rgba(0,0,0,0.7);
        padding: 6px 14px;
        border-radius: 6px;
        letter-spacing: 1px;
        box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }

    .box {
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid #000;
        width: 320px;
        padding: 25px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0,0,0,0.5);
    }

    h2 {
        color: black;
        margin-bottom: 20px;
    }

    input[type=text], input[type=password] {
        width: 90%;
        padding: 10px;
        margin: 8px 0;
        border: 1px solid black;
        border-radius: 6px;
        font-size: 14px;
    }

    input[type=submit] {
        background: black;
        color: white;
        border: none;
        padding: 12px;
        width: 100%;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        margin-top: 10px;
        transition: 0.3s;
    }

    input[type=submit]:hover {
        background: #333;
    }

    label {
        font-size: 13px;
        color: black;
    }
</style>
</head>
<body>
    <div id="clock"></div>
    <div class="box">
        <h2>Login</h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <input type="text" name="txtusername" placeholder="Username" required><br>
            <input type="password" id="password" name="txtpassword" placeholder="Password" required><br>
            <label><input type="checkbox" onclick="togglePassword()"> Show Password</label><br><br>
            <input type="submit" name="btnlogin" value="Login">
        </form>
    </div>

<script>
    // Show/Hide password
    function togglePassword() {
        var pwd = document.getElementById("password");
        pwd.type = (pwd.type === "password") ? "text" : "password";
    }

    // 12-hour Digital Clock with AM/PM
    function updateClock() {
        var now = new Date();
        var h = now.getHours();
        var m = String(now.getMinutes()).padStart(2, '0');
        var s = String(now.getSeconds()).padStart(2, '0');
        var ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12;
        h = h ? h : 12; // 0 should be 12
        document.getElementById("clock").innerText = h + ":" + m + ":" + s + " " + ampm;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
</body>
</html>
