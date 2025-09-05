<?php
//define database connection
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'Joshua');
define('DB_PASSWORD', 'coronel');
define('DB_NAME', 'itc127batch22025-assesment');
//attempt to connect
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
//check if the connection is unsuccessful
if($link === false) {
	die("ERROR: Could not connect," . mysqli_connect_error());
}
//set time zone
date_default_timezone_set('Asia/Manila');
?>