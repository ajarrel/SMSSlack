<?php
/* channel ID list
#charlottecustomers - C5ZG488BD
#cusomterupdate - C6894E9AP
#raleighcustomers - C5ZM7UXBP
*/
error_reporting(E_ERROR);
ignore_user_abort(true);

header("HTTP/1.0 200 OK");
header("Content-Type: plain/text");
header("Content-Length: ".strlen("OK"));
echo "OK";
flush();
ob_flush();

//SAMPLE UPDATE OBJECT TO SENT TO SLACK

//END SAMPLE UPDATE OBJECT


$rawInput = file_get_contents('php://input');

$json = json_decode($rawInput);


	//echo $rawInput;

if($json->token === "sRsflMIES2eveufxb8jFYa9v"){

	
	if($json->event->type === "message" && $json->event->subtype = "bot_message" &&
	   ($json->event->channel === "C5ZG488BD" || 
		$json->event->channel === "C6894E9AP" || 
		$json->event->channel === "C5ZM7UXBP"  ||
	   	$json->event->channel === "C86SZ65N1")){
		
		$fp = fopen("capturelog.txt", "a");
		fwrite($fp, "\r\n".$rawInput);
		
		//fwrite($fp, 'bot message in correct channel '.$json->event->text.'\r\n');
		
		if( stripos($json->event->text, "ent message to ")){
			include('jsontest.php');

			$phoneNum = array(); $msg = array();
			preg_match("/(?<=\+1)[0-9]{10}/",$json->event->text, $phoneNum);
			preg_match("/(?<=\+1[0-9]{10}\:\s).+$/",$json->event->text,$msg);
			
			$note = "SENT BURNER MSG: ".$msg[0]; //put note in single Variable

			$customers = getAllCustomers($phoneNum[0]);
			if($customers){
				fwrite($fp, "\r\nCustomers found, requesting ledgers");
				$ledgers = getLedgersByCustomer($customers);
				if($ledgers){
					fwrite($fp, "\r\nLedgers found, writing the following to ledgers");
					writeToLedgers($ledgers, $note, $phoneNum[0]);
					fwrite($fp, "\r\nWrote ".$note." to ledgers");
				}
				else{
					fwrite($fp, "\r\nNoLedgers IDENT, but customers were found");
				}
			}
			else{
				fwrite($fp, "\r\nNo Customers found, bye bye");
			}
			
			$customerCount = count($customers);
			$ledgerCount = count($ledgers);
			
			fwrite($fp, "\r\nCustomer count: ".$customerCount." Ledger Count: ".$ledgerCount."\r\n"."SENT BURNER MSG: ".$msg[0]	);

		}
		
	}
	//fclose($fp);
}
else{
	header("HTTP/1.0 404 Not Found");
	die();
}

?>