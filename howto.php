<?php
//PHP Basics

$variableName = "Something"; //variable assignment, $ is the variable declaration
$int = 4; //declare integer, loosely typed language
$float = 4.5; //declare float
$intTypecast = (int)$float; //typecase a float as an integer, $intTypecase now equals 4

$arr = array(); //blank array assignment

$arr = array(1, 2, 3, 4, 5);
/*
	$arr[0] = 1
	$arr[1] = 2
	$arr[2] = 3 etc etc etc
*/

$arr = array( 0 => 4, 1 => 2, 2 => "banana", "fruit" => 4.5);
/*
	$arr[0] = 4
	$arr[1] = 2
	$arr[2] = banana
	$arr["fruit"] = 4.5
*/

$arr[0] = array("fruit" => "banana", "price" => 12.99, "prettyPrice" => "$ 12.49");
/*
	$arr[0] = array
		$arr[0]["fruit"] = banana
		$arr[0]["price"] = 12.99
		$arr[0]["price"] = $ 12.49
	$arr[1] = 2
	$arr[2] = banana
	$arr["fruit"] = 4.5
*/

//How to loop through an array
foreach($arr as $key => $value){
	//loops through array from start to finish. Is not recursive
	var_dump($key); //will output 0, 1, 2, "fruit"
	var_dump($value); //will output a string saying is an array, 2, "banana", 4.5
}


?>