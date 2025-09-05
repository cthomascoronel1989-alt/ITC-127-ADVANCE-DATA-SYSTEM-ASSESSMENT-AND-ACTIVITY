<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once "config.php";

// Make sure username is provided via POST
if (isset($_POST['username']) && !empty($_POST['username'])) {
    $usernameToDelete = $_POST['username'];

    // Optional: Prevent deleting your own account
    if ($usernameToDelete === $_SESSION['username']) {
        echo "<script>alert('You cannot delete your own account!'); window.location.href='accounts-management.php';</script>";
        exit;
    }

    // Delete the user
    $sqlDelete = "DELETE FROM tblaccounts WHERE username = ?";
    if ($stmt = mysqli_prepare($link, $sqlDelete)) {
        mysqli_stmt_bind_param($stmt, "s", $usernameToDelete);
        if (mysqli_stmt_execute($stmt)) {
            // Log the deletion
            $sqlLog = "INSERT INTO tblaccountslogs (datelog, timelog, action, module, performedto, performedby) VALUES (?, ?, ?, ?, ?, ?)";
            if ($logStmt = mysqli_prepare($link, $sqlLog)) {
                $date = date("d/m/Y");
                $time = date("h:i:sa");
                $action = "Delete";
                $module = "Accounts Management";
                mysqli_stmt_bind_param($logStmt, "ssssss", $date, $time, $action, $module, $usernameToDelete, $_SESSION['username']);
                mysqli_stmt_execute($logStmt);
                mysqli_stmt_close($logStmt);
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Redirect back to accounts page
echo "<script>window.location.href='accounts-management.php';</script>";
exit;
