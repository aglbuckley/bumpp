<?php
 //ini_set('display_errors', 'On');
ob_start();
$fname = "";
$lname = "";
$email = "";
$password = "";
$serverPasswordHash = "";
$salt = "";
$user_id = -1;
$result = -1;

if(isset($_POST['email']) && isset($_POST['password']))
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
if ($stmt = $mysqli->prepare("SELECT first_name, last_name, email, password, salt, user_id FROM user WHERE email=?")) {

	if(!$stmt->bind_param("s", $email))
	{
		echo "Binding failed: (".$stmt->errno.")".$stmt->error;
		exit();
	}
	
    if(!$stmt->execute()){
    	echo "Execute failed: (".$stmt->errno.")".$stmt->error;
    	exit();
    }

    $stmt->bind_result($fname, $lname, $email, $serverPasswordHash, $salt, $user_id);

    $result = $stmt->fetch();
        
    if ($result==null){
    	echo '<h1>Login Failed!</h1><br><form action="login.html">
    		<input type="submit" value="Login to Var!">
		</form>';
		exit();
    }

	$clientHash = hash('sha512', $password.$salt);
	
	if($clientHash == $serverPasswordHash)
	{
		session_start();
		$_SESSION['user_id'] = $user_id;
		$_SESSION['email'] = $email;
		$_SESSION['fname'] = $fname;
		$_SESSION['lname'] = $lname;
		echo '<h1>Success!</h1><br>';
		header('Location: index.php');
		exit();
	} else {
		header('Location: login.html');
	}
    $stmt->close();
}

$mysqli->close();

?>