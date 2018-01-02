<?php
//this is the landing page for the slack slash command /sms

if($_REQUEST["token"] === 'wUkhIcmC6f33t8S7DmkwFLzD'){ //valid slack request
	if($_REQUEST["text"] === 'debug'){ //$_REQUEST["text"] holds the entirety of the slack slash command after the slash command and trailing space, i.e. "/sms "
		header("Content-Type: json/application"); //tell end-user response is json
		
		$input = file_get_contents('php://input'); //get the entire request from the user (slack)
		
		echo($input); //echo that json string back to the user (assumes input was in json, which if msg is from slack, it is)
	}
	else{
		header("Content-Type: plain/text");
		
		echo("Valid request received, to view debug data, send /sms debug");
	}
}
else{
	header("HTTP/1.0 404 Not Found"); //sends a 404 error to browser, makes the user think the page does not exist
	die();
}
?>