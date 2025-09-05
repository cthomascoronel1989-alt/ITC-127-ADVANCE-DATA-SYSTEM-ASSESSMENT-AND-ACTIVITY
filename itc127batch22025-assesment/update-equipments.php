<?php
require_once "config.php";
include("session-checker.php");

// Check if AssetNumber is provided
if (!isset($_GET['AssetNumber'])) {
    header("Location: equipments.php");
    exit();
}

$assetNumber = $_GET['AssetNumber'];

// Fetch the current equipment data
$sql = "SELECT * FROM tblequipments WHERE AssetNumber = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $assetNumber);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$equipment = mysqli_fetch_assoc($result);

// Handle form submission
if (isset($_POST['btnupdate'])) {
    $serialnumber  = trim($_POST['serialnumber']);
    $type          = $_POST['type'];
    $manufacturer  = trim($_POST['manufacturer']);
    $yearmodel     = trim($_POST['yearmodel']);
    $description   = trim($_POST['description']);
    $branch        = $_POST['branch'];
    $department    = $_POST['department'];
    $status        = $_POST['status'];
    $updatedby     = $_SESSION['username'];
    $dateupdated   = date('Y-m-d H:i:s');

    if (!preg_match("/^[0-9]{4}$/", $yearmodel)) {
        $error_message = "Year model must be a valid 4-digit year.";
    } else {
        $sql_update = "UPDATE tblequipments 
                       SET SerialNumber=?, Type=?, Manufacturer=?, YearModel=?, Description=?, Branch=?, Department=?, Status=?, CreatedBy=?, DateCreated=? 
                       WHERE AssetNumber=?";
        if ($stmt2 = mysqli_prepare($link, $sql_update)) {
            mysqli_stmt_bind_param($stmt2, "sssssssssss", 
                $serialnumber, $type, $manufacturer, $yearmodel, $description, $branch, $department, $status, $updatedby, $dateupdated, $assetNumber
            );
            if (mysqli_stmt_execute($stmt2)) {
                // Insert log
                $log_date = date("d/m/Y");
                $log_time = date("h:i:sa");
                $action   = "Update";
                $module   = "Equipments";
                $sql_log = "INSERT INTO tblequipmentslogs (DateLog, TimeLog, AssetNumber, PerformedBy, Action, Module) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                if ($stmt3 = mysqli_prepare($link, $sql_log)) {
                    mysqli_stmt_bind_param($stmt3, "ssssss", $log_date, $log_time, $assetNumber, $updatedby, $action, $module);
                    mysqli_stmt_execute($stmt3);
                }
                header("Location: equipments.php?msg=updated");
                exit();
            } else {
                $error_message = "Error updating equipment. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Equipment</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background: url('bg.jpg') no-repeat center center fixed;
    background-size: cover;
    color: black;
}

form {
    width: 400px;
    margin: 50px auto;
    padding: 20px;
    background: rgba(255,140,0,0.85); /* dashboard orange style */
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.5);
    font-size: 14px;
    color: black;
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
    border: 1px solid #fff;
    background: rgba(255,255,255,0.1);
    color: black;
    box-sizing: border-box;
}

select {
    color: black;
}

select option {
    color: black;
    background: #fff;
}

textarea { resize: none; }

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
    background: #b58900; /* dark yellow */
    color: black;
    text-decoration: none;
}

input[type="submit"]:hover { background: darkred; }
.cancel-btn:hover { background: #e0a800; }

.error-message {
    color: red;
    text-align: center;
    margin-bottom: 10px;
}
</style>
</head>
<body>

<h1>Update Equipment</h1>

<?php if(isset($error_message)): ?>
    <p class="error-message"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="assetnumber">Asset Number:</label>
    <input type="text" id="assetnumber" name="assetnumber" value="<?php echo htmlspecialchars($equipment['AssetNumber']); ?>" readonly>

    <label for="serialnumber">Serial Number:</label>
    <input type="text" id="serialnumber" name="serialnumber" value="<?php echo htmlspecialchars($equipment['SerialNumber']); ?>" required>

    <label for="type">Type:</label>
    <select name="type" id="type" required>
        <option value="">--Select Type--</option>
        <option value="Monitor" <?php if($equipment['Type']=="Monitor") echo "selected"; ?>>Monitor</option>
        <option value="CPU" <?php if($equipment['Type']=="CPU") echo "selected"; ?>>CPU</option>
        <option value="Keyboard" <?php if($equipment['Type']=="Keyboard") echo "selected"; ?>>Keyboard</option>
        <option value="Mouse" <?php if($equipment['Type']=="Mouse") echo "selected"; ?>>Mouse</option>
        <option value="AVR" <?php if($equipment['Type']=="AVR") echo "selected"; ?>>AVR</option>
        <option value="MAC" <?php if($equipment['Type']=="MAC") echo "selected"; ?>>MAC</option>
        <option value="Printer" <?php if($equipment['Type']=="Printer") echo "selected"; ?>>Printer</option>
        <option value="Projector" <?php if($equipment['Type']=="Projector") echo "selected"; ?>>Projector</option>
    </select>

    <label for="manufacturer">Manufacturer:</label>
    <input type="text" id="manufacturer" name="manufacturer" value="<?php echo htmlspecialchars($equipment['Manufacturer']); ?>" required>

    <label for="yearmodel">Year Model:</label>
    <input type="text" id="yearmodel" name="yearmodel" maxlength="4" value="<?php echo htmlspecialchars($equipment['YearModel']); ?>" required>

    <label for="description">Description:</label>
    <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($equipment['Description']); ?></textarea>

    <label for="branch">Branch:</label>
    <select id="branch" name="branch" required>
        <?php
        $branches = ["AU Main Campus","AU Pasay","AU Mandaluyong","AU Malabon","AU Pasig","College of Engineering","College of Nursing","College of Business","Registrar’s Office","Finance Office","IT Department"];
        foreach($branches as $b) {
            $selected = ($equipment['Branch']==$b) ? "selected" : "";
            echo "<option value='$b' $selected>$b</option>";
        }
        ?>
    </select>

    <label for="department">Department:</label>
    <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($equipment['Department']); ?>">

    <label for="status">Status:</label>
    <input type="text" id="status" name="status" value="<?php echo htmlspecialchars($equipment['Status']); ?>" required>

    <div class="buttons">
        <input type="submit" name="btnupdate" value="Update Equipment">
        <a href="equipments.php" class="cancel-btn">Cancel</a>
    </div>
</form>

</body>
</html>
