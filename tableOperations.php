<?php
	include_once('config.php');

	if(function_exists($_GET['f'])) {
		$_GET['f'](); 
	}
	function getTableColumns(){
		$con = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD,'information_schema');
		// Check connection
		if (mysqli_connect_errno()) {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		// escape variables for security
		$tablename = (string)$_POST['action'];

		$sql = "SELECT column_name FROM COLUMNS WHERE TABLE_NAME='".$tablename."'  AND TABLE_SCHEMA = '".DB_DATABASE."'";

		$result = mysqli_query($con,$sql);
		
		$result_array_select_tables;
		for($i = 0; $i <= $result->num_rows; $i++){
			$result_array_select_tables[] = mysqli_fetch_array($result);
		}
		$stringWithColumns = "<div class='tableColumns'>";
		for($i=0; $i < count($result_array_select_tables); $i++){			
			for($j=0; $j<count($result_array_select_tables[$i])/2; $j++) {
				$stringWithColumns .= "- " . $result_array_select_tables[$i][$j] . "<br>";
			}
		}
		$stringWithColumns .= "</div>";
		echo $stringWithColumns;
		
		mysqli_close($con);
	}

?>