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
			
			<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
			<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
			<script type="text/javascript" src="js/bootstrap.min.js"></script>
			<script src="pixi/bin/pixi.js"></script>
			<script type="text/javascript" src="js/script.js"></script>
			<script src="livequery/jquery.livequery.js"></script>
			<script src="https://code.createjs.com/easeljs-0.8.2.min.js"></script>
			<script src="https://code.createjs.com/createjs-2015.11.26.min.js"></script>
			
			
            <?php 
			include_once('config.php');
			include_once('tbl.php');
			include_once('parser.php');
			if($_POST){
				$sql = $_POST["sql-input"];
			}
			
			ini_set('display_errors',1);
			ini_set('display_startup_errors',1);
			error_reporting(-1);
			
			?>  
				
		<script>      
			function init() {
				if($(".streammode-panel").length != 0){
		        	var stage = new createjs.Stage("demoCanvas");
		  			var canvas = document.getElementById("demoCanvas");
		  			var table = document.getElementById("main-panel streammode-panel");
		  			var formDOMElement = new createjs.DOMElement("main-panel streammode-panel");
	
		  	  		stage.canvas.width = formDOMElement.htmlElement.clientWidth + 2;
		  			stage.canvas.height = formDOMElement.htmlElement.clientHeight + 2;
		  			//move it's rotation center at the center of the form
		  			formDOMElement.regX = table.offsetWidth*0.5;
		  			formDOMElement.regY = table.offsetHeight*0.5;
		  			//move the form above the screen
		  			formDOMElement.x = canvas.width * 0.5;
		  			formDOMElement.y = canvas.height * -0.50; //- 200;
	
		        	stage.addChild(formDOMElement);
		        	stage.update();
		  	  		var thCount = $(".empty-table").children('tbody').find('tr').first().find("th").length;
		  			for(var i = 0; i < thCount; i++){
		  				var spanName = ".span_" + i;
		  				$(".empty-table").find(spanName).css("visibility","hidden");
		  			}
					
					
					
					var textDOM = new createjs.DOMElement("span_0");
					stage.addChild(textDOM);
		  		  	createjs.Tween.get(textDOM, {loop: false})
					.wait(1500)
					.to({y: 216}, 1000, createjs.Ease.getPowIn(1));
					$("#original-td_0").find(".original-span_0").show();
					
					var textDOM1 = new createjs.DOMElement("span_1");
					stage.addChild(textDOM1);
		  		  	createjs.Tween.get(textDOM1, {loop: false})
					.wait(1500)
					.to({y: 216}, 1000, createjs.Ease.getPowIn(1));
					$("#original-td_0").find(".original-span_1").show();
					
					var textDOM2 = new createjs.DOMElement("span_2");
					stage.addChild(textDOM2);
		  		  	createjs.Tween.get(textDOM2, {loop: false})
					.wait(1500)
					.to({y: 216}, 1000, createjs.Ease.getPowIn(1));
					$("#original-td_0").find(".original-span_2").show();
					
		          	  //.to({alpha: 0, y: 75}, 500, createjs.Ease.getPowInOut(2))
		           	  //.to({alpha: 0, y: 216}, 100)
					//.to({alpha: 1, y: 0}, 100);
		          	  //.to({alpha: 1, y: 100}, 500, createjs.Ease.getPowInOut(2))
		           	  //.to({x: 100}, 800, createjs.Ease.getPowInOut(2));
		         	createjs.Ticker.setFPS(60);
		         	createjs.Ticker.addEventListener("tick", stage);
				}
			}
		</script>
    </head>
    <body onload="init();">
        <div class="page-header">
            <h1><a href="/"> viSQLizer</a> <small>  SQL learning tool </small> </h1>
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
							<? if(isset($sql)){ echo "value=\"" .$sql. "\"";}?>	name="sql-input">
							<span class="input-group-btn">
								<button class="btn btn-default decompose" type="submit">Decompose</button>
								<button class="btn btn-default" id="save_button"><span class="glyphicon glyphicon-floppy-disk"></span></button>
							</span>	
					</div>
				</form>
			
				<?php
					
					$con=mysqli_connect(DB_SERVER,DECOMPOSE_USER,DECOMPOSE_PASSWORD,DECOMPOSE_DATABASE);
					if (mysqli_connect_errno()) {	?>
						<div class="alert alert-danger" role="alert"> Failed to connect to MySQL:" <?php echo mysqli_connect_error();?> </div>	
						
						
						<?php	
					}?>
					<canvas id="demoCanvas" width="500" height="300"></canvas>
					<?php
					if (isset($sql) && $sql != "")
					{
						// Check if the SQL contains any errors:
						$result = mysqli_query($con, $sql);
						$error = mysqli_error($con);
						
						if($result) 
						{
							$parser = new Parser($sql, $con);
							$parser->setMode('stream');
							$parser->parse_sql_query();
							
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
