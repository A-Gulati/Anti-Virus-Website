<?php

//include_once 'adminlogin.php';
require_once('functions.php');

//check if admin has been verified by login, if so generate the admin access page
if(auth($hn,$un,$pw,$db)){


/*The below section creates a form that allows the user to upload a text file*/
echo <<<_END
	<html>
			<head>
				<title>Text Upload</title>
				<script>
				function validate(form) {
					fail += validateFilename(form.fn.value)
					if (fail == "") return true
					else { alert(fail); return false }
				}
				</script>
			</head>

			<body>
			<table border="0" cellpadding="2" cellspacing="5" bgcolor="#00FF7F" align="center">
			<th colspan="2" align="center">Admin Access - Infected File Upload</th>
				<form method='post' action='admin.php' enctype='multipart/form-data' onsubmit="return validate(this)">

					<tr><td>File name: </td>
							<td><input type="text" maxlength="64" name="fn" id="fn" value=""></td></tr>

					<tr><td>
						Select File:</td><td> <input type='file' name='filename' id='filename' size='20'>
						<input type='submit' value='Upload' id='upload' name='upload'></td></tr>

					<tr><td>
						<input type='submit' value='Logout' id='logout' name='logout'></td></tr>

					</form>
			</table>
			</body>
	</html>
_END;

//check if return button was pressed
if(isset($_POST['return'])){
	header('location: scan.php');
}

//check if logout button was pressed
if(isset($_POST['logout'])){
	logout();
}

//check if upload button was pressed
if(isset($_POST['upload'])){
	//make sure a file name and file are set
	if(isset($_POST['fn']) && isset($_FILES['filename']) && ($_FILES['filename']['error'] !== UPLOAD_ERR_NO_FILE)){ //check that there is both a file name and a file selected
		//store file name string
		$fn = $_POST['fn'];

			//validate filename, with php validation functions
			$fail = validate_filename($fn);

			//show user if there's an error
			echo $fail."<br>";
			echo "<!DOCTYPE html>\n<html><head><title>Validation</title>";

			//if no fail and file selected
			if ($_FILES && ($fail == "")) 	{

				//the code below defines the functions needed to validate the file name
				echo<<<_END
				<style>
					.signup{
						border:1px solid #999999;
					font: normal 14px helvetica; color:#444444;
					}
				</style>

				<script>
					function validate(form){
						fail = validateFilename(form.fn.value)

						if(fail == "") {return true}
						else {alert(fail); return false}
					}

					function validateFilename(field){
						if(field == "") return "No file name entered\n"
						else if(field.length < 5)
							return "File names must be at least 5 characters. \n"
						else if(/[^a-zA-Z0-9]/.test(field))
							return "Only a-z, A-Z, 0-9 allowed in file names\n"
						return ""
					}

					</script>
				</head>
				<body>
				</html>
_END;

				//store file contents in variable, take first 20 bytes as signature
  			$temp_str = file_get_contents($_FILES['filename']['tmp_name']);
  			$signature = substr($temp_str, 0, 20); //store first 20 bytes in substr variable
  			echo "<br> Signature was uploaded. <br>"; //show admin the signature that was uploaded

				//sanitize and encrypt signature and filename before passing it to addsignature
				$conn = new mysqli($hn, $un, $pw, $db);
				if ($conn->connect_error) die($conn->connect_error);
				$signature = mysql_entities_fix_string($conn,$signature);
				$fn = mysql_entities_fix_string($conn,$fn);
				$conn->close();

				//need to add filename and signature to the badfiles table if it is not currently there
				addsignature($fn, $signature,$hn,$un,$pw,$db, $table);
			}
		}
		else{echo"<br> One or more fields was left blank. <br>";}
	}
}

else {header('location:login.php');}

?>
