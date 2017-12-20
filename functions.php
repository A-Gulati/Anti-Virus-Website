<?php

session_start(); //start session at the beginning of the code since functions.php is required in all the other files

/*initialize variables that need to have a broad scope*/
$hn = "localhost";
$un = "root";
$pw = "";
$db = "login_test";	//login_test is the database name
$table = "badfiles"; //name of the table that contains all the signatures


/*opens and closes the connection, sends the db the query, returns result*/
function myq($query,$hn,$un,$pw,$db){

	$conn = new mysqli($hn, $un, $pw, $db);
	if ($conn->connect_error) die($conn->connect_error);
	$result = $conn->query($query);/*
	if (!$result) echo "Your query failed: $query<br>" .
		$conn->error . "<br><br>";*/

	//added this else statement to return the result of the query
	if($result){return $result;}

	$conn->close();
}

/*sanitize inputs*/
function mysql_entities_fix_string($conn, $string)
{
	return htmlentities(mysql_fix_string($conn, $string));
}

/*sanitize inputs helper function*/
function mysql_fix_string($conn, $string)
{
	if (get_magic_quotes_gpc()) $string = stripslashes($string);
	return $conn->real_escape_string($string);
}


/*first select the database, then retrieve the row that belongs to the user, return true if the passwords match, else false */
function credcheck($username, $password,$hn,$un,$pw,$db){

  /*select a database*/
  myq("USE $db",$hn,$un,$pw,$db);

  $query = "SELECT * from users WHERE username = '$username'";
  $u_row = myq($query,$hn,$un,$pw,$db);
  $temp = mysqli_fetch_assoc($u_row); /*returns an associative array of the result row*/

  /*if the admin user exists and the correct password was entered, return true, else return false*/
  if(is_object($u_row) && $password == $temp["password"]){
    //echo "<br> true <br>";
		return true;
  }

  else{
	   //echo "<br> false <br>";
	   return false;
  }
}

//auth checks to ensure that the login_flag is set - this means the user has entered the right credentials to gain admin access
function auth($hn,$un,$pw,$db) {
	if($_SESSION['login_flag']){return true;}
	else{return false;}
}

//myhash gets the raw string as input and salts it, then computed md5 hash
function myhash($string){
	$salt1="#kA1%^";
	$salt2="*j[+Vu";
	$saltedpass= $salt1.$string.$salt2;

	return md5($saltedpass);
}

/*create a query to add a signature to badfiles table, then send query to myq function*/
function addsignature($filename,$signature,$hn,$un,$pw,$db,$table){
  //insert signature into the badfiles table
  $query = "INSERT INTO $table (signature,filename) VALUES('$signature','$filename')";
  $discard = myq($query,$hn,$un,$pw,$db); //store result from myq function in an un-used variable
}

//checks if the signature is present in the contents of the file
function checkforsignature($filecontents, $signature){
	//the first if statement makes sure that signature is not an empty string
if(isset($signature) && $signature!=""){
		if(strpos($filecontents, $signature) !== false){
    	return true; //true means that the file does contain the signature, so the file is infected
		}
  	else{
    	return false; //false means the file doesn't contain the signature, so the file is not infected
		}
	}
}

//ensures the file name uses english chars or numbers only
function validate_filename($field){
	if($field == "") {return "No file name entered<br>";}
	else if(strlen($field) < 5) {return "File name must be at least 5 characters<br>";}
	else if(preg_match("/[^a-zA-Z0-9]/", $field)) {return "Only english letters or numbers in file name <br>";}
	return "";
}

//sanitize string before it is used as a file name
function fix_string($string){
	if(get_magic_quotes_gpc()){$string = stripslashes($string);}
		return htmlentities($string);
}

/*logout, destroy cookies and session, go back to login page*/
function logout(){
	//explicitly destroy the authentication flag
	$_SESSION['login_flag'] = false;

	/*destroy the session*/
	session_start();
	session_destroy();

	/*go back to the login page*/
	header("location:login.php");
}

?>
