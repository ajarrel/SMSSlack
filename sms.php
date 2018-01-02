<?php
//this is the landing page for the slack slash command /sms

error_reporting(E_ALL);
ini_set('display_errors', 1);

if($_REQUEST["token"] === 'wUkhIcmC6f33t8S7DmkwFLzD' || TRUE){ //valid slack request
	
	include('io.php');
	include('util.php');
	require ('lib/twilio/Twilio/autoload.php') ;
	//use Twilio\Rest\Client as Client;
	
	if($_REQUEST["text"] === 'debug' || true){ //$_REQUEST["text"] holds the entirety of the slack slash command after the slash command and trailing space, i.e. "/sms "
		
		$str = ''; //init string
		
		$text = explode(" ", $_REQUEST["text"], 2);
		
		foreach($_REQUEST as $key => $val){
			$str = $str . $key . " = " . $val . "\n"; //key  value  newline
		}
		
		$sid = 'ACc6fd2878203e2c29950e4774ebb0b377';
		$token = '67294a8df00433f8f6d9e9be19c3f0d4';
		$client = new Twilio\Rest\Client($sid, $token);
		
		$client->messages->create($text[0], array(
			'from' => '+19193733345',
			'body' => $text[1]
		));
		
		echo($str); //echo that json string back to the user (assumes input was in json, which if msg is from slack, it is)
	}
	else{
		header("Content-Type: text/plain");
		
		echo("Valid request received, to view debug data, send /sms debug");
	}
}
else{
	header("HTTP/1.0 404 Not Found"); //sends a 404 error to browser, makes the user think the page does not exist
	die();
}
?>