<?php

//scan.php

require_once('functions.php');

/*The below section creates a form that allows the user to upload a text file or attempt login*/
	echo <<<_END
		<html>
			<head>
				<title>Text Upload</title>
			</head>

		<body>
		<table border="0" cellpadding="2" cellspacing="5" bgcolor="#00FF7F" align="center">
		<th colspan="2" align="center">Admin Access - Infected File Upload</th>
			<form method='post' action='scan.php' enctype='multipart/form-data'>
					<tr><td>
							<input type="submit" name="loginattempt" id="loginattempt" value="Login"></td></tr>
					<tr><td>Choose a file to check for viruses.</td>
					<tr><td>
						<input type='file' name='filename' size='20'>
						<input type='submit' value='Upload' name = 'Upload'>
					</td></tr>
			</form>
			</table>
			</body>
	</html>
_END;

//check if user attempts login
if(isset($_POST['loginattempt'])){
	//if the user wants to try to login, send them to the admin login page
	header("location: login.php");
}

//check if user hit upload
if(isset($_POST['Upload'])){

		//check if file was selected
		if ($_FILES) 	{

			//get contents of file, store in var
			$filecontents = file_get_contents($_FILES['filename']['tmp_name']);

			//store contents of badfiles table as an associative array
			$tempvar = mysqli_fetch_all(myq("SELECT * FROM badfiles",$hn,$un,$pw,$db));

			// flag that is true if virus found, else stays false
			$myflag = false;

			foreach($tempvar as $signature){

				//if a signature match is detected, alert user
				if(checkforsignature($filecontents,$signature[1])){

					echo "<br> This file is infected, burn your computer! <br>";
					echo "<br>The signature that matched is: " . $signature[1]."<br>";

					$myflag = true; //set flag
					break;
				}
				//if there was no match, continue to the next signature check
				else {continue;}
			}
			//if the flag wasn't set, tell the user that no virus was detected
			if(!($myflag)){echo "<br> No virus was detected. <br>";}
		}
	else{echo "<br>No file was selected.<br>";}
}

?>
