<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit();
}
?>
<html>
<head>
<title>Accounts Management Page - Equipment Management System</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Open Sans', sans-serif;
        font-size: 14px;
        background: url("bggg.jpg") no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        padding: 20px;
        color: #fff;
    }

    h1, h4 {
        margin: 5px 0;
        font-weight: 500;
        color: #fff;
    }

    .msg {
        color: #4CAF50;
        font-weight: 600;
        font-size: 13px;
    }
    .error {
        color: #f44336;
        font-weight: 600;
        font-size: 13px;
    }

    .container {
        background: rgba(0, 0, 0, 0.7);
        border: 1px solid #2e2e2e;
        border-radius: 8px;
        padding: 20px;
        width: 90%;
        max-width: 1000px;
        margin: 20px auto;
        text-align: center;
    }

    a {
        text-decoration: none;
        color: #fff;
        font-size: 13px;
        margin: 0 8px;
        font-weight: 500;
    }
    a:hover {
        text-decoration: underline;
    }

    input[type="text"] {
        padding: 6px 10px;
        font-size: 13px;
        border: 1px solid #444;
        border-radius: 5px;
        background: #222;
        color: #fff;
    }
    input[type="submit"], .btn-link {
        padding: 6px 14px;
        font-size: 13px;
        border: none;
        border-radius: 5px;
        background-color: #333;
        color: #fff;
        cursor: pointer;
        font-weight: 500;
    }
    input[type="submit"]:hover {
        background-color: #555;
    }
    .btn-link {
        background: none;
        color: #fff;
    }

    /* Transparent table box like login */
    .table-box {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid #2e2e2e;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 13px;
    }
    th, td {
        padding: 10px 12px;
        text-align: center;
        color: #fff;
    }
    th {
        background-color: rgba(0, 0, 0, 0.8);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    tr:nth-child(even) {
        background-color: rgba(255, 255, 255, 0.05);
    }
    tr:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    td a {
        margin: 0 6px;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    td a:first-child {
        background-color: #444;
        color: #fff;
    }
    td a:first-child:hover {
        background-color: #666;
    }
    td a:last-child {
        background-color: #b58900; /* dark yellow */
        color: #fff;
    }
    td a:last-child:hover {
        background-color: #7c6100;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.7);
    }
    .modal-content {
        background-color: #222;
        margin: 12% auto;
        padding: 20px;
        border-radius: 8px;
        width: 350px;
        text-align: center;
        color: #fff;
        font-size: 14px;
    }
    .modal button {
        margin: 8px;
        padding: 6px 14px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 13px;
    }
    .btn-cancel {
        background-color: #555;
        color: white;
    }
    .btn-delete {
        background-color: #b58900;
        color: white;
    }
    .btn-cancel:hover {
        background-color: #777;
    }
    .btn-delete:hover {
        background-color: #7c6100;
    }
</style>
</head>
<body>
    <div class="container">
        <?php
        echo "<h1>Welcome, " . htmlspecialchars($_SESSION['username']) . "</h1>";
        echo "<h4>Account type: " . htmlspecialchars($_SESSION['usertype']) . "</h4>";

        if (isset($_GET['msg'])) {
            if ($_GET['msg'] == "deleted") {
                echo "<p class='msg'>User account deleted successfully.</p>";
            } elseif ($_GET['msg'] == "updated") {
                echo "<p class='msg'>User account updated successfully.</p>";
            } elseif ($_GET['msg'] == "created") {
                echo "<p class='msg'>User account created successfully.</p>";
            }
        }
        ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <a href="create-account.php">Create new account</a> |
            <a href="logout.php">Logout</a>
            <br><br>
            <input type="text" name="txtsearch" placeholder="Search accounts">
            <input type="submit" name="btnsearch" value="Search">
            <a class="btn-link" href="<?php echo $_SERVER['PHP_SELF']; ?>">Reset</a>
        </form>

        <div class="table-box">
        <?php
        function buildtable($result) {
            if (mysqli_num_rows($result) > 0) {
                echo "<table>";
                echo "<tr>";
                echo "<th>Username</th><th>Usertype</th><th>Status</th><th>Created by</th><th>Date created</th><th>Actions</th>";
                echo "</tr>";

                while ($row = mysqli_fetch_array($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['usertype']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['createdby']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['datecreated']) . "</td>";
                    echo "<td>";
                    echo "<a href='update-account.php?username=" . urlencode($row['username']) . "'>Edit</a>";
                    echo "<a href='#' class='btn-open-modal' data-username='" . htmlspecialchars($row['username']) . "'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='error'>No record/s found.</p>";
            }
        }

        require_once "config.php";

        if (isset($_POST['btnsearch'])) {
            $sql = "SELECT * FROM tblaccounts WHERE username LIKE ? OR usertype LIKE ? ORDER BY username";
            if ($stmt = mysqli_prepare($link, $sql)) {
                $searchvalue = '%' . $_POST['txtsearch'] . '%';
                mysqli_stmt_bind_param($stmt, "ss", $searchvalue, $searchvalue);
                if (mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);
                    buildtable($result);
                } else {
                    echo "<p class='error'>ERROR loading data: " . mysqli_error($link) . "</p>";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            $sql = "SELECT * FROM tblaccounts ORDER BY username";
            if ($stmt = mysqli_prepare($link, $sql)) {
                if (mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);
                    buildtable($result);
                } else {
                    echo "<p class='error'>ERROR loading data: " . mysqli_error($link) . "</p>";
                }
                mysqli_stmt_close($stmt);
            }
        }
        ?>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Confirm Delete</h3>
            <p>Are you sure you want to delete <strong id="userToDelete"></strong>?</p>
            <button class="btn-cancel" onclick="closeModal()">Cancel</button>
            <a id="confirmDeleteBtn"><button class="btn-delete">Delete</button></a>
        </div>
    </div>

    <script>
        const modal = document.getElementById("deleteModal");
        const userToDelete = document.getElementById("userToDelete");
        const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

        document.querySelectorAll(".btn-open-modal").forEach(button => {
            button.addEventListener("click", function(e) {
                e.preventDefault();
                let username = this.getAttribute("data-username");
                userToDelete.textContent = username;
                confirmDeleteBtn.href = "delete-account.php?username=" + encodeURIComponent(username);
                modal.style.display = "block";
            });
        });

        function closeModal() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
