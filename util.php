<?php
header("Content-Type: text/plain");
function fixSitelinkDate($str, $format = 'm/d/Y'){
	$pos = strpos($str,"T");
	$str = substr($str, 0, $pos);
	
	$date = strtotime($str);
	
	return date($format, $date);
}

function strTrim($str, $length = 2, $direction = 'r'){
	$len = strlen($str);
	if($direction === 'r'){
		return substr($str, 0, $len-$length);
	}
	else{
		return substr($str, $length);
	}
}

function buildMyhubUrl($corpCode = 'CAVJ', $locCode, $tenantId, $ledgerId){
	return "https://myhub.smdservers.net/".$corpCode."/".$locCode."/Operations/Payment/MakePayment?TenantId=".$tenantId."&amp;LedgerId=".$ledgerId;
}

function prettyPrintPhoneNumber($str){
	$str = preg_replace("/[^0-9]/", "", $str);
	$arr = str_split($str);
	
	$arr = insertStrMidArray($arr, '-', 4);
	$arr = insertStrMidArray($arr, '-', 8); //pos 8 not 7 because we just padded the array already
	
	if(count($arr) > 12){ //handle case where number has a 1 in front of it
		$arr = insertStrMidArray($arr, ' ', 12);
	}
	
	$str = implode($arr);
	return $str;
}

function insertStrMidArray($array, $valToInsert, $insertionPoint){
	array_push($array, NULL);
	$array = array_reverse($array);
		
	foreach($array as $key => $val){
		if($key < $insertionPoint){
			$array[$key] = $array[$key+1];
		}
	}
	
	$array[$insertionPoint] = $valToInsert;
	$array = array_reverse($array);
	return $array;
}
?>