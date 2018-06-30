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

$ClassName = $_POST["class"];
$_SESSION["class"] = $ClassName;

if (false) {
	echo '$_POST = ';
	print_r($_POST);
	echo nl2br("\n");
	
	echo '$_SESSION = ';
	print_r($_SESSION);
	echo nl2br("\n");
}

// $QUERY = "SELECT MemberName FROM NSattend_Classes WHERE MemberRole = 'student' AND ClassName='" . $ClassName . "' "; 
$QUERY = "SELECT * FROM NS_Students ORDER BY FirstName"; //  WHERE MemberRole = 'student' AND ClassName='" . $ClassName . "' "; 
$rStudents = $db->query($QUERY);

if (false) {
	echo nl2br("\n");
	echo nl2br("\n");
	echo '$QUERY = '.$QUERY;

	echo nl2br("\n");
	echo nl2br("\n");
	echo '$rStudents = ';
	print_r($rStudents);
	echo nl2br("\n");
}

$TODAY = date('m\/d\/y');

 ?>
 
 <script type="text/javascript">
  
	var bGlobal_SheetIsSaved = true;  // for testing before leaving sheet 7/28/17
	
 </script>
    
 <html>  
		<head>
      		<link rel="stylesheet" type="text/css" href="Students.css">
     		<link rel="stylesheet" type="text/css" href="Media_Students.css">
      		<link rel="stylesheet" type="text/css" href="Mobile_Students.css"> 

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
			
				<form id="userform" class="form-entry" >
			 		<span id="banner">
			 		
				 		<div id="submitdiv">
				 			<button type="submit" id="submitbar">SUBMIT</button>
				 		</div>
						<div id="btndiv">
							<button id="backbtn" onclick="Back(); return false"><span style="color: black"><<</span> Back</button>
							<button id="logoutbtn" onclick="Done(); return false"><span style="color: crimson">x</span> Log Out</button>
							<div id="hdrdiv"><?php  echo $ClassName ?></div>
						</div>
						<span id="datelabel">Attendance Date: </span><input id = "DateInput" name="date" type="text" value="<?php  echo $TODAY ?>"><br>		
						<span id="viewdoc" >Attendance Doc: <a class="linkbtns" id="classfilelink" href="./classdocs/<?php  echo $ClassName ?>.txt" target="_blank" title="View this class's attendance doc"><img id="exportimg" src="./images/export.png" alt=""></a></span>
						<span id="instructspan">Select students in attendance:</span>
															
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

  	//
	//   DATE PICKER
	//
	$("#DateInput").datepicker({
    // Show month dropdown
    changeMonth: true,
    // Show year dropdown
    changeYear: true,
    dateFormat: "mm/dd/y",
    // Number of months to display
    numberOfMonths: 1,
    // Define maxDate
    maxDate: 365,
    // Define minDate
    minDate: -365
	});

  	// 2/28/17 - for dynamic add of new row
	$('body').on('focus',"#DateInput", function(){
	    $(this).datepicker();
	})
		
	$('div.ui-datepicker').css({ fontSize: '36px' });
		
	var rStudents = <?php echo json_encode($rStudents); ?>;
	var nStudentCount = rStudents.length;
	console.log('nStudentCount = ' + nStudentCount);
	
	var i;
	for (i=0; i<nStudentCount; i++) {
		var rStudent = rStudents[i];
		// var sStudent = rStudent['LastName'];
		var sStudentFirst = rStudent['FirstName'];
		var sStudentLast = rStudent['LastName'];
		var sStudent = sStudentFirst + " " + sStudentLast;
		// console.log(sStudent);
		
		var sStudentLine = '<label><span id="listrow" style="margin-left:20px"><input type="checkbox" id="studentcheck" style="transform: scale(2.5); height:25px" name="student[]" value="'+sStudent+'"><span id="studentname">'+sStudent+'</span></span></label>';
		// var sStudentLine = '<span id="listrow"><input type="checkbox" id="studentcheck" style="transform: scale(2.5)" name="student[]" value="'+sStudent+'"><label for="student[]" id="studentname" style="font-size:72px; margin-left:10px">'+sStudent+'</label></span>';
		// var sStudentLine = '<input type="checkbox" id="studentcheck" name="student-selected" value="'+sStudent+'"><span>'+sStudent+'</span>';
		// var sStudentLine = '<span id="listrow"><input type="checkbox" id="studentcheck" name="student" value="'+sStudent+'"><span id="studentname">'+sStudent+'</span></span>';

		if ( i==0 ) {
			$('#listdiv').append(sStudentLine);
		}
		else {
			// $(sAddToThisRowRef).after(sNewRow);  
			$('#listdiv').append(sStudentLine);  
		}
/*	
		if ( i==0 ) {
			$('#userform').app     		<link rel="stylesheet" type="text/css" href="Media.css">
      		<link rel="stylesheet" type="text/css" href="iPhone.css"> 
end(sStudentLine);
		}
		else {
			// $(sAddToThisRowRef).after(sNewRow);  
			$('#userform').append(sStudentLine);  
		}          	
*/	
	}
	
	$('#submitbar').click(function(e){            
		e.preventDefault();			// 3/23/17 - prevent reload (not sure why) 

		// var sStudentsSel = $('#userform').serialize();
		// alert(sStudentsSel);        
					
		// alert("SAVING...");  
	 	console.log("SUBMITTING...");  

/*		// timeout handling 9/7/17
		var strIsTimedOut = GetSessionTimedOut();
		if (strIsTimedOut === "true") {	
			window.location.href = "http://www.dweeda.com/processwire-master/login/"
		}
*/
		$.ajax({     
    			url:"SubmitStudents.php",        
             method:"POST",  
             data:$('#userform').serialize(),  
             success:function(data)  
             {  
             	// alert("Attendance SUBMIT complete:  "+data); 
             	alert("Attendance Submitted");  
/*
					var downloadUrl = <?php echo  json_encode($config->urls->templates); ?>+"Users/" + <?php echo json_encode($Username); ?> + "/WorksheetExport.csv";
				   window.open(downloadUrl, 'download_window', 'toolbar=0,location=no,directories=yes,status=0,scrollbars=0,resizeable=yes,width=600,height=300,top=300,left=300');
					window.focus();
*/

				}
			
		});        // end ajax
	});        // end EXPORT
	
});  // *********   end document ready

function Done() {
	// alert("Done()");
	console.log("Done()");
	
	var sReturnPage = "http://www.dweeda.com/NSattend/";
	console.log('Done(): sReturnPage = '+sReturnPage);

	window.location.href = sReturnPage;		

}
function Back() {
	console.log("Back()");
	
	var sBackPage = "http://www.dweeda.com/NSattend/Classes.php";
	console.log('Back(): sBackPage = '+sBackPage);

	window.location.href = sBackPage;		
} 
 
</script>  
 
 <?php
 
	$mysqli->close();
	
 ?>