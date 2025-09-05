<?php
require_once "config.php";
include("session-checker.php");

// Check if AssetNumber is provided
if (isset($_GET['AssetNumber'])) {
    $AssetNumberToDelete = $_GET['AssetNumber'];

    // Delete the equipment
    $sqlDelete = "DELETE FROM tblequipments WHERE AssetNumber = ?";
    if ($stmtDelete = mysqli_prepare($link, $sqlDelete)) {
        mysqli_stmt_bind_param($stmtDelete, "s", $AssetNumberToDelete);
        if (mysqli_stmt_execute($stmtDelete)) {
            mysqli_stmt_close($stmtDelete);

            // Log deletion
            $sqlLog = "INSERT INTO tblequipmentslogs (DateLog, TimeLog, AssetNumber, PerformedBy, Action, Module)
                       VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmtLog = mysqli_prepare($link, $sqlLog)) {
                $DateLog = date("d/m/Y");
                $TimeLog = date("h:i:sa");
                $Action = "Delete";
                $Module = "Equipments Management";
                mysqli_stmt_bind_param(
                    $stmtLog,
                    "ssssss",
                    $DateLog,
                    $TimeLog,
                    $AssetNumberToDelete,
                    $_SESSION['username'],
                    $Action,
                    $Module
                );
                mysqli_stmt_execute($stmtLog);
                mysqli_stmt_close($stmtLog);
            }
        }
    }
}

// Redirect back to equipment list
header("Location: equipments.php");
exit;
