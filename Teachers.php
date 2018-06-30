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

/*
echo nl2br("\n");
echo '$_SESSION = ';
print_r($_SESSION);
echo nl2br("\n");
*/

// $QUERY = "SELECT MemberName FROM NSattend_Classes WHERE MemberRole = 'instructor' "; 
$QUERY = "SELECT * FROM NS_Teachers ORDER BY FirstName"; 
$rTeachers = $db->query($QUERY);
	
if (false) {
	echo nl2br("\n");
	echo '$rTeachers = ';
	print_r($rTeachers);
	echo nl2br("\n");
}

// if <<Back from next page
if ( isset($_SESSION["teacher"]) ) {
	$TeacherName = $_SESSION["teacher"];
}
else {
		$TeacherName = "";
}


 ?>
 
 <script type="text/javascript">
  
	var bGlobal_SheetIsSaved = true;  // for testing before leaving sheet 7/28/17
	
 </script>
    
 <html>  
		<head>
      		<link rel="stylesheet" type="text/css" href="Teachers.css">
      		<link rel="stylesheet" type="text/css" href="Media_Teachers.css">
      		<link rel="stylesheet" type="text/css" href="Mobile_Teachers.css"> 

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

			 	<form id="userform" class="form-entry" method="POST" action="http://www.dweeda.com/NSattend/Classes.php">									
			 		<span id="banner">
			 		
						<div id="submitdiv">
				 			<button type="submit" id="submitbar">LIST CLASSES</button>
				 		</div>
						<div id="btndiv">	
							<button id="logoutbtn" onclick="Done(); return false"><span style="color: crimson">x</span> Log Out</button>
							<span id="instructspan">Select teacher:</span>								
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
	var TeacherName = <?php echo json_encode($TeacherName); ?>;
	console.log('TeacherName = '+TeacherName);
			
	var rTeachers = <?php echo json_encode($rTeachers); ?>;
	var nTeacherCount = rTeachers.length;	
	console.log('nTeacherCount = ' + nTeacherCount);
	
	// var rUniqueTeachers = [...new Set(rTeachers)];
	var rUniqueTeachers = Array.from(new Set(rTeachers));
	var nUniqueTeacherCount = rUniqueTeachers.length;	
	console.log('nUniqueTeacherCount = ' + nUniqueTeacherCount);

	// test
	// nUniqueTeacherCount = 2;
	
	var i, rTeacher, sTeacher, sTeacherLine;
	for (i=0; i<nUniqueTeacherCount; i++) {
		var rTeacher = rUniqueTeachers[i];
		var sTeacherFirst = rTeacher['FirstName'];
		var sTeacherLast = rTeacher['LastName'];
		var sTeacher = sTeacherFirst + " " + sTeacherLast;
		console.log(sTeacher);

		// sTeacherLine = '<span id="listrow"><input type="checkbox" id="studentcheck" style="transform: scale(2.5)" name="student[]" value="'+sTeacher+'"><label for="student[]" id="studentname" style="font-size:72px; margin-left:10px">'+sTeacher+'</label></span>';
		// sTeacherLine = '<span id="listrow" style="margin-left:20px"><input type="radio" id="teacherradio"  style="z-index: -1; transform: scale(2.5); height:25px"  name="teacher" value="'+sTeacher+'" checked><label for="teacher"  id = "teachername" style="font-size:72px; margin-left:20px">'+sTeacher+'</label></span>';

		
		// turn on radio if returning from next page
		if (TeacherName == sTeacher) {
			sTeacherLine = '<label><span id="listrow" style="margin-left:20px"><input type="radio" id="teacherradio"  style="z-index: -1; transform: scale(2.5); height:25px"  name="teacher" value="'+sTeacher+'" checked><span id = "teachername">'+sTeacher+'</span></span></label>';
			// sTeacherLine = '<span id="listrow" style="margin-left:20px"><input type="radio" id="teacherradio"  style="z-index: -1; transform: scale(2.5); height:25px"  name="teacher" value="'+sTeacher+'" checked><label for="teacherradio"  id = "teachername" style="font-size:72px; margin-left:20px">'+sTeacher+'</label></span>';
			// sTeacherLine = '<span id="listrow" style="margin-left:20px"><input type="radio" id="teacherradio"  style="z-index: -1; transform: scale(2.5); height:25px"  name="teacher" value="'+sTeacher+'" checked><span id = "teachername" style="font-size:72px; margin-left:20px">'+sTeacher+'</span></span>';
		}
		else {
			sTeacherLine = '<label><span id="listrow" style="margin-left:20px"><input type="radio" id="teacherradio"  style="z-index: -1; transform: scale(2.5); height:25px"  name="teacher" value="'+sTeacher+'" ><span id = "teachername">'+sTeacher+'</span></span></label>';
			// sTeacherLine = '<span id="listrow" style="margin-left:20px"><input type="radio" id="teacherradio"  style="z-index: -1; transform: scale(2.5); height:25px"  name="teacher" value="'+sTeacher+'" ><label for="teacherradio"  id = "teachername" style="font-size:72px; margin-left:20px">'+sTeacher+'</label></span>';
			// sTeacherLine = '<span id="listrow" style="margin-left:20px"><input type="radio" id="teacherradio"  style="z-index: -1; transform: scale(2.5); height:25px"  name="teacher" value="'+sTeacher+'"><span id = "teachername" style="font-size:72px; margin-left:20px">'+sTeacher+'</span></span>';
		}

		// $('#userform').append(sTeacherLine);
		$('#listdiv').append(sTeacherLine);
	
	}

/*	
	$('#submitbtn').click(function(e){            
		e.preventDefault();			// 3/23/17 - prevent reload (not sure why) 

		var sData = $('#userform').serialize();
		alert(sData);
		
		// window.location.href = "http://www.dweeda.com/NSattend/Classes.php"
	});
*/	

});  // *********   end document ready

function Done() {
	// alert("Done()");
	console.log("Done()");
	
	var sReturnPage = "http://www.dweeda.com/NSattend/";
	console.log('Done(): sReturnPage = '+sReturnPage);

	window.location.href = sReturnPage;		

/*
	var strIsTimedOut = GetSessionTimedOut();
	if (strIsTimedOut === "true") {	
		window.location.href = "http://www.dweeda.com/processwire-master/login/";
	}
	else {
		window.location.href = sReturnPage;		
	}
*/

} 
 </script>  
 
 <?php
 
	$mysqli->close();
	
 ?>