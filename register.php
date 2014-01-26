<?php

$fname = "";
$lname = "";
$email = "";
$password = "";

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

$mysqli = new mysqli("localhost", "root", "root", "main_test_db");
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
   echo '<h1>Success!</h1><br><form action="login.html">
    		<input type="submit" value="Login to Var!">
		</form>';
} else {
   echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}
session_destroy();
?>