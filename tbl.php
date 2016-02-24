<?php

class tbl{
		
	function make_table($result, $show_headings, $tablename="", $empty=false, $step=1, $tableName=array(), $whereColumns=array(), $onColumns=array()) {
		//print_r($onColumns);
		if(($tablename == "savedQueries") || ($tablename == "databaseTables")){
		?>

		<table class='table table-bordered <?php echo $tablename; ?>' id='<?php echo $tablename; ?>'>
			<tbody>
			<tr> 
			
				<?php
					$keys = array_keys($result[0]);
				
					// Table headings:
					if($show_headings){
						for($i=1; $i < count($keys); $i+= 2) {
							echo '<th class="' . $keys[$i] . '"><p>' . $keys[$i] . '</p></th>';	
						} 
				}?>
			</tr>
			<?php
				// Table data:
				for($i=0; $i < count($result); $i++){ ?>
					<tr>
						<?php 				
							for($j=0; $j<count($result[$i])/2; $j++) {
								echo '<td>' . $result[$i][$j] . '</td>';
								if($tablename == "savedQueries"){
									echo '<td style="width: 70px;"><button style="margin-right: 5px;" type="button" class="btn btn-default btn-xs edit_button"><span class="glyphicon glyphicon-play"></span></button></td>';
									
								}
								else if($tablename == "databaseTables") {
									echo '<td><span class="glyphicon glyphicon-chevron-down pull-right"></span></td>';
								}
						}?>
					</tr>
					<?php
				} ?>
		</tbody>
		</table>
		<?php
		}else{
			if($step <= count($tableName[0])){
				$counter = $step-1;
				$query = "SELECT * FROM " . $tableName[0][$counter];
				$con=mysqli_connect(DB_SERVER,DECOMPOSE_USER,DECOMPOSE_PASSWORD,DECOMPOSE_DATABASE);
				$tableResult = mysqli_query($con, $query);
				
				for($j = 0; $j < $tableResult->num_rows; $j++){
					$finalTbResult[] = mysqli_fetch_array($tableResult);
				}
				$thisTableName = $tableName[0][$counter];
				
				$keyQuery = "SHOW INDEX FROM " . $tableName[0][$counter];
				$keyResult = mysqli_query($con, $keyQuery);
				
				for($j = 0; $j < $keyResult->num_rows; $j++){
					$finalKeyResult[] = mysqli_fetch_array($keyResult);
				}
		?>
		
		<table class='table table-bordered original-table org-db-table <?php echo $thisTableName; ?> <?php echo $tablename; ?>' id='<?php echo $thisTableName; ?>'>
			<tbody>
			<tr> 
	
				<?php
					$keys = array_keys($finalTbResult[0]);
					// Table headings:
					if($show_headings){
						for($k=1; $k < count($keys); $k+= 2) {
							$classNameKey = "";
							for($h=0; $h < count($finalKeyResult); $h++){
								if($h > 0){
									if(($keys[$k] == $finalKeyResult[$h]['Column_name']) && ($finalKeyResult[$h]['Column_name'] != $finalKeyResult[$h-1]['Column_name'])){
										$classNameKey = "primary_key";
										if($finalKeyResult[$h]['Non_unique'] > 0){
											$classNameKey = "foreign_key";
										}
									}
								}
								else {
									if($keys[$k] == $finalKeyResult[$h]['Column_name']){
										$classNameKey = "primary_key";
										if($finalKeyResult[$h]['Non_unique'] > 0){
											$classNameKey = "foreign_key";
										}
									}
								}
								
							}
							$whereClassName = "";
							for($r = 0; $r < count($whereColumns); $r++){
								if($keys[$k] == $whereColumns[$r]){
									$whereClassName = "where";
								}
							}
							
							$onClassName = "";
							if(!empty($onColumns)){
							for($r = 0; $r < count($onColumns[0]); $r++){
								if (($pos = strpos($onColumns[0][$r]['base_expr'], ".")) !== FALSE) { 
								    $thisOnColumn = substr($onColumns[0][$r]['base_expr'], $pos+1); 
									if($keys[$k] === $thisOnColumn){
										$onClassName = "onColumn";
									}
								}
							}
							}
							
							if($classNameKey != ""){
								echo '<th class="'. $classNameKey . ' ' . $onClassName . ' ' . $whereClassName . ' '. $keys[$k] . '"><p>' . $keys[$k] . '</p></th>';
							}
							else {
								echo '<th class="'. $whereClassName . ' ' . $onClassName . ' '. $keys[$k] . '"><p>' . $keys[$k] . '</p></th>';
							}
							
						} 
					}  
					unset($keys);
					?>
			</tr>
			<?php
				// Table data:
				for($m=0; $m < count($finalTbResult); $m++){ ?>
					<tr id="data"> <?php
							for($n=0; $n<count($finalTbResult[$m])/2; $n++) {
								echo '<td class="original-data original-data-'.$n.'"><span id="span_'.$n.'" class="span_'.$n.'">' . $finalTbResult[$m][$n] . '</span><span id="original-span" class="used original-span_'.$n.'">' . $finalTbResult[$m][$n] . '</span></td>';
							}?>
					</tr>
					<?php
				} ?>
		</tbody>
		</table> 
		<?php
			}else{ //When all original DB tables has been shown in each initial steps, begin showing the prev step result table
				
				
				for($i = 0; $i < $step; $i++){
					if($i < count($tableName[0])){
						$query = "SELECT * FROM " . $tableName[0][$i];
						$con=mysqli_connect(DB_SERVER,DECOMPOSE_USER,DECOMPOSE_PASSWORD,DECOMPOSE_DATABASE);
						$tableResult = mysqli_query($con, $query);
						
						for($j = 0; $j < $tableResult->num_rows; $j++){
							$finalTbResult[] = mysqli_fetch_array($tableResult);
						}
						$thisTableName = $tableName[0][$i];
						
						$keyQuery = "SHOW INDEX FROM " . $tableName[0][$i];
						$con=mysqli_connect(DB_SERVER,DECOMPOSE_USER,DECOMPOSE_PASSWORD,DECOMPOSE_DATABASE);
						$keyResult = mysqli_query($con, $keyQuery);
				
						for($j = 0; $j < $keyResult->num_rows; $j++){
							$finalKeyResult[] = mysqli_fetch_array($keyResult);
						}
						
				?>
				
				<table class='table table-bordered original-table <?php echo $thisTableName; ?> <?php echo $tablename; ?>' id='<?php echo $thisTableName; ?>'>
					<tbody>
					<tr> 
			
						<?php
							$keys = array_keys($finalTbResult[0]);
							// Table headings:
							if($show_headings){
								for($k=1; $k < count($keys); $k+= 2) {
									$classNameKey = "";
									for($h=0; $h < count($finalKeyResult); $h++){
										if($h > 0){
											if(($keys[$k] == $finalKeyResult[$h]['Column_name']) && ($finalKeyResult[$h]['Column_name'] != $finalKeyResult[$h-1]['Column_name'])){
												$classNameKey = "primary_key";
												if($finalKeyResult[$h]['Non_unique'] > 0){
													$classNameKey = "foreign_key";
												}
											}
										}else {
											if($keys[$k] == $finalKeyResult[$h]['Column_name']){
												$classNameKey = "primary_key";
												if($finalKeyResult[$h]['Non_unique'] > 0){
													$classNameKey = "foreign_key";
												}
											}
										}
									}
									$whereClassName = "";
									for($r = 0; $r < count($whereColumns); $r++){
										if($keys[$k] == $whereColumns[$r]){
											$whereClassName = "where";
										}
									}
									$onClassName = "";
									if(!empty($onColumns)){
									for($r = 0; $r < count($onColumns[0]); $r++){
										if (($pos = strpos($onColumns[0][$r]['base_expr'], ".")) !== FALSE) { 
										    $thisOnColumn = substr($onColumns[0][$r]['base_expr'], $pos+1); 
											if($keys[$k] === $thisOnColumn){
												$onClassName = "onColumn";
											}
										}
									}
									}
									
									if($classNameKey != ""){
										echo '<th class="'. $classNameKey . ' ' . $onClassName . ' ' . $whereClassName . ' '. $keys[$k] . '"><p>' . $keys[$k] . '</p></th>';
									}
									else {
										echo '<th class="'. $whereClassName . ' ' . $onClassName . ' ' .  $keys[$k] . '"><p>' . $keys[$k] . '</p></th>';
									}
								} 
							}  
							unset($keys);
							?>
					</tr>
					<?php
						
						// Table data:
						for($m=0; $m < count($finalTbResult); $m++){  ?>
							
							<tr id="data">	<?php
									for($n=0; $n<count($finalTbResult[$m])/2; $n++) {
										echo '<td class=" original-data original-data-'.$n.'"><span id="span_'.$n.'" class="span_'.$n.'">' . $finalTbResult[$m][$n] . '</span><span id="original-span" class="used original-span_'.$n.'">' . $finalTbResult[$m][$n] . '</span></td>';
									}?>
							</tr>
							<?php
						} ?>
				</tbody>
				</table>
				<?php
					}
					unset($finalTbResult);
				}
			}
		
		if($empty){?>
		<table class='table table-bordered empty-table' id='empty-table'>
			<tbody>
			<tr> 
			
				<?php
				
					$keys = array_keys($result[0]);
					// Table headings:
					if($show_headings){
						for($i=1; $i < count($keys); $i+= 2) {
							echo '<th class="' . $keys[$i] . '"><p>' . $keys[$i] . '</p></th>';	
						} 
					}?>
			</tr>
				<?php
					// Table data:
					for($i=0; $i < count($result); $i++){ 
						$thisRow = $i+1; 
						echo '<tr class="data" id="row_'.$thisRow.'">';
											
							for($j=0; $j<count($result[$i])/2; $j++) {
								if($j == 0){
									echo '<td><span class="textOrigin"  >' . $result[$i][$j] . '</span><span class="span_'.$j.'"  >' . $result[$i][$j] . '</span> <span class="glyphicon glyphicon-arrow-right" style="display: none;"></span></td>';
								}
								else {
									echo '<td><span class="textOrigin"  >' . $result[$i][$j] . '</span><span class="span_'.$j.'"  >' . $result[$i][$j] . '</span></td>';
								}
								
							}?>
						</tr>
						<?php
					} ?>
			</tbody>
			</table>
		<?php	
		}
	}
	}
}

?>