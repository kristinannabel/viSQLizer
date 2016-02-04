<?php
include_once('config.php');

if(function_exists($_GET['f'])) {
	$_GET['f'](); 
}
function save_query(){
	$con = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD,ADMIN_DATABASE);
	// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	// escape variables for security
	$sqlquery = (string)$_POST['action'];

	$check = "SELECT * FROM saved_queries WHERE username = '".DECOMPOSE_USER."' AND query = '".$sqlquery."'";
	$checkresult = mysqli_query($con,$check);
	if($checkresult->num_rows > 0){
		echo "excist";
	}
	else {
	$sql = "INSERT INTO saved_queries (username, query) 
	VALUES ('".DECOMPOSE_USER."','".$sqlquery."')";

	if (!mysqli_query($con,$sql)) {
	  die('Error: ' . mysqli_error($con));
	}
	echo "success";
	}
	mysqli_close($con);
}
function delete_query(){
	$con = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD,ADMIN_DATABASE);
	// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	// escape variables for security
	$sqlquery = (string)$_POST['action'];

	$check = "SELECT * FROM saved_queries WHERE username = '".DECOMPOSE_USER."' AND query = '".$sqlquery."'";
	$checkresult = mysqli_query($con,$check);
	if($checkresult->num_rows > 0){
		// Delete query from table
		$sql = "DELETE FROM saved_queries WHERE username = '".DECOMPOSE_USER."' AND query = '".$sqlquery."'";

		if (!mysqli_query($con,$sql)) {
		  die('Error: ' . mysqli_error($con));
		}
		echo "success";
	}
	else { // if the query wasn't found in the table
		echo "error";
	
	}
}

?>