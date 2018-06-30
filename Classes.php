<?php
session_start(); 
// echo session_id();
// ini_set('session.gc_maxlifetime', _TIMEOUT); 

// INIT $mysqli DB connection 
require_once('DBConn.php');

if($mysqli == false) {
    echo "--- NO DB ---";     
}
if (mysqli_connect_errno()) {
	printf("Connect failed: %s\n", mysqli_connect_error());
	// $session->redirect($loginurl);
   echo "--- BAD CONNECT ---"; 
   	exit();										// *********************  lets eventually do a redirect here!!  (NEED user message)
}

// new DB class
require_once('MySQLDB.php');

$db = new DB(DB_NAME, DB_USER, DB_PASSWORD, DB_HOST);

if($db == false) {
    echo "--- DB class construct FAIL ---";     // *********************  lets eventually do a redirect here!!
}
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
	// $session->redirect($loginurl);
	echo "\nHERE ERR";
    exit();										// *********************  lets eventually do a redirect here!!  (NEED user message)
}	

if (false) {
	echo nl2br("\n");
	echo '$_SESSION = ';
	print_r($_SESSION);
	echo nl2br("\n");
}

if (false) {
	echo nl2br("\n");
	echo '$_POST = ';
	print_r($_POST);
	echo nl2br("\n");
}

if ( isset($_POST["teacher"]) ) {
	$TeacherName = $_POST["teacher"];
	$_SESSION["teacher"] = $TeacherName;
	// echo '$_SESSION teacher IS set (<< Back)';
}
else {
	$TeacherName = $_SESSION["teacher"];
}

// echo nl2br("\n");
// echo '$TeacherLast = '.$TeacherName;
// echo nl2br("\n");


/*
	if ( isset($_SESSION["teacher"]) ) {
		$TeacherName = $_SESSION["teacher"];
		// echo '$_SESSION teacher IS set (<< Back)';
	}
	else {
		$TeacherName = $_POST["teacher"];
		$_SESSION["teacher"] = $TeacherName;
		// echo '$_SESSION teacher NOT set yet';
	}
*/

// split value by space into first and last name
$pieces = explode(" ", $TeacherName);
$TeacherFirst = $pieces[0];
$TeacherLast = $pieces[1];

// echo '$TeacherFirst = '.$TeacherFirst;
// echo nl2br("\n");
// echo '$TeacherLast = '.$TeacherLast;

// $QUERY = "SELECT ClassName FROM NSattend_Classes WHERE MemberRole = 'instructor' AND MemberName='" . $TeacherName . "' "; 
$QUERY = "SELECT Classes FROM NS_Teachers WHERE FirstName = '" . $TeacherFirst . "' AND LastName='" . $TeacherLast . "' "; 
// $QUERY = "SELECT Classes FROM NS_Teachers WHERE LastName='" . $TeacherLast . "' "; 

$rClasses = $db->query($QUERY);
	
if (false) {
	echo nl2br("\n");
	echo nl2br("\n");
	echo '$QUERY = '.$QUERY;

	echo nl2br("\n");
	echo nl2br("\n");
	echo '$rClasses = ';
	print_r($rClasses);
	echo nl2br("\n");
}

// if <<Back from next page
if ( isset($_SESSION["class"]) ) {
	$ClassName = $_SESSION["class"];
}
else {
		$ClassName = "";
}


// exit();
 
 ?>
 
 <script type="text/javascript">
  
	var bGlobal_SheetIsSaved = true;  // for testing before leaving sheet 7/28/17
	
 </script>
    
 <html>  
		<head>
      		<link rel="stylesheet" type="text/css" href="Classes.css">
     		<link rel="stylesheet" type="text/css" href="Media_Classes.css">
      		<link rel="stylesheet" type="text/css" href="Mobile_Classes.css"> 

			<title>NS Attendance</title>    
			<!-- <link rel="stylesheet" type="text/css" href="./styles/Classes.css"> -->
			
  			<!-- Add the JQuery UI CSS -->
  			<link href="./css/jquery-ui.min.css" rel="stylesheet">

  			<!-- Add the JQuery UI JS file -->
  			<script src ="./js/jquery-ui.min.js" > </script>

          <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>  
  			<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  			<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
      </head>
        
      <body>
      		<!-- <?php include('NavUserHdr.php'); ?>	-->
			<div id="container">

			 	<form id="userform" method="POST" action="http://www.dweeda.com/NSattend/Students.php">
			 		<span id="banner">
			 				 		
				 		<div id="submitdiv">
				 			<button type="submit" id="submitbar">LIST STUDENTS</button>
				 		</div>
						<div id="btndiv">
							<button id="backbtn" onclick="Back(); return false"><span style="color: black"><<</span> Back</button>
							<button id="logoutbtn" onclick="Done(); return false"><span style="color: crimson">x</span> Log Out</button>
							<div id="hdrdiv"><?php  echo $TeacherName ?></div><br>
							<span id="instructspan">Select class:</span>						
						</div>
						
					</span>

					<div id="listdiv">		
					</div>
 		
				</form>									

			</div> <!-- container -->
      </body>  
 </html>  
 
 
