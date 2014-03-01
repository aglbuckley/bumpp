<?php
 //ini_set('display_errors', 'On');
ob_start();
$fname = "";
$lname = "";
$email = "";
$password = "";
$serverPasswordHash = "";
$salt = "";
$verified = 0;
$user_id = -1;
$result = -1;
$profileImageID = 1;

if(isset($_POST['email']) && isset($_POST['password']) && !empty($_POST['email']) && !empty($_POST['email']))
{
	$email = $_POST['email'];
	$password = $_POST['password'];
} else {
	header('Location: login.html');
	//exit();
}

//$mysqli = new mysqli("localhost", "root", "root", "main_test_db");
$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

/* create a prepared statement */
if ($stmt = $mysqli->prepare("SELECT first_name, last_name, email, password, salt, profile_image_id, user_id, verified FROM user WHERE email=?")) {

	if(!$stmt->bind_param("s", $mysqli->real_escape_string($email)))
	{
		echo "Binding failed: (".$stmt->errno.")".$stmt->error;
		exit();
	}
	
    if(!$stmt->execute()){
    	echo "Execute failed: (".$stmt->errno.")".$stmt->error;
    	exit();
    }

    $stmt->bind_result($fname, $lname, $email, $serverPasswordHash, $salt, $profileImageID, $user_id, $verified);

    $result = $stmt->fetch();
        
    if ($result==null){
    	echo '<h1>Login Failed!</h1><br><form action="login.html">
    		<input type="submit" value="Login to Var!">
		</form>';
		exit();
    }
    
    if ($verified == 0)
    {
    	echo '<h1>Please verify your account before logging in.</h1';
    	exit();
    }

	$clientHash = hash('sha512', $password.$salt);
	
	if($clientHash == $serverPasswordHash)
	{
		session_start();
		$_SESSION['mysqli'] = $mysqli;
		$_SESSION['user_id'] = $user_id;
		$_SESSION['email'] = $email;
		$_SESSION['fname'] = $fname;
		$_SESSION['lname'] = $lname;
		$_SESSION['profile_image_id'] = $profileImageID;
		echo '<h1>Success!</h1><br>';
		header('Location: ./');
		exit();
	} else {
		header('Location: login.html');
	}
    $stmt->close();
}

$mysqli->close();

?>