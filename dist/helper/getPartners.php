<?php

/* PHP Script to get nearest Places from DB based on Coordinates */
header('Content-Type: application/json');
require('connection.php');
require('distance.php');

if(isset($_POST['usr_lat']) && isset($_POST['usr_lon'])){
	$usr_lat = floatval($_POST['usr_lat']);
	$usr_lon = floatval($_POST['usr_lon']);


	// Create connection
	$conn = new mysqli($db['servername'], $db['username'], $db['password'], $db['dbname']);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	$sql = "SELECT id, name, street, plz, town, mail, web, phone, lat, lon, license_a, license_b, license_c FROM tbl_locations ORDER BY id";

	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$helper = array();
		$nearestPartners = array();
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	    	$partner_lat = floatval($row['lat']);
	    	$partner_lon = floatval($row['lon']);
	    	$partners[] = array(
	    						'distance' => nxProductions::distance($partner_lat, $partner_lon, $usr_lat, $usr_lon, "K" ), 
	    						'lat' => $partner_lat, 
	    						'lon' => $partner_lon,
	    						'details' => array(
	    							'name'=> $row['name'], 
	    							'street' => $row['street'], 
	    							'zip' => $row['plz'], 
	    							'town' => $row['town'], 
	    							'mail' => $row['mail'], 
	    							'phone' => $row['phone'], 
	    							'web' => $row['web'],
	    							'license_a' => $row['license_a'],
	    							'license_b' => $row['license_b'],
	    							'license_c' => $row['license_c']
	    							)
	    						);

	    	
	    	$locationstring = str_replace(' ', '+', $row["name"]);

	    }

	    $helper = array();
	    
		foreach ($partners as $key => $row)
		{
		    $helper[$key] = $row['distance'];
		}
		array_multisort($helper, SORT_ASC, $partners);

	    echo json_encode($partners);
	} else {

	    echo json_encode(array('Error' => 'Das hat nicht funktioniert'));
	}

	$conn->close();

}else{
	echo 'Error';
}

?>