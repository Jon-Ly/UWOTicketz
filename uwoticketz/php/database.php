<?php

$host = "localhost";
$user = "";
$pass = "";

$databaseName = "lyj47";

$conn = mysqli_connect($host, $user, $pass);

if (mysqli_connect_error()) {
    die("Database connection failed: " . mysqli_connect_error());
    echo "FAILED TO CONNECT";
}

mysqli_select_db($conn, $databaseName);

$array = [];

if($results = mysqli_query($conn, "CALL 'GetAllTickets'()")){
	while($row = mysqli_fetch_row($results)){
		array_push($array, $row[0]);
	}
}else{
	echo "FAILURE";
}

echo json_encode($array);

?>