<?php  
session_start(); 

$Class = $_SESSION["class"];
$Teacher = $_SESSION["teacher"];
// print_r($_POST);
	
$filename = $Class.".txt";
$folder = './classdocs/';
$filepath = $folder.$filename;

$bCreateNew = false;
if (!file_exists($filepath)) {
	$bCreateNew = true;
}
else {
 	$bCreateNew = false;
}

$fp = fopen($filepath, "a+"); // create/open text file for appending

// report error unless creating new
$fopenfailed = FALSE;
if ( ($fp == FALSE) && ($bCreateNew == false) ) {
	echo ' !! fopen failed: '.$filepath.'   ';
	$fopenfailed = TRUE;
	die();
}

/*
$datestr = $_POST["date"];
echo $datestr;
fputs($fp, $datestr);
fclose($fp);
exit();	
*/

else {
	// echo "HEREIAM";
	
	// if new file, add class/teacher header first
	if ($bCreateNew) {
		fputs($fp, $Class." - ".$Teacher);
		fputs($fp, "\n\n");	
	}
	
	// date header
	fputs($fp, "\n\n");	
	$datestr = $_POST["date"];
	fputs($fp, $datestr);
	fputs($fp, "\n");

	// loop thru students
	$count = count($_POST["student"]);
	echo $count. " students added";
	
	if ($count > 0)  {     
    	for ($i=0; $i<$count; $i++) { 
    	 	$student = $_POST["student"][$i]; 
    	 	fputs($fp, $student);
    	 	fputs($fp, "\n"); 	
		}
	}
	
	// Close the file
	fclose($fp);
}


 ?>