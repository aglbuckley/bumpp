<?php

	try{
		session_start();
		$id = $_SESSION['user_id'];
		$accepted = 0;
		$response = "";
		$count = -1;
	
		$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

		if ($mysqli->connect_errno) {
			echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			throw new Exception('Failed to connect');
		}
		if($stmt = $mysqli->prepare("SELECT COUNT(*) FROM friendship WHERE friendee_id = ? AND accepted = ?"))
		{
			if(!$stmt->bind_param("ii", $id, $accepted))
			{	
				$response = 'error';
				exit();
			} else {
				if($stmt->execute())
				{
					if(!$stmt->bind_result($count))
					{
						$reponse = 'error';
						exit();
					}
				} else {
					$response = 'error';
				}
			
				//$stmt->store_result();
				$stmt->fetch();
				
				$response = 'Friend Requests ('.$count.')';
			
				$stmt->close();
			}
			echo $response;
		} else {
			echo 'prepare error';
			exit();
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