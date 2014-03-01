<?php
	if(!isset($_SESSION['profile_image_id']) || empty($_SESSION['profile_image_id']) || $_SESSION['profile_image_id']==null)
	{
		$_SESSION['profile_image_id'] = 1;
	}
	if(!isset($_SESSION['profile_image_location']) || empty($_SESSION['profile_image_location']) || $_SESSION['profile_image_location']==null)
	{
		$imageID = $_SESSION['profile_image_id'];
		$imageString = "";

		$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

		if ($mysqli->connect_errno) {
			echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			exit();
		}

		/* create a prepared statement */
		if ($stmt = $mysqli->prepare("SELECT image FROM photo WHERE photo_id=?")) {

			if(!$stmt->bind_param("i", $imageID))
			{
				echo "Binding failed: (".$stmt->errno.")".$stmt->error;
				exit();
			}
	
			if(!$stmt->execute()){
				echo "Execute failed: (".$stmt->errno.")".$stmt->error;
				exit();
			}

			$stmt->bind_result($imageString);

			$result = $stmt->fetch();
		
			if ($result==null){
				echo 'No Image!';
				exit();
			}

			$_SESSION['profile_image_location'] = $imageString;
			$stmt->close();
		}
		$mysqli->close();
	}
	
?>