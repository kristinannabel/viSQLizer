<?php

class tbl{
		
	function make_table($result, $show_headings, $tablename="", $empty=false, $step=1, $tableName=array(), $whereColumns=array(), $onColumns=array(), $onOrderBy=array(), $onGroupBy=array(), $previousTable=array(), $sql="", $joinType=array(), $isMultipleJoin=false, $thisStep=1) {
		if(!empty($previousTable) && $step > 1){
			foreach($previousTable[$step-1] as $output) {
				if ($output['type']=='table') {
					$prevTable = $output['contents'];
				}
			}
		}
		$crossClassName = "";
		if(!empty($joinType['FROM'][1]['join_type'])){
			if($joinType['FROM'][1]['join_type'] === "CROSS"){
				$crossClassName = "cross";
			}
		}
		
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
			//echo count($tableName[0]);
			//echo $step;
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
				mysqli_close($con);
				
		echo "<b>" . $thisTableName . "</b>";?>
		
		<table class='table table-bordered original-table org-db-table <?php echo $thisTableName;?> <?echo $crossClassName;?> <?php echo $tablename; ?>' id='<?php echo $thisTableName; ?>'>
			<tbody>
			<tr> 
	
				<?php
					$keys = array_keys($finalTbResult[0]);
					$var = (string)$previousTable[$step][1]['contents'];
					$removeWords = array("returned", "the", "following", "table:", "<b>", "</b>", "<i>", "</i>");
					$tempString = str_replace($removeWords, "", $var);
					//$tempQuery = strstr($tempString, 'FROM');
					$tempQuery = $tempString . " LIMIT 1";
					mysqli_report(MYSQLI_REPORT_ERROR);
					$con=mysqli_connect(DB_SERVER,DECOMPOSE_USER,DECOMPOSE_PASSWORD,DECOMPOSE_DATABASE);
					$tempResult = mysqli_query($con, $tempQuery);
					mysqli_close($con);
					// Table headings:
					if($show_headings){
						for($k=1, $c=0; $k < count($keys); $k+= 2, $c++) {
							$finfo = mysqli_fetch_field_direct($tempResult, $c);
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
							if(!empty($whereColumns)){
								for($r = 0; $r < count($whereColumns[0]); $r++){
									if($keys[$k] == $whereColumns[0][$r]){
										$whereClassName = "where";
									}
								}
							}
							
							$onClassName = "";
							if(!empty($onColumns[1]['ref_clause'])){
							for($r = 0; $r < count($onColumns[1]['ref_clause'][0]); $r++){
								if (($pos = strpos($onColumns[1]['ref_clause'][0][$r]['base_expr'], ".")) !== FALSE) { 
								    $thisOnColumn = substr($onColumns[1]['ref_clause'][0][$r]['base_expr'], $pos+1); 
									if($keys[$k] === $thisOnColumn){
										$onClassName = "onColumn";
									}
								}
							}
							}
							$OrderByClassName = "";
							if(!empty($onOrderBy)){
								for($a = 0; $a < count($onOrderBy); $a++){
									if($keys[$k] === $onOrderBy[$a]["base_expr"]){
										$OrderByClassName = "orderByColumn";
									}
								}
							}
							$GroupByClassName = "";
							if(!empty($onGroupBy)){
								for($a = 0; $a < count($onGroupBy); $a++){
									if($keys[$k] === $onGroupBy[$a]["base_expr"]){
										$GroupByClassName = "orderByColumn";
									}
								}
							}
							
							if($classNameKey != ""){
								echo '<th class="'. $classNameKey . ' '. $GroupByClassName . ' ' . $OrderByClassName . ' ' . $onClassName . ' ' . $whereClassName . ' '. $keys[$k] . '" id="' . $finfo->orgtable . '"><p>' . $keys[$k] . '</p></th>';
							}
							else {
								echo '<th class="'. $whereClassName . ' ' . $GroupByClassName . ' ' . $OrderByClassName . ' ' . $onClassName . ' '. $keys[$k] . '" id="' . $finfo->orgtable . '"><p>' . $keys[$k] . '</p></th>';
							}
							
						} 
					}  
					unset($keys);
					mysqli_free_result($tempResult);
					?>
			</tr>
			<?php
				// Table data:
				for($m=0; $m < count($finalTbResult); $m++){ ?>
					<tr id="data"> <?php
							for($n=0; $n<count($finalTbResult[$m])/2; $n++) {
								if($finalTbResult[$m][$n] == ""){
									$finalTbResult[$m][$n] = "NULL";
								}
								echo '<td class="original-data original-data-'.$n.'"><span id="span_'.$n.'" class="span_'.$n.' extraSpan">' . utf8_encode($finalTbResult[$m][$n]) . '</span><span id="original-span" class="used original-span_'.$n.'">' . utf8_encode($finalTbResult[$m][$n]) . '</span><span class="textOrigin">'.utf8_encode($finalTbResult[$m][$n]).'</span></td>';
							}?>
					</tr>
					<?php
				} ?>
		</tbody>
		</table> 
		<?php
			}
			else { //When all original DB tables has been shown in each initial steps, begin showing the prev step result table
				if(($step > (count($tableName[0])+1)) && (!empty($prevTable))){ ?>
					
					<table class='table table-bordered original-table org-db-table <?echo $crossClassName; if($crossClassName != ""){echo " crossed";}?>' id='prevTable'>
						<tbody>
						<tr> 
			
							<?php
				
								$keys = array_keys($prevTable[0]);
								$var = (string)$previousTable[$step][1]['contents'];
								$removeWords = array("returned", "the", "following", "table:", "<b>", "</b>", "<i>", "</i>");
								$tempString = str_replace($removeWords, "", $var);
								$tempQueryOne = strstr($tempString, 'FROM');
								
								$tempQuery = "SELECT * " . $tempQueryOne . " LIMIT 1";
								$con=mysqli_connect(DB_SERVER,DECOMPOSE_USER,DECOMPOSE_PASSWORD,DECOMPOSE_DATABASE);
								$tempResult = mysqli_query($con, $tempQuery);
								mysqli_close($con);
								// Table headings:
								if($show_headings){
									for($i=1, $c=0; $i <= count($keys); $i+= 2, $c++) {
										if($i == count($keys) && !isset($keys[$i])){
											$finfo = mysqli_fetch_field_direct($tempResult, $c);
											
											$whereClassName = "";
											for($r = 0; $r < count($whereColumns); $r++){
												if(count($whereColumns[$r]) > 1){
													$numInArr = 1;
												}
												else {
													$numInArr = 0;
												}
												if($finfo->name == $whereColumns[$r][$numInArr]){
													$whereClassName = "where";
												}
											}
											$onClassName = "";
											if(!empty($onColumns[0][$thisStep]['ref_clause'])){
												for($r = 0; $r < count($onColumns[0][$thisStep]['ref_clause'][0]['sub_tree']); $r++){
													if (($pos = strpos($onColumns[0][$thisStep]['ref_clause'][0]['sub_tree'][$r]['base_expr'], ".")) !== FALSE) { 
														$thisOnColumn = substr($onColumns[0][$thisStep]['ref_clause'][0]['sub_tree'][$r]['base_expr'], $pos+1); 
														if($keys[$i] === $thisOnColumn){
															$onClassName = "onColumn";
														}
													}
												}
											}
											$OrderByClassName = "";
											if(!empty($onOrderBy)){
												for($a = 0; $a < count($onOrderBy); $a++){
													if($keys[$i] === $onOrderBy[$a]["base_expr"]){
														$OrderByClassName = "orderByColumn";
													}
												}
											}
											$GroupByClassName = "";
											if(!empty($onGroupBy)){
												for($a = 0; $a < count($onGroupBy); $a++){
													if($keys[$i] === $onGroupBy[$a]["base_expr"]){
														$GroupByClassName = "orderByColumn";
													}
												}
											}
											
											echo '<th class="' .$whereClassName .' '. $GroupByClassName . ' ' . $OrderByClassName . ' ' . $onClassName . ' '. $finfo->name . '" id="' . $finfo->orgtable . '"><p>' . $finfo->name . '</p></th>';	
											//Add classnames here as well later
										}
										else if(is_int($keys[$i])){
											$finfo = mysqli_fetch_field_direct($tempResult, $keys[$i]-1);
											
											$whereClassName = "";
											for($r = 0; $r < count($whereColumns); $r++){
												if(count($whereColumns[$r]) > 1){
													$numInArr = 1;
												}
												else {
													$numInArr = 0;
												}
												if($finfo->name == $whereColumns[$r][$numInArr]){
													$whereClassName = "where";
												}
											}
											$onClassName = "";
											if(!empty($onColumns[0][$thisStep]['ref_clause'])){
												for($r = 0; $r < count($onColumns[0][$thisStep]['ref_clause'][0]['sub_tree']); $r++){
													if (($pos = strpos($onColumns[0][$thisStep]['ref_clause'][0]['sub_tree'][$r]['base_expr'], ".")) !== FALSE) { 
														$thisOnColumn = substr($onColumns[0][$thisStep]['ref_clause'][0]['sub_tree'][$r]['base_expr'], $pos+1); 
														if($keys[$i] === $thisOnColumn){
															$onClassName = "onColumn";
														}
													}
												}
											}
											$OrderByClassName = "";
											if(!empty($onOrderBy)){
												for($a = 0; $a < count($onOrderBy); $a++){
													if($keys[$i] === $onOrderBy[$a]["base_expr"]){
														$OrderByClassName = "orderByColumn";
													}
												}
											}
											$GroupByClassName = "";
											if(!empty($onGroupBy)){
												for($a = 0; $a < count($onGroupBy); $a++){
													if($keys[$i] === $onGroupBy[$a]["base_expr"]){
														$GroupByClassName = "orderByColumn";
													}
												}
											}
											
											echo '<th class="' .$whereClassName .' '. $GroupByClassName . ' ' . $OrderByClassName . ' ' . $onClassName . ' '. $finfo->name . '" id="' . $finfo->orgtable . '"><p>' . $finfo->name . '</p></th>';	
											//Add classnames here as well later
										}
										else {
											$finfo = mysqli_fetch_field_direct($tempResult, $c);
											
											$whereClassName = "";
											for($r = 0; $r < count($whereColumns); $r++){
												if(count($whereColumns[$r]) > 1){
													$numInArr = 1;
												}
												else {
													$numInArr = 0;
												}
												if($keys[$i] == $whereColumns[$r][$numInArr]){
													$whereClassName = "where";
												}
											}
							
											$onClassName = "";
											if(!empty($onColumns[0][$thisStep]['ref_clause'])){
												for($r = 0; $r < count($onColumns[0][$thisStep]['ref_clause'][0]['sub_tree']); $r++){
													if (($pos = strpos($onColumns[0][$thisStep]['ref_clause'][0]['sub_tree'][$r]['base_expr'], ".")) !== FALSE) { 
														$thisOnColumn = substr($onColumns[0][$thisStep]['ref_clause'][0]['sub_tree'][$r]['base_expr'], $pos+1); 
														if($keys[$i] === $thisOnColumn){
															$onClassName = "onColumn";
														}
													}
												}
											}
											$OrderByClassName = "";
											if(!empty($onOrderBy)){
												for($a = 0; $a < count($onOrderBy); $a++){
													if($keys[$i] === $onOrderBy[$a]["base_expr"]){
														$OrderByClassName = "orderByColumn";
													}
												}
											}
											$GroupByClassName = "";
											if(!empty($onGroupBy)){
												for($a = 0; $a < count($onGroupBy); $a++){
													if($keys[$i] === $onGroupBy[$a]["base_expr"]){
														$GroupByClassName = "orderByColumn";
													}
												}
											}
											echo '<th class="'. $whereClassName . ' ' . $GroupByClassName . ' ' . $OrderByClassName . ' ' . $onClassName . ' '. $keys[$i] . '" id="' . $finfo->orgtable . '"><p>' . $keys[$i] .'</p></th>';
										}
									} 
								}unset($keys);
								mysqli_free_result($tempResult);
								?>
						</tr>
							<?php
								// Table data:
								for($i=0; $i < count($prevTable); $i++){ 
									$thisRow = $i+1; 
									echo '<tr class="data">';
											
										for($j=0; $j<count($prevTable[$i])/2; $j++) {
											if($prevTable[$i][$j] == ""){
												$prevTable[$i][$j] = "NULL";
											}
											echo '<td class="original-data original-data-'.$j.'"><span id="span_'.$j.'" class="span_'.$j.' extraSpan">' . utf8_encode($prevTable[$i][$j]) . '</span><span id="original-span" class="used original-span_'.$j.'">' . utf8_encode($prevTable[$i][$j]) . '</span><span class="textOrigin">'.utf8_encode($prevTable[$i][$j]).'</span></td>';
										}?>
									</tr>
									<?php
								} ?>
						</tbody>
						</table>
						<?php
						if($isMultipleJoin && $thisStep < count($tableName[0])){
							$query = "SELECT * FROM " . $tableName[0][$thisStep];
							$con=mysqli_connect(DB_SERVER,DECOMPOSE_USER,DECOMPOSE_PASSWORD,DECOMPOSE_DATABASE);
							$tableResult = mysqli_query($con, $query);
							
							for($j = 0; $j < $tableResult->num_rows; $j++){
								$queryResultJoin[] = mysqli_fetch_array($tableResult);
							} 
							echo "<b>" . $tableName[0][$thisStep] . "</b>";
							?>
							<table class='table table-bordered original-table <?php echo $tableName[0][$thisStep]; ?>' id='<?php echo $tableName[0][$thisStep]; ?>'>
								<tbody>
									<tr>
										<?php
										$keys = array_keys($queryResultJoin[0]);
										for($k=1, $c=0; $k < count($keys); $k+= 2, $c++) {
										$finfo = mysqli_fetch_field_direct($tableResult, $c);
											$onClassName = "";
											if(!empty($onColumns[0][$thisStep]['ref_clause'])){
												for($r = 0; $r < count($onColumns[0][$thisStep]['ref_clause'][0]['sub_tree']); $r++){
													if (($pos = strpos($onColumns[0][$thisStep]['ref_clause'][0]['sub_tree'][$r]['base_expr'], ".")) !== FALSE) { 
														$thisOnColumn = substr($onColumns[0][$thisStep]['ref_clause'][0]['sub_tree'][$r]['base_expr'], $pos+1); 
														if($keys[$k] === $thisOnColumn){
															$onClassName = "onColumn";
														}
													}
												}
											}
											$whereClassName = "";
											if(!empty($whereColumns)){
												for($r = 0; $r < count($whereColumns[0]); $r++){
													if($keys[$i] == $whereColumns[0][$r]){
														$whereClassName = "where";
													}
												}
											}
											$OrderByClassName = "";
											if(!empty($onOrderBy)){
												for($a = 0; $a < count($onOrderBy); $a++){
													if($keys[$i] === $onOrderBy[$a]["base_expr"]){
														$OrderByClassName = "orderByColumn";
													}
												}
											}
											$GroupByClassName = "";
											if(!empty($onGroupBy)){
												for($a = 0; $a < count($onGroupBy); $a++){
													if($keys[$i] === $onGroupBy[$a]["base_expr"]){
														$GroupByClassName = "orderByColumn";
													}
												}
											}
											echo '<th class="'. $onClassName . ' '. $keys[$k] . '" id="' . $finfo->orgtable . '"><p>' . $keys[$k] . '</p></th>';
										}
										unset($keys);
										?>
									</tr>
										<?php
										for($m=0; $m < count($queryResultJoin); $m++){
											echo "<tr id='data'>";
											for($n=0; $n<count($queryResultJoin[$m])/2; $n++) {
												if($queryResultJoin[$m][$n] == ""){
													$queryResultJoin[$m][$n] = "NULL";
												}
												echo '<td class=" original-data original-data-'.$n.'"><span id="span_'.$n.'" class="span_'.$n.' extraSpan">' . utf8_encode($queryResultJoin[$m][$n]) . '</span><span id="original-span" class="used original-span_'.$n.'">' . utf8_encode($queryResultJoin[$m][$n]) . '</span><span class="textOrigin">'.utf8_encode($queryResultJoin[$m][$n]).'</span></td>';
											}
											echo "</tr>";
										} 
										mysqli_free_result($tableResult);?>
								</tbody>
							</table> <?php		
						}
				}
				else {
					// Show all previous tables in one step
					$numberOfTables = count($tableName[0]);
					if($isMultipleJoin){
						$numberOfTables = 2;
					}
					for($i = 0; $i < $step; $i++){
					if($i < $numberOfTables){
						$query = "SELECT * FROM " . $tableName[0][$i];
						$con=mysqli_connect(DB_SERVER,DECOMPOSE_USER,DECOMPOSE_PASSWORD,DECOMPOSE_DATABASE);
						$tableResult = mysqli_query($con, $query);
						
						for($j = 0; $j < $tableResult->num_rows; $j++){
							$finalTbResult[] = mysqli_fetch_array($tableResult);
						}
						$thisTableName = $tableName[0][$i];
						mysqli_close($con);
						$keyQuery = "SHOW INDEX FROM " . $tableName[0][$i];
						$con=mysqli_connect(DB_SERVER,DECOMPOSE_USER,DECOMPOSE_PASSWORD,DECOMPOSE_DATABASE);
						$keyResult = mysqli_query($con, $keyQuery);
				
						for($j = 0; $j < $keyResult->num_rows; $j++){
							$finalKeyResult[] = mysqli_fetch_array($keyResult);
						}
						mysqli_close($con);
				echo "<b>" . $thisTableName . "</b>"; ?>
				
				<table class='table table-bordered original-table <?php echo $thisTableName; ?> <?echo $crossClassName;?> <?php echo $tablename; ?>' id='<?php echo $thisTableName; ?>'>
					<tbody>
					<tr> 
			
						<?php
							$keys = array_keys($finalTbResult[0]);
							$con=mysqli_connect(DB_SERVER,DECOMPOSE_USER,DECOMPOSE_PASSWORD,DECOMPOSE_DATABASE);
							$tempResult = mysqli_query($con, $query);
							mysqli_close($con);
							// Table headings:
							if($show_headings){
								for($k=1, $c=0; $k < count($keys); $k+= 2, $c++) {
									$finfo = mysqli_fetch_field_direct($tempResult, $c);
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
										if($keys[$k] == $whereColumns[$r][0]){
											$whereClassName = "where";
										}
									}
									$onClassName = "";
									if(!empty($onColumns[0][$thisStep]['ref_clause'])){
										if(!empty($onColumns[0][$thisStep]['ref_clause'][0]['sub_tree'])){
											for($r = 0; $r < count($onColumns[0][$thisStep]['ref_clause'][0]['sub_tree']); $r++){
												if (($pos = strpos($onColumns[0][$thisStep]['ref_clause'][0]['sub_tree'][$r]['base_expr'], ".")) !== FALSE) { 
										    		$thisOnColumn = substr($onColumns[0][$thisStep]['ref_clause'][0]['sub_tree'][$r]['base_expr'], $pos+1); 
													if($keys[$k] === $thisOnColumn){
														$onClassName = "onColumn";
													}
												}
											}
										}
										else {
											for($r = 0; $r < count($onColumns[0][$thisStep]['ref_clause']); $r++){
												if (($pos = strpos($onColumns[0][$thisStep]['ref_clause'][$r]['base_expr'], ".")) !== FALSE) { 
										    		$thisOnColumn = substr($onColumns[0][$thisStep]['ref_clause'][$r]['base_expr'], $pos+1); 
													if($keys[$k] === $thisOnColumn){
														$onClassName = "onColumn";
													}
												}
												else if(!empty($onColumns[0][$thisStep]['ref_clause'][$r]['base_expr'])) {
													$thisOnColumn = $onColumns[0][$thisStep]['ref_clause'][$r]['base_expr'];
													if($keys[$k] === $thisOnColumn){
														$onClassName = "onColumn";
													}
												}
											}
										}
									}
									$OrderByClassName = "";
									if(!empty($onOrderBy)){
										for($a = 0; $a < count($onOrderBy); $a++){
											if($keys[$k] === $onOrderBy[$a]["base_expr"]){
												$OrderByClassName = "orderByColumn";
											}
										}
									}
									$GroupByClassName = "";
									if(!empty($onGroupBy)){
										for($a = 0; $a < count($onGroupBy); $a++){
											if($keys[$k] === $onGroupBy[$a]["base_expr"]){
												$GroupByClassName = "orderByColumn";
											}
										}
									}
									
									if($classNameKey != ""){
										echo '<th class="'. $classNameKey . ' ' . $GroupByClassName . ' ' . $OrderByClassName . ' ' . $onClassName . ' ' . $whereClassName . ' '. $keys[$k] . '" id="' . $finfo->orgtable . '"><p>' . $keys[$k] . '</p></th>';
									}
									else {
										echo '<th class="'. $whereClassName . ' ' . $GroupByClassName . ' ' . $OrderByClassName . ' ' . $onClassName . ' ' .  $keys[$k] . '" id="' . $finfo->orgtable . '"><p>' . $keys[$k] . '</p></th>';
									}
								} 
							}  
							unset($keys);
							mysqli_free_result($tempResult);
							?>
					</tr>
					<?php
						
						// Table data:
						for($m=0; $m < count($finalTbResult); $m++){ ?>
							
							<tr id="data">	<?php
									for($n=0; $n<count($finalTbResult[$m])/2; $n++) {
										if($finalTbResult[$m][$n] == ""){
											$finalTbResult[$m][$n] = "NULL";
										}
										echo '<td class=" original-data original-data-'.$n.'"><span id="span_'.$n.'" class="span_'.$n.' extraSpan">' . utf8_encode($finalTbResult[$m][$n]) . '</span><span id="original-span" class="used original-span_'.$n.'">' . utf8_encode($finalTbResult[$m][$n]) . '</span><span class="textOrigin">'.utf8_encode($finalTbResult[$m][$n]).'</span></td>';
									}?>
							</tr>
							<?php
						} ?>
				</tbody>
				</table>
					<?php
						}
						unset($finalTbResult);
						unset($tableResult);
						unset($keyResult);
					}
				}
			}
		
		if($empty){
			if(empty($result)){
				echo "<div class='alert alert-warning-decomposer' role='alert'>This query returned no results!</div>";
			}
			else { ?>
		<b>Result table</b><table class='table table-bordered empty-table' id='empty-table'>
			<tbody>
			<tr> 
			
				<?php
					$keys = array_keys($result[0]);
					$var = (string)$previousTable[$step][1]['contents'];
					$removeWords = array("returned", "the", "following", "table:", "<b>", "</b>", "<i>", "</i>");
					$tempString = str_replace($removeWords, "", $var);
					//$tempQuery = strstr($tempString, 'FROM');
					$tempQuery = $tempString . " LIMIT 1";
					mysqli_report(MYSQLI_REPORT_ERROR);
					$con=mysqli_connect(DB_SERVER,DECOMPOSE_USER,DECOMPOSE_PASSWORD,DECOMPOSE_DATABASE);
					$tempResult = mysqli_query($con, $tempQuery);
					$numericResult = mysqli_query($con, $tempString);
					mysqli_close($con);
					$numericArrayResult = mysqli_fetch_row($numericResult);
					
					if(count($result[0]) / 2 != count($numericArrayResult)){
						// Table headings:
						if($show_headings){
							for($i=1, $c=0; $i <= count($numericArrayResult); $i++, $c++) {
								$finfo = mysqli_fetch_field_direct($tempResult, $c);
								echo '<th class="' . $finfo->name . '" id="' . $finfo->orgtable . '"><p>' . $finfo->name . '</p></th>';	
							} 
						}
						unset($keys);
						//unset($finfo);
						mysqli_free_result($tempResult);
						//mysqli_close($con);
						?>
				</tr>
						<?php
						// Table data:
						for($i=0; $i < count($result); $i++){ 
							$thisRow = $i+1; 
							echo '<tr class="data" id="row_'.$thisRow.'">';
							if($i > 0){
								$numericArrayResult = mysqli_fetch_row($numericResult);
							}
							for($j=0; $j<count($numericArrayResult); $j++) {
								if($numericArrayResult[$j] == ""){
									$numericArrayResult[$j] = "NULL";
								}
								if($j == 0){
									echo '<td><span class="textOrigin"  >' . utf8_encode($numericArrayResult[$j]) . '</span><span class="span_'.$j.'"  >' . utf8_encode($numericArrayResult[$j]) . '</span> <span class="glyphicon glyphicon-arrow-right" style="display: none;"></span></td>';
								}
								else {
									echo '<td><span class="textOrigin"  >' . utf8_encode($numericArrayResult[$j]) . '</span><span class="span_'.$j.'"  >' . utf8_encode($numericArrayResult[$j]) . '</span></td>';
								}
							} ?>
							</tr>
							<?php
						}
					}
					else {
					
					// Table headings:
					if($show_headings){
						for($i=1, $c=0; $i <= count($keys); $i+= 2, $c++) {
							if($i == count($keys) && !isset($keys[$i])){
								$finfo = mysqli_fetch_field_direct($tempResult, $c);
								echo '<th class="' . $finfo->name . '" id="' . $finfo->orgtable . '"><p>' . $finfo->name . '</p></th>';	
								//printf($finfo->name);
							}
							else if(is_int ($keys[$i])){
								$finfo = mysqli_fetch_field_direct($tempResult, $keys[$i]-1);
								echo '<th class="' . $finfo->name . '" id="' . $finfo->orgtable . '"><p>' . $finfo->name . '</p></th>';	
							}
							else {
								$finfo = mysqli_fetch_field_direct($tempResult, $c);
								echo '<th class="' . $keys[$i] . '" id="' . $finfo->orgtable . '"><p>' . $keys[$i] . '</p></th>';	
							}
						} 
					}
					unset($keys);
					//unset($finfo);
					mysqli_free_result($tempResult);
					//mysqli_close($con);
					?>
			</tr>
				<?php
					// Table data:
					for($i=0; $i < count($result); $i++){ 
						$thisRow = $i+1; 
						echo '<tr class="data" id="row_'.$thisRow.'">';
											
							for($j=0; $j<count($result[$i])/2; $j++) {
								if($result[$i][$j] == ""){
									$result[$i][$j] = "NULL";
								}
								if($j == 0){
									echo '<td><span class="textOrigin"  >' . utf8_encode($result[$i][$j]) . '</span><span class="span_'.$j.'"  >' . utf8_encode($result[$i][$j]) . '</span> <span class="glyphicon glyphicon-arrow-right" style="display: none;"></span></td>';
								}
								else {
									echo '<td><span class="textOrigin"  >' . utf8_encode($result[$i][$j]) . '</span><span class="span_'.$j.'"  >' . utf8_encode($result[$i][$j]) . '</span></td>';
								}
								
							}?>
						</tr>
						<?php
					} 
				} ?>
			</tbody>
			</table>
		<?php }	
		}
	}
	}
}

?>