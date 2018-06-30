<?php 

 
include('./mpdf.php'); 
$mpdf=new mPDF(); 
$mpdf->WriteHTML('<div style="color:yellow;">SAMPLE TEXT, WITH HTML TAGS Too!</div>'); 
$mpdf->Output();\
exit();



$pathtothebills = '../../../TheBills/';

$DBConnPath = $pathtothebills.'DBConn.php'; 
require_once($DBConnPath);
if($mysqli == false){
	echo "--- NO DB ---";
	echo nl2br("\n\n");
}
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

date_default_timezone_set('America/New_York');  // need this for DATE setting in MYSQL

// PW way
$userid = 	$session->get("userid");  // PW way

//						
//  EXPORT TO CSV
//
$todaysdate = 	date("F j, Y");
$username = GetUsername($mysqli, $userid);
$CSVfolder = "./Users/".$username.'/';
$INITAVAIL = GetInitAvail($mysqli, $userid);   // (...  Will eventualy come direct from ACTUAL BANK with API call  ...)
$fopenfailed = FALSE;

$rCSVrow = [];
$CSVfilename = "ProjectionExport.csv";
$CSVfilepath = $CSVfolder.$CSVfilename;			
// $fh = fopen($CSVfile, "w");
$fh = fopen($CSVfilepath, "w+"); // overwrite

if ($fh == FALSE) {
	echo ' !! fopen failed: '.$CSVfilepath.'   ';
	$fopenfailed = TRUE;
}
else {
	$title_sheet = "PROJECTION EXPORT";
	fputcsv($fh, array($title_sheet));
	$title_date = $todaysdate;	
	fputcsv($fh, array($title_date));
	$title_user = $username;	
	fputcsv($fh, array($title_user));
	fputcsv($fh, array('','','','','','','','',"Initial Bal=",$INITAVAIL));
	$headers = array('Description', 'Date Due', 'Date Scheduled', 'Date Paid', 'Amount Due', 'Date Posted', 'Notes', 'Debit', 'Credit', 'Available Balance', 'AutopayYN');
	fputcsv($fh, $headers);
}

$number = count($_POST["descr"]); 
if ($number > 0)  {     

    for ($i=0; $i<$number; $i++)  {  

		$rCSVrow[0] = 	 $_POST["descr"][$i];  					
	     	$rCSVrow[1] = $_POST["duedate"][$i];
		$rCSVrow[2] = $_POST["sched"][$i];
  	    	     	$rCSVrow[3] = $_POST["arrange"][$i];	
	     	$rCSVrow[4] = $_POST["amt"][$i];
	     	$rCSVrow[5] = $_POST["paiddate"][$i];
	     	$rCSVrow[6] = $_POST["confirm"][$i];    	     			
	     	$rCSVrow[7] = $_POST["debit"][$i];
	     	$rCSVrow[8] = $_POST["credit"][$i];
	     	$rCSVrow[9] = $_POST["availbal"][$i];
  			$rCSVrow[10] = $_POST["autopay"][$i];
 
			//						
			//  EXPORT ROW TO CSV
			//
			if ($fopenfailed != TRUE) {
				fputcsv($fh, $rCSVrow);
			}
    }
}

$mysqli->close();

// Close the CSV file
fclose($fh);



function formatdate($datein) {
	if ($datein == NULL) {
		return "";			
	}	
	
	$pieces = explode("/", $datein);
	$dbdate = $pieces[2] ."-".$pieces[0]."-".$pieces[1];
			
	return $dbdate;
} 

function GetUsername($USERSmysqli, $USERID) {
	
	$Uquery = "SELECT user_username FROM USERS WHERE ID='" . $USERID . "' ";

	$Uresult = $USERSmysqli->query($Uquery);
	if (!$Uresult){
		echo "QUERY FAIL";
	}

	$AllURows = [];
	while ($row = $Uresult->fetch_array(MYSQLI_NUM)) {
 		$AllURows[] = $row;
	}		
	
	$usernm = 	$AllURows[0][0];	
	// echo "usernm=".$usernm;
	
	return $usernm;
}	

function GetInitAvail($USERSmysqli, $USERID) {
	
	$Uquery = "SELECT user_initavailbal FROM USERS WHERE ID='" . $USERID . "' ";

	$Uresult = $USERSmysqli->query($Uquery);
	if (!$Uresult){
		echo "QUERY FAIL";
	}

	$AllURows = [];
	while ($row = $Uresult->fetch_array(MYSQLI_NUM)) {
 		$AllURows[] = $row;
	}		
	
	$usernm = 	$AllURows[0][0];	
	// echo "usernm=".$usernm;
	
	return $usernm;
}		
		

 ?>