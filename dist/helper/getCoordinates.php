<?php

/* PHP Script to get Coordinates for ZIP Code */
header('Content-Type: application/json');
require('connection.php');

if(isset($_POST['zipcode']) && strlen($_POST['zipcode']) > 3){
	$zip = $_POST['zipcode'];


	//***** Temporary Values for testing

	$usr_lat = 47.0503;
	$usr_lon = 9.41446;

	//***** Temporary Values for testing

	// Create connection
	$conn = new mysqli($db['servername'], $db['username'], $db['password'], $db['dbname']);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
	// Create the Query
	$sql = "SELECT plz, town, kanton, lat, lon FROM tbl_switzerland WHERE plz = $zip LIMIT 1";
	// Get the Data
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	    	$element = array('zip' => $row['plz'], 'town' => $row['town'], 'kanton' => $row['kanton'], 'lat' => $row['lat'], 'lon' => $row['lon'], 'error' => '');
	    }

	    echo json_encode($element);

	}else{
		echo json_encode(array('error' => 'No entry for ZIP Code'));
	}

	

	$conn->close();

}else{
	echo json_encode(array('error' => 'No Data transferred'));
}

?>