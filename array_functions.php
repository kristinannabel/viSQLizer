<?php
	function cleanArray($oldArray,$removeComma=false) {
		$newArray=array();
		foreach ($oldArray as $key=>$element) {
			if (!$element=='') {
				if($removeComma) {
					$newArray[]=trim($element,',');
				}
				else {
					$newArray[]=$element;
				}
			}
		}
		return $newArray;
	}
	
	
	
	function getTrueValue($valueArray,$legalValues) {
		foreach($valueArray as $value) {
			if ($value==$legalValues[0]) {
				return $legalValues[0];
			}
		}
		//var_dump($value,$legalValues[0]);
		return false;
	 }
	 function makeArrayOfValues($oldArray) {
		$newArray=array();
		foreach ($oldArray as $value=>$element) {
			foreach ($element as $subElement) {
				if ((count($element)>1) and (count($oldArray[$subElement])>1)) {
					$newArray[]=$subElement;
				}
				else if (count($element)==1){
					$newArray[]=$subElement;
				}
				else {
					//var_dump($subElement);
					$newArray[]=$subElement;
				}
			}
		}
		sort($newArray);
		return array_unique($newArray);
	 }
	function sortTextArrayKeys($textArray1,$textArray2) {
		$keys=array();
		$textArray2=cleanArray($textArray2,true);
		
		for($i=0;$i<count($textArray1);$i++) {
			if (!$textArray1[$i]=='') {
				$keys[]=array_keys($textArray2,trim($textArray1[$i],','));
			}
			
		}
		$tempArray=makeArrayOfValues($keys);
		//var_dump($tempArray,$keys,$textArray1);
		foreach ($keys as $value=>$key) {
			if ($key==null) {
				$keys[$value]=false;
			}
			else { 
				$temp=getTrueValue($key,$tempArray);
				//var_dump($temp);
				if (($temp===0) or (!$temp===false)) {
				//var_dump($temp);
					$keys[$value]=$temp;
					$tempArray=array_slice($tempArray,1);
						
				}
				else {
					$keys[$value]=false;
				}
					
			}
		}
		
		//var_dump($keys);
		return $keys;
	}
?>