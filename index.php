<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title>SQL Decomposer</title>
			
			<link href="css/bootstrap.min.css" rel="stylesheet">
			<link href="css/starter-template.css" rel="stylesheet">
			<link href="css/stylesheet.css" rel="stylesheet">
			
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
			<script src="js/bootstrap.min.js"></script>
			<script type="text/javascript" src="js/script.js"></script>
			
            <?php 
			include_once('config.php');
			include_once('tbl.php');
			include_once('parser.php');
			
			$sql = $_POST["sql-input"];
			/*
			ini_set('display_errors',1);
			ini_set('display_startup_errors',1);
			error_reporting(-1);
			*/
			?>  
					
    </head>
    <body>
        <div class="page-header">
            <h1><a href="/"> SQL Decomposer</a> <small>  by MACS-students </small> </h1>
        </div>
		
		<div class="col-md-9 content">
			<div class="col-lg-7">
				
				<div class="alert alert-success alert-dismissible hidden query-was-saved" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>Success!</strong> The query was successfully saved
				</div>
				<div class="alert alert-warning alert-dismissible hidden query-excists" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <strong>Oh no!</strong> The query allready excists in the database
				</div>
			
				<form method="post" action="">
					<div class="input-group">
						<input type="text" id="sql-query-input" class="form-control" placeholder="Enter a SQL query.."
							<?php if(isset($sql)){ echo "value=\"" .$sql. "\"";}?>	name="sql-input">
							<span class="input-group-btn">
								<button class="btn btn-default decompose" type="submit">Decompose</button>
								<button class="btn btn-default" id="save_button"><span class="glyphicon glyphicon-floppy-disk"></span></button>
							</span>	
					</div>
				</form>
			
				<?php
					
					$con=mysqli_connect(DB_SERVER,TU_USER,TU_PASSWORD,DB_DATABASE);
					if (mysqli_connect_errno()) {	?>
						<div class="alert alert-danger" role="alert"> Failed to connect to MySQL:" <?php echo mysqli_connect_error();?> </div>	<?php	
					}
					
					if (isset($sql) && $sql != "")
					{
						// Check if the SQL contains any errors:
						$result = mysqli_query($con, $sql);
						$error = mysqli_error($con);
						
						if($result) 
						{
							
							$parser = new Parser($sql, $con);
							if(!isset($_COOKIE["pagemode"])) {
								$pagemode = "Single";
							} else {
								$pagemode = $_COOKIE["pagemode"];
							}
							//Sjekk om cookien er singlestep eller stream
							if($pagemode == "Single") {
								$parser->setMode('single');
								$parser->parse_sql_query();
								
							} else {
								$parser->setMode('stream');
								$parser->parse_sql_query();
							}
							?>
							<br>
							<?php
							if(!isset($_POST["stepnumber"])){
								$parser->displayResult(1);
							}
							else {
								$parser->displayResult($_POST["stepnumber"]);
							}
							
						} 
						else
						{
							
							// Remove the database-name from the error:
							$error = str_replace(DB_DATABASE.".", "", $error)
						
				?>
							<br><div class="alert alert-danger" role="alert">
								<?php	echo $error; ?>
							</div>
				<?php
							
						}
					
					} ?>			
			</div> <!-- class="col-lg-7 -->
		</div> <!-- class="col-md-9 -->
		<div class="col-md-3 menu">
			<div class="menu-content">
				<div class="panel panel-info">
				  <div class="panel-heading"><h3 class="panel-title"><span class="glyphicon glyphicon-cog"></span> Decomposer mode</h3></div>
				  <div class="panel-body">
					<div class="checkbox">
						<label>
							<?php 
								if($_COOKIE["pagemode"] == "Single"){
									echo "<input type='checkbox' name='pagemode' value='Single' checked> Single-step mode";
								}
								else{
									echo "<input type='checkbox' name='pagemode' value='Single'> Single-step mode";
								}
							?>
						</label>
					</div>
					<div class="checkbox">
						<label>
							<?php 
								if($_COOKIE["pagemode"] == "Stream"){
									echo "<input type='checkbox' name='pagemode' value='Stream' checked> Stream mode";
								}
								else{
									echo "<input type='checkbox' name='pagemode' value='Stream'> Stream mode";
								}
							?>
						</label>
					</div>
				  </div>
				</div>
				
				<div class="panel panel-info">
				  <div class="panel-heading"><h3 class="panel-title"><span class="glyphicon glyphicon-th-list"></span> Database tables</h3></div>
					<?php
						$tables = "SHOW TABLES";
						
						$tablesresult = mysqli_query($con,$tables);
						
						$result_array_select_tables;
						for($i = 0; $i <= $tablesresult->num_rows; $i++){
							$result_array_select_tables[] = mysqli_fetch_array($tablesresult);
						}
						
						$MENUTBL = new tbl();
						$MENUTBL->make_table($result_array_select_tables, false, "databaseTables");
					?>
				</div>
				<div class="panel panel-info">
				  <div class="panel-heading"><h3 class="panel-title"><span class="glyphicon glyphicon-star"></span> Saved queries</h3></div>
					<?php
						$tucon = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD,TU_DATABASE);
						$favorites = "SELECT query FROM saved_queries WHERE username = '" . TU_USER. "'";
						
						$favoritesresult = mysqli_query($tucon,$favorites);
						
						$result_array_select_favorites;
						for($i = 0; $i <= $favoritesresult->num_rows; $i++){
							$result_array_select_favorites[] = mysqli_fetch_array($favoritesresult);
						}
						
						$MENUTBLFAV = new tbl();
						$MENUTBLFAV->make_table($result_array_select_favorites, false, "savedQueries");
					?>
				</div>
			</div>
		</div>
    </body>
</html>
