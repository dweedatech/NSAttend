<?php
	session_start(); 
	echo session_id();
	
	$_SESSION["page2"] = "PG2";

	echo nl2br("\n");
	echo '$_SESSION = ';
	print_r($_SESSION);
	echo nl2br("\n");

 ?>
 
<html>  
	<head>
		<title>Session Test Pg2</title>    
   </head>
     
   <body>
   				<br>
   			 	<form id="userform" method="POST" action="http://www.dweeda.com/NSattend/Session1.php">
   			 		<button type="submit" id="okbtn">OK >></button>
   			 		<button id="backbtn" onclick="Back(); return false"><< BACK</button>
   			 	</form>
   			 	
   </body>
</html> 



<script type="text/javascript">  

$(document).ready(function() {  

});  // *********   end document ready

function Back() {
	console.log("Back()");
	
	var sBackPage = "http://www.dweeda.com/NSattend/Session1.php";
	console.log('Back(): sBackPage = '+sBackPage);

	window.location.href = sBackPage;		
} 

</script>  