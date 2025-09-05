<?php
require_once "config.php";
include("session-checker.php");

if (isset($_POST['btnsave'])) {
    $assetnumber   = trim($_POST['assetnumber']);
    $serialnumber  = trim($_POST['serialnumber']);
    $type          = $_POST['type'];
    $manufacturer  = trim($_POST['manufacturer']);
    $yearmodel     = trim($_POST['yearmodel']);
    $description   = trim($_POST['description']);
    $branch        = $_POST['branch'];
    $status        = "WORKING";
    $department    = "";
    $createdby     = $_SESSION['username'];
    $datecreated   = date('Y-m-d H:i:s');

    if (!preg_match("/^[0-9]{4}$/", $yearmodel)) {
        echo "<p style='color:red;text-align:center;'>Year model must be a valid 4-digit year.</p>";
    } else {
        $sql = "SELECT 1 FROM tblequipments WHERE assetnumber = ? OR serialnumber = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $assetnumber, $serialnumber);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 0) {
                $sql_insert = "INSERT INTO tblequipments 
                    (assetnumber, serialnumber, type, manufacturer, yearmodel, description, branch, department, status, createdby, datecreated) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                if ($stmt2 = mysqli_prepare($link, $sql_insert)) {
                    mysqli_stmt_bind_param($stmt2, "sssssssssss",
                        $assetnumber, $serialnumber, $type, $manufacturer, $yearmodel,
                        $description, $branch, $department, $status, $createdby, $datecreated
                    );

                    if (mysqli_stmt_execute($stmt2)) {
                        // Insert log
                        $log_date = date("d/m/Y");
                        $log_time = date("h:i:sa");
                        $action   = "Add";
                        $module   = "Equipments";

                        $sql_log = "INSERT INTO tblequipmentslogs (datelog, timelog, assetnumber, performedby, action, module) 
                                    VALUES (?, ?, ?, ?, ?, ?)";
                        if ($stmt3 = mysqli_prepare($link, $sql_log)) {
                            mysqli_stmt_bind_param($stmt3, "ssssss", $log_date, $log_time, $assetnumber, $createdby, $action, $module);
                            mysqli_stmt_execute($stmt3);
                        }

                        header("location: equipments.php?msg=added");
                        exit();
                    } else {
                        echo "<p style='color:red;text-align:center;'>Error saving equipment. Please try again.</p>";
                    }
                }
            } else {
                echo "<p style='color:red;text-align:center;'>ERROR: Asset Number or Serial Number already exists.</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Equipment</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background: url('bg.jpg') no-repeat center center fixed;
    background-size: cover;
    color: white;
}

form {
    width: 400px;
    margin: 50px auto;
    padding: 20px;
    background: rgba(50, 50, 50, 0.9); /* changed form background to dark gray */
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.5);
    font-size: 14px;
}

h1 {
    text-align: center;
    margin-bottom: 20px;
}

label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
}

input[type="text"],
select,
textarea {
    width: 100%;
    padding: 7px;
    margin-bottom: 12px;
    border-radius: 5px;
    border: 1px solid #fff; /* white border */
    background: rgba(255,255,255,0.1);
    color: white;
    box-sizing: border-box;
}

select {
    color: black; /* dropdown text visible */
}

select option {
    color: black;
    background: #fff;
}

textarea {
    resize: none;
}

.buttons {
    display: flex;
    justify-content: space-between;
}

input[type="submit"] {
    flex: 1;
    padding: 8px;
    margin: 5px 5px 0 0;
    border-radius: 5px;
    border: none;
    font-weight: bold;
    cursor: pointer;
    text-align: center;
    font-size: 14px;
    background: red;
    color: white;
    text-decoration: none;
}

.cancel-btn {
    flex: 1;
    padding: 8px;
    margin: 5px 0 0 5px;
    border-radius: 5px;
    border: none;
    font-weight: bold;
    cursor: pointer;
    text-align: center;
    font-size: 14px;
    background: yellow;
    color: black;
    text-decoration: none;
}

input[type="submit"]:hover { background: darkred; }
.cancel-btn:hover { background: gold; }
</style>
</head>
<body>

<h1>Add Equipment</h1>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <label for="assetnumber">Asset Number:</label>
    <input type="text" id="assetnumber" name="assetnumber" required>

    <label for="serialnumber">Serial Number:</label>
    <input type="text" id="serialnumber" name="serialnumber" required>

    <label for="type">Type:</label>
    <select name="type" id="type" required>
        <option value="">--Select Type--</option>
        <option value="Monitor">Monitor</option>
        <option value="CPU">CPU</option>
        <option value="Keyboard">Keyboard</option>
        <option value="Mouse">Mouse</option>
        <option value="AVR">AVR</option>
        <option value="MAC">MAC</option>
        <option value="Printer">Printer</option>
        <option value="Projector">Projector</option>
    </select>

    <label for="manufacturer">Manufacturer:</label>
    <input type="text" id="manufacturer" name="manufacturer" required>

    <label for="yearmodel">Year Model:</label>
    <input type="text" id="yearmodel" name="yearmodel" maxlength="4" required>

    <label for="description">Description:</label>
    <textarea id="description" name="description" rows="3"></textarea>

    <label for="branch">Branch:</label>
    <select id="branch" name="branch" required>
        <option value="">--Select Branch--</option>
        <option value="AU Main Campus">AU Main Campus</option>
        <option value="AU Pasay">AU Pasay</option>
        <option value="AU Mandaluyong">AU Mandaluyong</option>
        <option value="AU Malabon">AU Malabon</option>
        <option value="AU Pasig">AU Pasig</option>
        <option value="College of Engineering">College of Engineering</option>
        <option value="College of Nursing">College of Nursing</option>
        <option value="College of Business">College of Business</option>
        <option value="Registrar’s Office">Registrar’s Office</option>
        <option value="Finance Office">Finance Office</option>
        <option value="IT Department">IT Department</option>
    </select>

    <div class="buttons">
        <input type="submit" name="btnsave" value="Save">
        <a href="equipments.php" class="cancel-btn">Cancel</a>
    </div>
</form>

</body>
</html>