<script type="text/javascript">  
	

$( window ).load(function() { 


	if (true) {   // for debug 	
		// do something on load
	}	

})	

$(document).ready(function() {   
	
	console.log('in doc.ready');
	
	// will have a value if returning from next page
	var sClassName = <?php echo json_encode($ClassName); ?>;
	console.log('sClassName = '+sClassName);
			
	var rClasses = <?php echo json_encode($rClasses); ?>;

	// array should have just one row with one field = multvalued with ';' seperator
	var i, rClass, sClass, sClassLine;
	var rClassList = rClasses[0];
	sClasses = rClassList['Classes'];
	// alert(sClasses);

	var rClassList = sClasses.split(";");
	var nClassCount = rClassList.length;
	console.log('nClassCount = ' + nClassCount);	

	for (i=0; i<nClassCount; i++) {
		sClass = rClassList[i];
		// sClass = rClass['ClassName'];
		console.log('sClass = '+sClass);
						
		// turn on radio if returning from next page
		if (sClassName == sClass) {
			sClassLine = '<label><span id="listrow" style="margin-left:20px"><input type="radio" id="classradio"  style="transform: scale(2.5); height:25px"  name="class" value="'+sClass+'" checked><span id="classname">'+sClass+'</span></span></label>';
		}	
		else {
			sClassLine = '<label><span id="listrow" style="margin-left:20px"><input type="radio" id="classradio"  style="transform: scale(2.5); height:25px"  name="class" value="'+sClass+'"><span id="classname">'+sClass+'</span></span></label>';
		}
				
		// $('#userform').append(sClassLine);
		$('#listdiv').append(sClassLine);
		
	}	

/*	
		if ( i==0 ) {
			elemLine = $('#userform').append(sClassLine);
		}
		else {
			// $(sAddToThisRowRef).after(sNewRow);  
			elemLine = $('#userform').append(sClassLine);  
		}          	
		
		// turn on radio if returning from next page
		if (sClassName == sClass) {
			console.log("YO");
			// $( "#userform input:last-child" ).prop('disabled', false);
			elemLine.prop('disabled', false);
		}

		
}
*/

/*	
	$('#submitbtn').click(function(e){            
		e.preventDefault();			// 3/23				<h3><?php  echo $ClassName ?></h1>/17 - prevent reload (not sure why) 

		var sData = $('#userform').serialize();
		alert(sData);
		
		window.location.href = "http://www.dweeda.com/NSattend/Students.php"
	});
*/	

/*
	$('#submitexport').click(function(){            
					
		// alert("SAVING...");  
	 	console.log("EXPORTING...");  

		$.ajax({  
             // url:"http://www.dweeda.com/processwire-master/update_billsdb/",         
             url:"http://www.dweeda.com/processwire-master/exportworksheettocsv/",         
             // url:"/processwire-master/update_billsdb/",          
    			// url:"../../../TheBills/UpdateBillsDB.php",         
    			// url:"UpdateBillsDB.php",        
             method:"POST",  
             data:$('#workform').serialize(),  
             success:function(data)  
             {  
					var downloadUrl = <?php echo  json_encode($config->urls->templates); ?>+"Users/" + <?php echo json_encode($Username); ?> + "/WorksheetExport.csv";
				   window.open(downloadUrl, 'download_window', 'toolbar=0,location=no,directories=yes,status=0,scrollbars=0,resizeable=yes,width=600,height=300,top=300,left=300');
					window.focus();
					 alert("EXPORT complete.");
					 // alert("EXPORT data:  "+data); 
				}
			
		});        // end ajax
	});        // end EXPORT
*/

});  // *********   end document ready

function Done() {
	console.log("Done()");
	
	var sReturnPage = "http://www.dweeda.com/NSattend/";
	console.log('Done(): sReturnPage = '+sReturnPage);

	window.location.href = sReturnPage;		

} 

function Back() {
	console.log("Back()");
	
	var sBackPage = "http://www.dweeda.com/NSattend/Teachers.php";
	console.log('Back(): sBackPage = '+sBackPage);

	window.location.href = sBackPage;		
} 

</script>  
 
 <?php
 
	$mysqli->close();
	
 ?>