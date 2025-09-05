<?php
require_once "config.php";
include("session-checker.php");

// check if username is provided
if (isset($_GET['username']) && !empty(trim($_GET['username']))) {
    $usernameToDelete = trim($_GET['username']);

    // delete account
    $sql = "DELETE FROM tblaccounts WHERE username = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $usernameToDelete);

        if (mysqli_stmt_execute($stmt)) {
            // insert into logs
            $sqlLog = "INSERT INTO tbllogs (datelog, timelog, action, module, performedto, performedby) 
                       VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmtLog = mysqli_prepare($link, $sqlLog)) {
                $date = date("d/m/Y");
                $time = date("h:i:sa");
                $action = "Delete";
                $module = "Accounts Management";
                mysqli_stmt_bind_param(
                    $stmtLog,
                    "ssssss",
                    $date,
                    $time,
                    $action,
                    $module,
                    $usernameToDelete,
                    $_SESSION['username']
                );
                mysqli_stmt_execute($stmtLog);
                mysqli_stmt_close($stmtLog);
            }

            // redirect with success message
            header("Location: accounts-management.php?msg=deleted");
            exit();
        } else {
            die("Error: Could not delete the account. " . mysqli_error($link));
        }
        mysqli_stmt_close($stmt);
    }
} else {
    // if no username provided
    header("Location: accounts-management.php?msg=error");
    exit();
}
?>
