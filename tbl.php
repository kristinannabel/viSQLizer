<?php

class tbl{
		
	function make_table($result, $show_headings, $tablename="") {
		
		?>

		<table class="table table-striped <?php echo $tablename; ?>">
			<tr> 
			
				<?php
					$keys = array_keys($result[0]);
				
					// Table headings:
					if($show_headings){
						for($i=1; $i < count($keys); $i+= 2) {
							echo "<th><p text-transform: capitalize>" . $keys[$i] . "</p></th>";	
						} 
				}?>
			</tr>
			<?php
				// Table data:
				for($i=0; $i < count($result); $i++){ ?>
					<tr>
						<?php 				
							for($j=0; $j<count($result[$i])/2; $j++) {
								echo "<td>" . $result[$i][$j] . "</td>";
								if($tablename == "savedQueries"){
									echo "<td style='width: 70px;'><button style='margin-right: 5px;' type='button' class='btn btn-default btn-xs edit_button'><span class='glyphicon glyphicon-play'></span></button><button type='button' class='btn btn-default btn-xs delete_button'><span class='glyphicon glyphicon-remove'></span></button></td>";
									
								}
								else if($tablename == "databaseTables") {
									echo "<td><span class='glyphicon glyphicon-chevron-down pull-right'></span></td>";
								}
						}?>
					</tr>
					<?php
				} ?>
		</table>
		<?php

	}

}

?>