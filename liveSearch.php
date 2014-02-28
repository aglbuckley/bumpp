<?php

try{

	$input = $_GET['input'];
	$response = "";
	
	$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		throw new Exception('Failed to connect');
	}
	echo 'hello';
	if($stmt = $mysqli->prepare("SELECT first_name, last_name FROM user WHERE first_name LIKE ? OR last_name LIKE ?"))
	{
		echo 'hello1';
		if(!$stmt->bind_param("ss", $input.'%', $input.'%'))
		{
			$response = 'error';
		} else {
			
			if($stmt->execute())
			{
				$fname = "";
				$lname = "";
				if(!$stmt->bind_result($fname, $lname))
				{
					$reponse = 'error';
				}
			} else {
				$response = 'error';
			}
			
			$stmt->store_result();
			if(mysqli_stmt_num_rows($stmt)<1)
			{
				$response = '<li><a href="#">Nada</a></li>';
			}
			
			$i=0;
			while($stmt->fetch())
			{
				$response = $response.'<li><a href="#">'.$fname.' '.$lname.'</a></li>';
				$i++;
			}
			
			$stmt->close();
		}
		echo $response;
	}

	$mysqli->close();
} catch(Exception $e) {
	echo '<h1>Uh oh!</h1>';
	echo '<h3 class="subheader">Something went wrong. Please reload the page.</h3>';
	echo $e;
	$mysqli->close();
	//exit();
}

?>