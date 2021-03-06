<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title>viSQLizer</title>
			
			<link href="css/bootstrap.min.css" rel="stylesheet">
			<link href="css/starter-template.css" rel="stylesheet">
			<link href="css/stylesheet.css" rel="stylesheet">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
			
			<script src="http://code.jquery.com/jquery-2.2.0.min.js"></script>
			<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
			<script type="text/javascript" src="js/bootstrap.min.js"></script>
			<script src="pixi/bin/pixi.js"></script>
			<script type="text/javascript" src="js/script.js"></script>
			<script type="text/javascript" src="js/visualization.js"></script>
			<script src="livequery/jquery.livequery.js"></script>
			<script src="https://code.createjs.com/easeljs-0.8.2.min.js"></script>
			<script src="https://code.createjs.com/createjs-2015.11.26.min.js"></script>
			
			
            <?php 
			include_once('config.php');
			include_once('tbl.php');
			include_once('parser.php');
			if($_POST){
				$sql = $_POST["sql-input"];
				$sql = str_replace('"',"'",$sql);
				$sql = str_replace(' 0'," '0'",$sql);
			}
			
			ini_set('display_errors',1);
			ini_set('display_startup_errors',1);
			error_reporting(-1);
			
			?>
    </head>
    <body onload="init();"><!---->
        <div class="page-header">
            <h1><a href=<?php ROOT?> > viSQLizer</a> <small>  SQL learning tool </small> </h1>
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
				
				<form method="post">
					<div class="btn-toolbar sql-op-btn-toolbar" role="toolbar" aria-label="...">
					  <div class="btn-group sql-op-btn-group" role="group" aria-label="...">
  				 	   <button type="button" class="btn btn-blue" onclick="$('#sql-query-input').val($('#sql-query-input').val() + ' SELECT '); var input = $('#sql-query-input');
input[0].selectionStart = input[0].selectionEnd = input.val().length;">SELECT</button>
					   <button type="button" class="btn btn-blue" onclick="$('#sql-query-input').val($('#sql-query-input').val() + ' FROM '); var input = $('#sql-query-input');
input[0].selectionStart = input[0].selectionEnd = input.val().length;">FROM</button>
					   <button type="button" class="btn btn-blue" onclick="$('#sql-query-input').val($('#sql-query-input').val() + ' WHERE '); var input = $('#sql-query-input');
input[0].selectionStart = input[0].selectionEnd = input.val().length;">WHERE</button>
					  </div>
					  <div class="btn-group sql-op-btn-group" role="group" aria-label="...">
  				 	   <button type="button" class="btn btn-danger" onclick="$('#sql-query-input').val(''); var input = $('#sql-query-input');
input[0].selectionStart = input[0].selectionEnd = input.val().length;">Erase</button>
					  </div>
		   			</div>
					<div class="input-group">
						<input type="text" id="sql-query-input" class="form-control" autocomplete="off" placeholder="Enter a SQL query.."
							<? if(isset($sql)){ echo "value=\"" .$sql. "\"";}?>	name="sql-input">
							<span class="input-group-btn">
								<button class="btn btn-default decompose" type="submit">Run new query!</button>
								<!--<button class="btn btn-default" id="save_button"><span class="glyphicon glyphicon-floppy-disk"></span></button>-->
							</span>	
					</div>
				</form>
			
				<?php
					
					$con=mysqli_connect(DB_SERVER,DECOMPOSE_USER,DECOMPOSE_PASSWORD,DECOMPOSE_DATABASE);
					if (mysqli_connect_errno()) {	?>
						<div class="alert alert-danger" role="alert"> Failed to connect to MySQL:" <?php echo mysqli_connect_error();?> </div>	
						
						
						<?php	
					}?>
					
					<canvas id="demoCanvas" width="0" height="0" style="z-index: 2; position: relative; pointer-events: none;"></canvas>
					
					<?php
					if (isset($sql) && $sql != "")
					{
						// Check if the SQL contains any errors:
						$result = mysqli_query($con, $sql);
						$error = mysqli_error($con);
						
						if($result) 
						{
							$sql = str_replace('"',"'",$sql);
							$parser = new Parser($sql, $con);
							$parser->setMode('stream');
							$parser->parse_sql_query();
							
							?>
							<br>
							<?php
							if(!isset($_POST["stepnumber"])){
								$parser->displayResult(1, $sql);
							}
							else {
								$parser->displayResult($_POST["stepnumber"], $sql);
							}
							
						} 
						else
						{
							
							// Remove the database-name from the error:
							$error = str_replace(DECOMPOSE_DATABASE.".", "", $error)
						
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
				<!--<div class="panel panel-default">
				  <div class="panel-body">
					<h5>Text animation</h5>
					<div class="radio"> <label> <input type="radio" name="animation" value="normal"> Normal animation </label> </div>
					<div class="radio"> <label> <input type="radio" name="animation" value="synchronous"> Synchronous animation </label> </div>
					<div class="checkbox"> <label> <input type="checkbox" name="dragout" value="dragout"> Drag-out animation </label> </div>
					<div class="checkbox"> <label> <input type="checkbox" name="bigtext" value="bigtext"> Big text animation </label> </div>
				  </div>
				</div>-->
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
						$tucon = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD,ADMIN_DATABASE);
						$favorites = "SELECT query FROM saved_queries WHERE username = '" . DECOMPOSE_USER. "'";
						
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
