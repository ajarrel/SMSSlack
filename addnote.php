<?php

if($_REQUEST["token"] = "sRsflMIES2eveufxb8jFYa9v"){
	
	include('jsontest.php');
	include('io.php');
	
	$text = $_REQUEST['text'];
	$respUrl = $_REQUEST["response_url"];
	$respType = ($_REQUEST["response_url"]) ? 'curl' : 'stdout';
	$user = ($_REQUEST['user_name']) ? $_REQUEST['user_name'] : 'debug';
	
	$response = filterSlackInput($text);
	
	
	if($response){
		if($response["type"] === 'loc'){
			$customers = getCustomer('', $response["ident"]["loc"], $response["ident"]["unit"], '');
		}
		elseif($response["type"] === 'email'){
			$customers = getAllCustomers('', '', $response["ident"]);
		}
		elseif($response["type"] === 'phone'){
			$customers = getAllCustomers($response["ident"], '', '');
		}
		else{
			pushOutput($respUrl, '{"text": "I did not understand the type of user you were trying to look up. Please try again using one of the following formats\n/addnote L006 H177 Left VM for customer about balance\n/addnote adam@americanstoragenc.com E-mailed customer lease agreement\n/addnote 9196737452 Spoke to customer about picking up keys to new lock from kiosk"}', $respType, true);
		}
		
		header("Content-Type: application/json"); //this is the special case where giving quick feedback to slack for the correct /addnote usage. Pushs output to client (instead of via cURL), and doesn't die afterwards
		$output = '{"text": "Hang tight while I add this note for you..."}';
		header("Content-Length: ".strlen($output));
		echo $output;
	}
	else{
		pushOutput($respUrl, '{"text": "I did not recognize that input. To add a note, use one of the following formats\n/addnote L006 H177 Left VM for customer about balance\n/addnote adam@americanstoragenc.com E-mailed customer lease agreement\n/addnote 9196737452 Spoke to customer about picking up keys to new lock from kiosk"}', $respType, true);
	}
	
	if($customers){
		$ledgers = getLedgersByCustomer($customers);
		if($ledgers){
			writeToLedgers($ledgers, $user.": ".$response["note"], false);
			$cType = ($ledgers[0]["type"] === 'cust') ? "Customer" : "Reservation";
			$cType = $cType.": ".$ledgers[0]["firstName"]." ".$ledgers[0]["lastName"]." - Unit: ".$ledgers[0]["unit"]." ".$ledgers[0]["lCode"];
			pushOutput($respUrl, '{"text": "*'.$cType.'*\n_'.$user.' Posted the following note: '.$response["note"].'_", "response_type": "in_channel"}');
		}
		else{
			pushOutput($respUrl, '{"text": "I found a customer with the information provided, but they either do not have an active ledger or reservation. Adding notes via Slack only works on customers with an active reservation or ledger"}', $respType, true);
		}
	}
	else{
		pushOutput($respUrl, '{"text": "I didn\'t find any customers with the information you provided. You can add notes via the following customer information:\n/addnote L006 H177 Left VM for customer about balance\n/addnote adam@americanstoragenc.com E-mailed customer lease agreement\n/addnote 9196737452 Spoke to customer about picking up keys to new lock from kiosk"}', $respType, true);
	}
		   
}
else{
	die();
}
?>