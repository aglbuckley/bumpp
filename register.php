<?php

$fname = "";
$lname = "";
$email = "";
$password = "";
$user_id=-1;

//Eventually check to see if the user is already registered.

if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['password']))
{
	$fname = $_POST['first_name'];
	$lname = $_POST['last_name'];
	$email = $_POST['email'];
	$password = $_POST['password'];
} else {
	header('Location: register.html');
	exit();
}

//$mysqli = new mysqli("localhost", "root", "root", "main_test_db");
$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

session_start();
$salt = uniqid(mt_rand(), true);
$salt = substr($salt,0,23);
$hash = hash('sha512', $password.$salt);

$stmt = $mysqli->prepare("INSERT INTO user (first_name, last_name, email, password, salt) VALUES (?, ?, ?, ?, ?)");

if(!$stmt->bind_param("sssss", $fname, $lname, $email, $hash, $salt))
{
	echo "Binding failed: (".$stmt->errno.") ".$stmt->error;
}

if ($stmt->execute()) {
			
	$stmt = $mysqli->prepare("SELECT user_id FROM user WHERE email = ? AND salt = ?");
		
	if(!$stmt->bind_param("ss", $email, $salt))
	{
		echo '<h1>Error on second select bind</h1>';
		exit();
			
	} else {
		if($stmt->execute()){
			$stmt->bind_result($user_id);
			
		} else {
			echo '<h1>Error on execute</h1>';
			exit();
		}
		
		$stmt->fetch();
				
		$stmt->close();
				
		$stmt = $mysqli->prepare("INSERT INTO blog (name, user_id) VALUES (?, ?)");	
		
		$blog_name = mysql_real_escape_string($fname.'\'s Blog');
			
		if(!$stmt->bind_param("ss", $blog_name, $user_id))
		{
			echo '<h1>Error on insert bind</h1>';
			exit();
		} else {
			if(!$stmt->execute()){
				echo '<h1>Error on execution of insert</h1>';
			}
		}
	}
	
	echo '<h1>Success!</h1><br><form action="login.html">
    		<input type="submit" value="Login to Var!">
		</form>';
} else {
   echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}
session_destroy();
?>