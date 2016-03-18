<?php
	require_once dirname(__FILE__) . '/parser/PHPSQLParser.php';
	require_once dirname(__FILE__) . '/parser/PHPSQLCreator.php';
	include('array_functions.php');
	//include('config.php');
	
	class Parser {
		private $db_connection;
		private $parser;
		private $creator;
		private $mode;
		private $singleStepTable;
		private $currentStep;
		private $totalSteps;
		private $tableNames;
		private $whereColumns;
		private $onColumns;
		private $isJOIN;
		private $onOrderBy;
		private $onGroupBy;
		private $isMultipleJoin;


		public function __construct($sql, $database_connection) {
			$this->db_connection=$database_connection;
			//$this->parser = new PHPSQLParser($sql, true);
			//$this->creator = new PHPSQLCreator();
			$this->parser = new PHPSQLParser\PHPSQLParser($sql, true);
			$this->creator = new PHPSQLParser\PHPSQLCreator();
			$this->singleStepTable=array();
			$this->currentStep=1;
			$this->mode='single';
			$this->totalSteps=0;
			$this->tableNames = array();
			$this->whereColumns = array();
			$this->onColumns = array();
			$this->isJOIN = false;
			$this->onOrderBy = array();
			$this->onGroupBy = array();
			$this->isMultipleJoin = false;
		}
		
		public function parse_sql_query($step=0) {
			$tableCount = count($this->parser->parsed['FROM']);
			for($i = 0; $i < $tableCount; $i++){
				$this->tableNames[$i] = $this->parser->parsed['FROM'][$i]['table'];
			}			
			// Set the first select to select all collumns. The projection step should be the last step.
			$select['SELECT']=array("0"=>array("expr_type"=>"colref", "alias"=>"", "base_expr"=>"*", "no_quotes"=>"", "sub_tree"=>"", "delim"=>"", "position"=>"7"));
						
			/* If the query contains a JOIN, 
			 * the different tables that are joined should be displayed separately first,
			 * and then combined afterwards 
			 */
			if(count($this->parser->parsed['FROM']) > 1) 
			{	
				for ($i = 0; $i < count($this->parser->parsed['FROM']); $i++)
				{
					$source_table = $select;
					$temp['0'] = $this->parser->parsed['FROM'][$i];
					$temp['0']['join_type']  = '';
					$temp['0']['ref_type']   = '';
					$temp['0']['ref_clause'] = '';
					$temp['0']['base_expr'] = $temp['0']['table'];
					
					$source_table = $this->buildSubQuery('FROM', $temp, $source_table);
				}
				$this->onColumns[] = $this->parser->parsed['FROM'];
			}
			// If the query contains multiple JOINS
			if(count($this->parser->parsed['FROM']) > 2) 
			{
				$this->isMultipleJoin = true;
				for ($i = 0; $i < count($this->parser->parsed['FROM']); $i++)
				{
					if(($i+1) == 2)
					{ 
						// Add first two tables from JOIN
						$count = $i-1;
						$joined_table = $select;
						$temp['0'] = $this->parser->parsed['FROM'][$count];
						$temp['1'] = $this->parser->parsed['FROM'][$i];
						$joined_table = $this->buildSubQuery('FROM', $temp, $joined_table);
					}
					else if(($i+1) > 2)
					{
						if($i+1 == count($this->parser->parsed['FROM'])){
							// Add the full FROM-part
							$select=$this->buildSubQuery('FROM',$this->parser->parsed['FROM'],$select);
						}
						else {
							// Add this JOIN table plus the previous ones
							$joined_table = $select;
							for($a = 0; $a <= $i; $a++){
								$temp[$a] = $this->parser->parsed['FROM'][$a];
							}
							$joined_table = $this->buildSubQuery('FROM', $temp, $joined_table);
						}
					}
				}
			}
			else {
				// Add the full FROM-part
				$select=$this->buildSubQuery('FROM',$this->parser->parsed['FROM'],$select);
			}
			
			if(isset($this->parser->parsed['WHERE']))
			{
				$select=$this->buildSubQuery('WHERE',$this->parser->parsed['WHERE'],$select);
				for($k = 0; $k < count($select["WHERE"]); $k++)
				{
					if(array_key_exists( 'no_quotes', $select["WHERE"][$k]))
					{
						$this->whereColumns[] = $select["WHERE"][$k]["no_quotes"]["parts"];//[0];
					}
				}
			}
		
			// Do the same for the ORDER clause
			if(isset($this->parser->parsed['ORDER'])){
				$this->onOrderBy = $this->parser->parsed['ORDER'];
				$select=$this->buildSubQuery('ORDER',$this->parser->parsed['ORDER'],$select);
			}
			// Do the same for the SELECT clause, now making the projection. (remowing the SELECT * FROM-part that was
			// generated in the first step.)
			if (isset($this->parser->parsed['SELECT']) && $this->parser->parsed['SELECT'] 
			&& $this->parser->parsed['SELECT'][0]['base_expr'] != '*') {
				$select['SELECT']=$this->parser->parsed['SELECT'];
				$this->decomposeSelect($select);
			}
			if (isset($this->parser->parsed['GROUP'])) {
				$this->onGroupBy = $this->parser->parsed['GROUP'];
				$this->decomposeGroupBy($select);
				$select=$this->buildSubQuery('GROUP',$this->parser->parsed['GROUP'],$select);
			}
			if (isset($this->parser->parsed['HAVING'])) {
				$select=$this->buildSubQuery('HAVING',$this->parser->parsed['HAVING'],$select);
			}
			$this->totalSteps=$this->currentStep-1;
		}
		/*
		 * This method splits the contents of a select-array into projection and aggregate-parts
		 * @param $parsedQuery : A query returned from the parser
		 */
		private function decomposeGroupBy($parsedQuery) {
			$temp=array();
			foreach($this->parser->parsed['GROUP'] as $group) {
				$temp[]=$group;
				$this->buildSubQuery('ORDER',$temp,$parsedQuery);
			}
		}
		private function decomposeSelect($parsedQuery) {
			$projections=array();
			$aggregations=array();
			$aggprojections=array();
			//Splits the projections from the aggregates and makes a array of the scores for aggregatefunction	
			foreach($parsedQuery['SELECT'] as $parsed) {
				if ($parsed['expr_type']!="aggregate_function") {
					$projections[]=$parsed;
				}
				else if ($parsed['expr_type']=="aggregate_function") {
					$aggregations[]=$parsed;
					$aggprojections[]=$this->makeAggProjection($parsed);
				}
				// Remove the AS from the query:
				$parsed['alias'] = "";
			}
			//displays the projections with projection of aggregate scores if present
			if(count($projections)>0) {
				$temp=$projections;
				if (count($aggprojections)>0) {
					$temp[]=$aggprojections[0];
				}
				$this->buildSubQuery('SELECT',$temp,$parsedQuery);
			}
			//Combines the projections with the aggregatfunctions to turn them into tables
			foreach ($aggregations as $aggregation) {
				$aggregation['alias'] = "";
				$temp=$projections;
				$temp[]=$aggregation;
				$this->explainFunction($aggregation,$parsedQuery['FROM']);
				$this->buildSubQuery('SELECT',$temp,$parsedQuery);
			}
		}
		/*
		 * This method turns the scores of an aggregatfunction into a projection
		 * @param $aggregation : Array of an aggregatefunction
		 */
		private function makeAggProjection($aggregation) {
			$temp=array();
			foreach ($aggregation['sub_tree'] as $key=>$value) {
				$temp[$key]=$value;
			}
			$projection=$temp[0];
			$projection['position']=$aggregation['position'];
			$projection['delim']=',';
			$projection['alias']=false;
			$projection['sub_tree']=false;
			return $projection;
		}
		/*
		 * This method builds a subquery after the array with the select-statement has been manipulated 
		 * @param $newSelect : Array of the manipulated select-array
		 * @param $parsedQuery : Rest of the query
		 */
		private function buildSubQuery($type,$expression,$parsedQuery) {
			$parsedQuery[$type]=$expression;
			$parsedQuery[$type][count($expression)-1]['delim']='';
			$this->addSubQuery($parsedQuery);
			return $parsedQuery;
		}
		private function addSubQuery($select) {
			$this->creator->create($select);
			$result = mysqli_query($this->db_connection, $this->creator->created);
			if(!$result){
				echo "<br><div class='alert alert-danger' role='alert'>".mysqli_error($this->db_connection)."</div>";
			}
			else {
			// Show the created SQL-query
			$this->makeQuery($this->creator->created . " <i>returned the following table:</i>"); //Shows the created SQL-query
			$result_array_select=array();
			// Grab the results into an array
			for($i = 0; $i < $result->num_rows; $i++){
				$result_array_select[] = mysqli_fetch_array($result);
			}
			// Send the array to a table-creator, which prints the array as a table.
			$this->makeTable($result_array_select,true);
			$this->currentStep++;
			}
		}
		private function explainFunction($aggr,$from) {
			$name=$aggr['base_expr'];
			$col=$aggr['sub_tree'][0]['base_expr'];
			$table=$from[0]['table'];
			switch ($name) {
				case 'SUM':
					$this->makeText("SUM: Sums all ".$col."s from ".$table);
					break;
				case 'AVG':
					$this->makeText("AVG: Sums all ".$col."s from ".$table." and divide by the total number of ".$col.'s');
					break;
				case 'MIN':
					$this->makeText("MIN: Finds the lowest instance of ".$col." from ".$table);
					break;
				case 'MAX':
					$this->makeText("MAX: Finds the highest instance of ".$col." from ".$table);
					break;
				case 'COUNT':
					$this->makeText("COUNT: Finds the total number of ".$col."s from ".$table);
					break;
			}
		}
		public function setMode($mode) {
			$this->mode=$mode;
		}
		public function getTotalSteps() {
			return $this->totalSteps;
		
		}
		public function getListOfTables() {
			return $this->tableNames;
		}
		//Displays text or tables during stream mode or put them into an array for step-mode
		private function makeTable($result,$boole) {
			$this->addTableEntry('table',$result);
		}
		private function makeText($text) {
			$this->addTableEntry('text',$text);
		}
		private function makeQuery($text) {
			$this->addTableEntry('query',$text);
		}
		/*
		 * This method checks if an element in array1 is also present in array2
		 * if the element is present, the part of the array2 upto the match is removed
		 * otherwise the element in array1 that isn't matched is highlightes as bold.
		 * @param $textArea1 : The textarray that will be highlighted
		 * @param $textArea2 : The textarray that will be used to look for differences
		 */
		private function highlightText($textArray1,$textArray2) {
			$keys=array();
			$textArray1=cleanArray($textArray1);
			//$textArray2=$textArray1;
			if (!$textArray2==null) {
				$keys=sortTextArrayKeys($textArray1,$textArray2);
				for($i=0;$i<count($textArray1);$i++) {
					if ($keys[$i]===false) {
						$textArray1[$i]='<b>'.$textArray1[$i].' </b>';
					}
				}
			}
			return implode($textArray1,' ');
		}
		/*
		 * This method runs through all the queries in the singleStepTable-array, and sends the
		 * query that will be highlighted and the query that it will be compared to, to the
		 * highlightText-function. The singleStepTable will then be updated with highlighted text.
		 * Before the text are sent, they are turned into arrays, for easier comparison.
		 */
		private function compareQueries() {
			$tableName[] = $this->getListOfTables();
			$prev=array('<i>returned', 'the', 'following', 'table:</i>');
			$i=1;
			foreach($this->singleStepTable as $step) {
				$j=1;
				foreach($step as $output) {
					if ($output['type']=='query') {
						$next=explode(' ',$output['contents']);
						$this->singleStepTable[$i][$j]['contents']=$this->highlightText($next,$prev);
						if($i >= (count($tableName[0])+1)){
							$prev=$next;
						}
					}
					$j++;
				}
				$i++;
			}
		}
		private function addTableEntry($type,$contents) {
			$i=@count($this->singleStepTable[$this->currentStep]);
			$this->singleStepTable[$this->currentStep][$i+1]['type']=$type;
			$this->singleStepTable[$this->currentStep][$i+1]['contents']=$contents;
		}
		public function displayResult($step=0, $sql) {
			$this->compareQueries();
			$this->showStep2($step, $sql);
		}
		//Takes the chosen step after the parser has run and displays the text and tables for the step
		public function showStep($step) {
			foreach($this->singleStepTable[$step] as $output) {
				if (($output['type']=='text') or ($output['type']=='query')){
					echo "<br><div class='alert alert-info-decomposer' role='alert'>".$output['contents']."</div>";
				}
				else if ($output['type']=='table') {
					$TBL = new tbl();
					$TBL->make_table($output['contents'], true);
				}
			}
		}
		// Displays the text and tabled for the step inside a wizard
		public function showStep2($step, $sql) {
			
			$tableName[] = $this->getListOfTables();
			$numOfSteps = $this->getTotalSteps();
			$thisStep = $step;
			if($numOfSteps > 1){
				$thisNumStep = $numOfSteps - count($tableName[0]);
			}
			else {
				$thisNumStep = $numOfSteps;
			}
			
			if($step <= count($tableName[0]) && $numOfSteps > 1){
				$step = count($tableName[0])+1;
				
				$thisStep = 1;
			}
			else if($step > count($tableName[0])){
				$thisStep = $step - count($tableName[0]);
			}
			$query = "SELECT * FROM " . $tableName[0][0];
			$con=mysqli_connect(DB_SERVER,DECOMPOSE_USER,DECOMPOSE_PASSWORD,DECOMPOSE_DATABASE);
			$tableResult = mysqli_query($con, $query);
			for($i = 0; $i < $tableResult->num_rows; $i++){
				$finalTbResult[] = mysqli_fetch_array($tableResult);
			}
			
			if(!empty($this->parser->parsed['SELECT'][0]['alias'])){
				echo "<div class='alert alert-danger' role='alert'>";
				echo "Alias is not supported by this version of the viSQLizer prototype!";
				echo "</div>";
			}
			if($this->parser->parsed['SELECT'][0]['expr_type']=="aggregate_function"){
				echo "<div class='alert alert-danger' role='alert'>";
				echo "Aggregate functions is not supported by this version of the viSQLizer prototype!";
				echo "</div>";
			}
			if($this->parser->parsed['FROM'][0]['alias']['name'] == "NATURAL"){
				echo "<div class='alert alert-danger' role='alert'>";
				echo "NATURAL JOIN is not supported by this version of the viSQLizer prototype!";
				echo "</div>";
			}
			if((empty($this->parser->parsed['SELECT'][0]['alias'])) && ($this->parser->parsed['SELECT'][0]['expr_type']!="aggregate_function") && ($this->parser->parsed['FROM'][0]['alias']['name']!="NATURAL")) {
				echo "<div class='panel panel-default streammode-panel' id='main-panel streammode-panel'><div class='panel-heading'><h3 class='panel-title'> Step " . $thisStep . " of " . $thisNumStep . "</h3></div>";
				//print_r($this->singleStepTable[3]);
				echo "<div class='panel-body'>";
				
				foreach($this->singleStepTable[$step] as $output) {
					if (($output['type']=='text') or ($output['type']=='query')){
						echo "<div class='alert alert-info-decomposer' role='alert'>".$output['contents']."</div>";
					}
					else if ($output['type']=='table') {
						$TBL = new tbl();
						$TBL->make_table($output['contents'], true, "dbtable", true, $step, $tableName, $this->whereColumns, $this->onColumns, $this->onOrderBy, $this->onGroupBy, $this->singleStepTable, $sql, $this->parser->parsed, $this->isMultipleJoin, $thisStep);
					}
				}
				echo "</div>";
				if($thisNumStep > 1){
					echo "<div class='panel-footer wizard-footer' style='text-align: center;'><ul class='pagination' style='margin: auto;'><li class='previous'><a href='#' onclick='stepClicked()'>Previous</a></li>";
					$stepCounter = 1;
					for($i = 1; $i <= $numOfSteps; $i++){
						if($i > count($tableName[0])){
							echo "<li class='step step" . $stepCounter . "'><a id='astep".$stepCounter."' href='#' onclick='stepClicked()' class='" . $i . "'>" . $stepCounter . "</a></li>";
							$stepCounter++;
						}
					}
					echo "<li class='next'><a href='#' onclick='stepClicked()'>Next</a></li></ul></div></div>";
				}
				else {
					echo "</div>";
				}

			}
			
		}
		
	} 
?>