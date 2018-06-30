<?php
	session_start(); 
	echo session_id();
	
	$_SESSION["page1"] = "PG1";

	echo nl2br("\n");
	echo '$_SESSION = ';
	print_r($_SESSION);
	echo nl2br("\n");

 ?>
 
<html>  
	<head>
		<title>Session Test Pg1</title>    
   </head>
     
   <body>
   
		<form id="userform" method="POST" action="http://www.dweeda.com/NSattend/Session2.php">
   			 <button type="submit" id="nextbtn">NEXT >></button>
   		</form>
   			 
   </body>
</html> 