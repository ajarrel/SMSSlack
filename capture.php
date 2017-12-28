<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

include('jsontest.php');

$newline = "\r\n";

$fp = fopen('capturelog.txt', 'a');
if (false === $fp) {
    throw new RuntimeException('Unable to open log file for writing');
}

$fileContents = file_get_contents('php://input');

/*$str = $_SERVER['REMOTE_ADDR'] . "\r\n" . $fileContents . "\r\n";
fwrite($fp, $str);

fclose($fp);*/

fwrite($fp, $_SERVER["REMOTE_ADDR"]);

$json = json_decode($fileContents);

fwrite($fp, print_r($json).$newline);

if($json->type === 'inboundText'){
	fwrite($fp, 'inboundText'.$newline);
	fwrite($fp, substr($json->fromNumber, 2)." -- ");
	fwrite($fp, $json->payload.$newline);
	
	$customers = getAllCustomers(substr($json->fromNumber, 2)); //returns all customers with this phone
	$ledgers = getLedgersByCustomer($customers); //gets all ledgers from all customers with this phone
	writeToLedgers($ledgers, "INBOUND BURNER MSG: ".$json->payload, substr($json->fromNumber, 2)); //writes the text to all ledgers with this phone
	
}
else{
	fwrite($fp, 'unknown type received'.$newline.$newline);
}

?>