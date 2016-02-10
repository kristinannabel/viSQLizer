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
						$(".empty-table").find(".textOrigin").hide();
						var numOfSpans = $(".empty-table").find(spanName).length;
						for(var j = 0; j < numOfSpans; j++){
							while ($(".empty-table").find(spanName).eq(j).height()>16) {
						    	$(".empty-table").find(spanName).eq(j).text(function (index, text) {
						        	return text.replace(/\W*\s(\S)*$/, '...');
						    	});
							}
						}			
		  			}
					
					
					
					//if($(".org-db-table").length != 0){ 
						var numberOfTables = $(".original-table").length;
						var tableRows = $("#empty-table").find("tr.data").length;
						var tableColumns = $("#empty-table").find("tr.data:first").find("td").length;
						var timeCount = 1500;
						for(var i = 0; i < tableRows; i++) (function(i){ //for each row
							//$(".original-table").find("tr#data").eq(i).css("background-color", "red");
							for(var j = 0; j < tableColumns; j++) (function(j){ //for each column in one row
								var rowCount = i + 2;
								var columnCount = j + 1;
								var textContent = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").find("span.textOrigin").html();
								var emptyContains = $("#empty-table").find("span:contains('"+textContent+"')").length;
								console.log(textContent);
								
								var numOfThisElem = $("#empty-table").find("tr:nth-child("+rowCount+")").find("span.textOrigin:contains('"+textContent+"')").length;
								var numOfThisElemNotUsed = $("#empty-table").find("tr:nth-child("+rowCount+")").find("span.textOrigin:not(.used):contains('"+textContent+"')").length;
								var elemNumInRow = numOfThisElem - numOfThisElemNotUsed;
								debugger;
								$("#empty-table").find("tr:nth-child("+rowCount+")").find("span.textOrigin:contains('"+textContent+"')").eq(elemNumInRow).addClass("used");
								
								if(elemNumInRow > 0){
									var thisGetTextOrigin = $(".original-table").find("span:not(.used):not(.duplicate)").filter(function(){
											return $(this).html() === textContent;
									});
								}
								else {
									var thisGetTextOrigin = $(".original-table").find("span:not(.used)").filter(function(){
											return $(this).html() === textContent;
									});
								}
								
								var isRightElem = false;
								var thisTable = thisGetTextOrigin.first().parent().parent().parent().parent();
								var duplData = $(thisTable).find("span:not(.used):contains('"+textContent+"')").length;
								var columnIndexOriginal = thisGetTextOrigin.first().parent().index();
								var columnIndexEmpty = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").index();
								var isNotFirstColumn = false;
								if((columnIndexOriginal > 0)&&(columnIndexEmpty > 0)){
									isNotFirstColumn = true;
								}
								
								var nextEmptyTableColumn = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").parent().parent().children().first().find("th").eq(columnIndexEmpty-1).html();
								var nextOriginalTableColumn = thisGetTextOrigin.first().parent().parent().parent().children().first().find("th").eq(columnIndexOriginal-1).html();
								
								if(duplData > 1){
									while(isRightElem == false){
										if(thisGetTextOrigin.first().parent().is(':first-child')){
											var getTextOrigin = $(thisGetTextOrigin[0]).attr("id","animThis").next();
											isRightElem = true;
										}
										else if(thisGetTextOrigin.first().parent().parent().has(".usedInRow").length != 0){//.length != 0){
											var getTextOrigin = $(thisGetTextOrigin[0]).attr("id","animThis").next();
											isRightElem = true;
										}
										else if((!thisGetTextOrigin.first().parent().siblings().find("span:not(#original-span)").hasClass("used")) && ((isNotFirstColumn) && (nextEmptyTableColumn != nextOriginalTableColumn))){
											//This element is first to be used in row even if it is not first element
											var getTextOrigin = $(thisGetTextOrigin[0]).attr("id","animThis").next();
											isRightElem = true;
										}
										else{
											thisGetTextOrigin.first().addClass("notInUse");
											var thisGetTextOrigin = $(".original-table").find("span:not(.used):not(.notInUse)").filter(function(){
												return $(this).html() === textContent;
											});
										}
									}
								}
								else {
									var getTextOrigin = $(thisGetTextOrigin[0]).attr("id","animThis").next();
								}
								
								var originalPosY = $(".original-table").find("span#animThis").first().position().top;
								
								var emptyPosY = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").find("span:not(.textOrigin)").position().top;
								var emptyPosX = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").find("span:not(.textOrigin)").position().left;
								if(numberOfTables > 1){
									var thisTableTemp = $(".original-table").find("span#animThis").parent().parent().parent().parent().attr("id");
									var thisTable = "#" + thisTableTemp;
									var orPosX = $(thisTable).find("span#animThis").position().left;
								}
								else {
									var orPosX = 									$(".original-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").find("span").position().left;
								}
								var calcPositionY = emptyPosY - originalPosY;
								var calcPositionX = emptyPosX - orPosX;
								var thisDOMElem = $("#animThis:not(.used)").get(0);
								var textDOM = new createjs.DOMElement(thisDOMElem);
								stage.addChild(textDOM);
								if(numberOfTables > 1){
									getTextOrigin.show();
					  		  		createjs.Tween.get(textDOM, {loop: false})
									.wait(timeCount).call(tweenStart)
									.to({y: calcPositionY, x: calcPositionX}, 1500, createjs.Ease.getPowIn(1))
									.to({alpha: 0}, 0, createjs.Ease.getPowIn(1))
									.to({alpha: 1, y: 0, x: 0}).call(tweenComplete);
								} else {
					  		  		createjs.Tween.get(textDOM, {loop: false})
									.wait(timeCount).call(tweenStart)
									.to({y: calcPositionY, x: calcPositionX}, 1500, createjs.Ease.getPowIn(1));
									//console.log($("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").find("span").position());
									getTextOrigin.show();
								}
								//$(".original-table").find("span:not(.used)").contains(textContent);
								if(numberOfTables > 1){
									var thisTableIdTemp = $(".original-table").find("span#animThis").parent().parent().parent().parent().attr("id");
									var thisTableId = "#" +thisTableIdTemp;
									var countOfDuplicates = $(thisTableId).find("span:not(#original-span):contains('"+textContent+"')").length;
									console.log("Duplicates: " + countOfDuplicates + " - ON: "+ textContent + " TABLE: " + thisTableId);
									if(countOfDuplicates > 1){
										$(".original-table").find("span#animThis").removeAttr("id").addClass("used").parent().addClass("usedInRow");
									}else{
										var duplicatesInAllTables = $(".original-table").find("span:not(.used):contains('"+textContent+"')").length;
										if(duplicatesInAllTables > countOfDuplicates){
											$(".original-table").find("span#animThis").first().addClass("duplicate").parent().addClass("usedInRow");
										}
										$(".original-table").find("span#animThis").first().removeAttr("id").parent().addClass("usedInRow");
									}
								}
								else {
									$(".original-table").find("span#animThis").removeAttr("id").addClass("used").parent().addClass("usedInRow");
								}
								
								function tweenComplete(){
									var emptyTextPlace = $("#empty-table").find("tr:nth-child("+rowCount+")").find("td:nth-child("+columnCount+")").find("span");
									emptyTextPlace.css("visibility", "visible");
								}
								
								function tweenStart(){
									$(this.htmlElement.parentElement.parentElement).css("background-color", "#d9edf7");
								}
								
							})(j);
							timeCount += 2000;
							$(".original-table").find(".usedInRow").removeClass("usedInRow");
						})(i);
					
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
