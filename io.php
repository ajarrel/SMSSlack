<?php
function pushOutput($url, $d, $dest = 'curl', $die = false, $header = 'application/json'){ //$dest === 'curl' or 'stdout' $d = data to be written, $url = url (curl only), $die = true means kill instead of return a value
	
	if($dest === 'curl'){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $d);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:'.$header)); //allow for curl to send other data types besides application/json
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close($ch);
		
		//echo $d;
		//echo $result;
	}
	elseif($dest === 'file'){
		fwrite($fp, $d."\r\n");
		fclose($fp);
	}
	elseif($dest === 'stdout'){
		header('Content-Length: '.strlen($d));
		ob_implicit_flush(true);
		ob_end_flush();
		echo $d;
		flush();
		ob_flush();
		$result = NULL;
	}
	
	return (($die) ? die() : $result);
}

function filterSlackInput($str){
	
	$arr = explode(' ',$str, 3);
	
	$response = array();
	
	if(preg_match('/(1)?[0-9\-]{7,8}/',$arr[0])){
		$response["type"] = "phone";
		$response["ident"] = preg_replace('/[^0-9]*/','',$arr[0]); //replace anything that's not a number with a null string
		$response["note"] = $arr[1]." ".$arr[2];
	}
	elseif(filter_var($arr[0], FILTER_VALIDATE_EMAIL)){
		$response["type"] = "email";
		$response["ident"] = $arr[0]; //nothing further to be done
		$response["note"] = $arr[1]." ".$arr[2];
	}
	elseif(preg_match('/(l|L)0(1|0)[aA0-9]{1}/', $arr[0])){
		$response["type"] = 'loc';
		$response["ident"] = array(
			"loc" => strtoupper($arr[0]),
			"unit" => strtoupper($arr[1])
		);
		$response["note"] = $arr[2];
	}
	elseif(preg_match('/(l|L)0(1|0)[aA0-9]{1}/', $arr[1])){
		$response["type"] = 'loc';
		$response["ident"] = array(
			"loc" => strtoupper($arr[1]),
			"unit" => strtoupper($arr[0])
		);
		$response["note"] = $arr[2];
	}
	else{
		return false;
	}
	
	return $response;
}
?>