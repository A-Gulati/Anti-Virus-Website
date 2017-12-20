<?php

/*
Note: Login.php gives admin access to the admin.php page from where the admin can add known infected file signatures to the database.
When testing this code, I had a database called 'login_test' which contained two tables - one called 'badfiles' which has
all of the signatures against which we check files that a user uploads in 'scan.php'. To test the functionality of 'Login.php'
I created a table 'users' which contains fields username, password, and id. The username is 'admin' and the password was the word 'pass'
encrypted with md5 and salted as per the myhash function you can see in 'functions.php'.
*/

require_once('functions.php'); //functions stored here

/*The below section creates a form that allows the user to upload a text file*/
	echo <<<_END
	<html>
		<head>
			<title>Login to admin</title>
		</head>
		<body>
			<table border="0" cellpadding="2" cellspacing="5" bgcolor="#00FF7F" align="center">
			<th colspan="2" align="center">Admin Access - Infected File Upload</th>
			<form action="login.php" method="post">
				<tr><td>Admin Login</td>
				<tr><td>
					<label for="username">Username: </label>
					<input type="text" name="username" id="username" value="">
				</td></tr>
				<tr><td>
					<label for="password">Password: </label>
					<input type="password" name="password" id="password" value=""></td></tr>
				<tr><td>
					<input type="submit" value="Submit" name = "submit">
				</td></tr>
				<tr><td>
					<input type="submit" value="Return to virus scan page" name = "return" >
				</td></tr>
		</form>
		</table>
	</body>
	</html>
_END;


// create a badfiles table if it does not currently exist
//it will store the signatures of files uploaded by an admin
$initquery = "CREATE TABLE IF NOT EXISTS $table (signature VARCHAR(200) NOT NULL, id int(100) NOT NULL AUTO_INCREMENT, UNIQUE (signature),UNIQUE KEY(id))ENGINE MyISAM";
$discard = myq($initquery,$hn,$un,$pw,$db);


if(isset($_POST['submit'])){

	if(!isset($_POST['username']) || $_POST['username'] == '' || !isset($_POST['password']) || $_POST['password'] == ''){
		echo "<br> Your login credentials are invalid. Please try again. <br>";
	}

	else{
		//create connection to sanitize un and pw before checked in database
		$conn = new mysqli($hn, $un, $pw, $db);
		if ($conn->connect_error) die($conn->connect_error);
		$username = mysql_entities_fix_string($conn,$_POST['username']);
		$password = mysql_entities_fix_string($conn,$_POST['password']);
		$conn->close();

		//salt and hash the password
		$password = myhash($password);
		echo "<br>$password<br>";

		//check creds, if they were correct, create session login flag
		if(credcheck($username, $password,$hn,$un,$pw,$db)){
			$_SESSION['login_flag'] = true; //flag to remember login
			header("location: admin.php"); //if this is an admin, admin will allow them to add their file to the badfiles table
		}

		else{echo"<br> Invalid Credentials. Admin Access Denied. <br>";}
	}
}

if(isset($_POST['return'])){header('location:scan.php');}



?>
