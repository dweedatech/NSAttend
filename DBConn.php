<?php

// DWEEDA version

// Define constants so that they can't be changed
DEFINE ('DB_NAME', '954740_dwc');
DEFINE ('DB_USER', '954740_cwd');
DEFINE ('DB_PASSWORD', '0CUh3>X3L159VT');
DEFINE ('DB_HOST', 'mysql51-049.wc1.dfw1.stabletransit.com');
	 
global $mysqli;
	 
// $dbc will contain a resource link to the database
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (mysqli_connect_error()) {
	echo 	mysqli_connect_error();
	exit;
}

// echo '<script type="text/javascript">alert("DBconn.php");</script>';

 ?>