<?php

try{

	$input = $_GET['input'];
	$response = "";
	
	$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		throw new Exception('Failed to connect');
	}
	if($stmt = $mysqli->prepare("SELECT first_name, last_name FROM user WHERE first_name LIKE CONCAT('%', ?) OR last_name LIKE CONCAT('%', ?)"))
	{
		if(!$stmt->bind_param("ss", $input, $input))
		{	
			echo 'hello1';
			$response = 'error';
			exit();
		} else {
			echo 'hello2';
			if($stmt->execute())
			{
				echo 'hello3';
				$fname = "";
				$lname = "";
				if(!$stmt->bind_result($fname, $lname))
				{
					echo 'hello4';
					$reponse = 'error';
					exit();
				}
			} else {
				echo 'hello5';
				$response = 'error';
			}
			
			echo 'hello6';
			$stmt->store_result();
			if(mysqli_stmt_num_rows($stmt)<1)
			{
				echo 'hello7';
				$response = '<li><a href="#">Nada</a></li>';
				exit();
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
	} else {
		echo 'prepare error';
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