<?php
function dbConn (){
	$con=mysqli_connect("localhost","root","","db_customers"); 
		if (mysqli_connect_errno()){
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
}
function clearDir () {
	$files = glob('C:\wamp64\www\Duplicates\*');
		foreach($files as $file){
			if(is_file($file))
			unlink($file);
		}
	$files = glob('C:\wamp64\www\Duplicates\Mids\*');
		foreach($files as $file){
			if(is_file($file))
			unlink($file);
		}
}
function genDup ($line) {
	$char= "abcdefghijklmnopqrstuvwxyz0123456789";
	$buck= strlen($char);
	$ran1= rand(0,$buck-1);
	$ran2= rand(0,$buck-1);
	$ran= substr($char, $ran1, 1).substr($char, $ran2, 1);
	$ref= strpos($line, "-rf");
	$count= strlen($line);
	$sub= substr($line, $ref, $count);
	$space=strpos($sub, " ");
	$reference= substr($sub,0,$space);
	$x= strpos($line, $reference);
	$y= strlen($reference);
	$first= substr($line, 0, $x + $y);
	$second= substr($line, $x + $y);
	$data = $first."*".$ran.$second."\r\n";
	$msDup = file_put_contents("C:\wamp64\www\Duplicates\ ".date ("jmy").".txt", $data, FILE_APPEND | LOCK_EX);
}
function writeDup ($line) {
	$fields = explode ("-",$line);
		foreach ($fields as $field){
			$mne2 = substr($field,0,2);
			$mne3 = substr($field,0,3);
			$count = strlen($field);
			$val = substr($field,2,$count);
				switch ($field) {
					case ($mne3 == "dat"):
						$dat = "Date: ".substr($field,9,2)."/".substr($field,7,2)."/".substr($field,3,4)."\r\n";
						$tim = "Time: ".substr($field,11,2).":".substr($field,13,2).":".substr($field,15,2)."\r\n";
					break;
					case ($mne3 == "cdr"):
					break;
					case ($mne2 == "cd"):
						$hash = $count - 5;
						$last4 = substr($field,$hash,4);
						$first6 = substr($field,2,6);
						$cd = "Card Number: ".$first6."-xxxx-".$last4."\r\n";
					break;
					case ($mne2 == "tk"):
						$tk = "Transaction ID : ".$val."\r\n";
					break;
					case ($mne2 == "rf"):
						$rf = "Reference: ".$val."\r\n";
					break;
					case ($mne2 == "mc"):
						$mid = $val;
						$mc = "Merchant ID : ".$val."\r\n";
					break;
					case ($mne2 == "am"):
						$pounds = $val /100;
						$am = "Amount: ".$pounds."\r\n";
					break;
					case ($mne2 == "td"):
						$td = "Terminal ID: ".$val."\r\n";
					break;
				}
		}
	$txt = $tk.$dat.$tim.$mc.$td.$rf.$cd.$am."\r\n";
	$duplicatefile = file_put_contents("C:\wamp64\www\Duplicates\Mids\ ".$mid.".txt", $txt, FILE_APPEND | LOCK_EX);
}
	dbconn();
	clearDir();
	$myfile = fopen("test.txt","r") or die ("Unable to open file!");
	$read = fread ($myfile, filesize("test.txt"));
	$lines = explode (PHP_EOL, $read);
	foreach ($lines as $line){
	$first = substr($line,0,4);
		if ($first == "-dat") {
			writeDup($line);
			genDup($line);
		}
}
fclose($myfile);
?>