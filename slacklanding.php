<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
if($_REQUEST["token"] = "sRsflMIES2eveufxb8jFYa9v"){
	include('jsontest.php');
	include('io.php');//init includes
	
	header('Content-Type: application/json'); //always json STDOUT
	//header('Content-Length: '.strlen($initResp));
	ignore_user_abort(true);
	
	pushOutput(null, '{"text": "Hold on a sec while I look up that customer for you..."}', 'stdout', false); //push this output immediately
	
	//INIT GLOBALS
	$text = $_REQUEST["text"];
	$respUrl = $_REQUEST["response_url"];
	$respType = ($_REQUEST["response_url"]) ? 'curl' : 'stdout';
	
	$propertyArray = array(
		"L001" => "Site 2",
		"L01A" => "Site 1",
		"L002" => "H&R",
		"L003" => "Harrisburg",
		"L004" => "Speedway",
		"L005" => "Attic",
		"L006" => "Smithfield",
		"L007" => "AAA",
		"L008" => "Armadillo",
		"L009" => "AAA High Point"
	);
	
	
	//MODIFY THE TEXT PASSED FROM SLACK
	$text = strtoupper($text); //uppercase, because expected inputs require no lowercase letters
	$text = explode(" ",$text); //explode the input string into an array by spaces
	
	$len = count($text);
	
	if( $len === 1 ){ //after exploding, if the array has 1 index, it was likely a phone number
		
		if(filter_var($text[0], FILTER_VALIDATE_EMAIL)){ //if has no spaces, then test e-mail case first
			$repl = $text[0];
			$flagType = 'email';
		}
		elseif(strlen($text[0]) !== 10){ //IF LENGTH IS NOT A PERFECT 10 digits, try PREG_REPLACE first to remove common error characters
			$repl = preg_replace(array(
				"/^(\+)?1/", "/-/", "/\s/" //pattern is an array of patterns to replace in string	
			), "", $text[0]); //this strips out a leading +1, dashes, or non-printing characters anywhere in the string
			$flagType = 'phone';
		}
		else{ //ELSE PHONE NUMBER MUST'VE BEEN TEN DIGITS, SO SET THE OUTPUT VAL TO $repl SO WE HAVE A UNIVERSAL WAY TO ACCESS THE OUTPUT
			$repl = $text[0];
			$flagType = 'phone';
		}
		
		
		if(preg_match("/^[0-9]{10}$/", $repl) !== 1 && $flagType === 'phone'){ //WE DID REPLACEMENTS AND EXPECT A PHONE NUMBER, TEST FOR THIS
			pushOutput($respUrl, '{ "text": "I did not recognize that phone number, try again and remember to format the number as a 10-digit phone number without spaces or dashes like 9196737452"}', $respType, true);
		}
		
		if($flagType === 'phone'){
			$customers = getAllCustomers($repl, '', ''); //GET CUSTOMERS
		}
		elseif($flagType === 'email'){
			$customers = getAllCustomers('', '', $repl); //GET CUSTOMERS
		}
		
		if(!$customers){ //IF NO CUSTOMERS, THEN RESPONSD TO SLACK UPDATE URL AND DIE()
			pushOutput($respUrl, '{ "text": "No customer found by the '.$flagType.' '.$repl.'"}', $respType, true);
		}
		
		$ledgers = getLedgersByCustomer($customers); //THIS WILL ONLY EXECUTE IF THERE ARE CUSTOMERS BECAUSE THE PREV pushOutput CALL ASKS SCRIPT TO DIE()
		
		
	}
	
	
	elseif( $len === 2 ){
		$customers = getCustomer('', $text[0], $text[1]); //[0] == location code like L006, [1] == unit like H177
		
		if(!$customers){
			pushOutput($respUrl, '{ "text": "Unit '.$text[1].' is vacant."}', $respType, true);
		}
		
		$ledgers = getLedgersByCustomer($customers);
	}
	else{
		pushOutput($respUrl, '{ "text": "I didn\'t understand your request. If you\'re looking up a customer by phone, type \n/whois 9196737452\nIf you are looking up a customer by unit type\n/whois L006 H177"}', $respType, true);
	}
	
	$data["text"] = "Customer name: ".$customers[0]["firstName"]." ".$customers[0]["lastName"];
	$data["ephmereal"] = "true";
	$data["attachments"] = array();
	$data["attachments"][0] = array();
	$data["attachments"][0]["title"] = $propertyArray[$customers[0]["lCode"]]." - ".$customers[0]["unit"];
	$data["attachments"][0]["fields"] = array();
	
	if(count($ledgers) === 1){
		$data["attachments"][0]["title_link"] = $ledgers[0]["myHub"];
	}
	
	$index = 0;
	foreach($ledgers as $ledger){
		
		$data["attachments"][0]["fields"][$index] = array();
		
		if($ledger["type"] === 'cust'){
			$data["attachments"][0]["fields"][$index]["title"] = $ledger["firstName"]." ".$ledger["lastName"]." - ".$ledger["unit"]." Owes: $".$ledger["balance"];
			$data["attachments"][0]["fields"][$index]["value"] = "Paid thru: ".$ledger["paidThru"]."\nCustomer since: ".$ledger["leaseDate"]."\nRent: $".$ledger["rent"]."\nInsurance: ".$ledger["insurance"]."\nGate code: ".$ledger["gateCode"]."\nPhone: ".$ledger["phone"]."\nE-mail: ".$ledger["email"];
			$data["attachments"][0]["fields"][$index]["short"] = "true";
			$index++;
		}
		elseif($ledger["type"] === "resv" || $ledger["type"] === 'inq'){
			$qType = ($ledger["type"] === 'resv') ? "RESERVATION" : "INQUIRY";
			
			$data["attachments"][0]["fields"][$index]["title"] = $qType.": ".$ledger["firstName"]." ".$ledger["lastName"]." - ".$ledger["unit"];
			
			$data["attachments"][0]["fields"][$index]["value"] = "Needed by: ".$ledger["needed"]."\nPlaced: ".$ledger["placed"]."\nQuoted Rate: $".$ledger["rent"]."\nPhone: ".$ledger["phone"]."\nE-mail: ".$ledger["email"];
			$data["attachments"][0]["fields"][$index]["short"] = "true";
			
			$index++;
		}
	}
	
	$json = json_encode($data, JSON_FORCE_OBJECT);
	
	pushOutput($respUrl, $json, $respType, true);
	/*$data = '{
		"text": "Customer name: Adam Jarrell",
		"attachments": [
			{
				"text": "Rents at Smithfield Rd, Unit H177, since 11/1/17, pays $104"
			},
			{
				"text": "Rent: $105"
			},
			{
				"text": "Moved-in: 11/1/2017"
			}
		]
	}';*/
	
	//echo $data;
}
else{
	header("HTTP/1.0 404 Not Found");
	die();
}
?>