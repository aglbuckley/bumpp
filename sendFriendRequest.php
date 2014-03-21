<?php
	session_start();
	require_once("BumppUtility.php");
	$friendID = $_GET['friendID'];
	$count = -1;
	$accepted = 0;
	$acceptedTrue = 1;
	$invited = 0;
	
	$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	
	//check if current user has already sent a request
	if($stmt = $mysqli->prepare("SELECT COUNT(*) FROM friendship WHERE friender_id = ? AND friendee_id = ? AND accepted = ?"))
	{
		if(!$stmt->bind_param("iii", $_SESSION['user_id'], $friendID, $accepted))
		{
			echo '<h1>Error on select bind</h1>';
			$mysqli->close();
			exit();
		} else {
			if($stmt->execute()){
				$stmt->bind_result($count);
			} else {
				echo '<h1>Error on execute</h1>';
				$mysqli->close();
				exit();
			}
			$stmt->fetch();
			$stmt->close();
			if($count>0){
				echo 'Friend Request Sent';
				$mysqli->close();
				exit();
			}
		}
	}
	
	//check if other user has sent you a request
	if($stmt = $mysqli->prepare("SELECT COUNT(*) FROM friendship WHERE friender_id = ? AND friendee_id = ? AND accpted = ?"))
	{
		if(!$stmt->bind_param("iii", $friendID, $_SESSION['user_id'], $accepted))
		{
			echo '<h1>Error on select bind</h1>';
			$mysqli->close();
			exit();
		} else {
			if($stmt->execute()){
				$stmt->bind_result($count);
			} else {
				echo '<h1>Error on execute</h1>';
				$mysqli->close();
				exit();
			}
			$stmt->fetch();
			$stmt->close();
			if($count>0){
				echo 'Check Your Friend Requests';
				$mysqli->close();
				exit();
			}
		}
	}
	
	//Check if the two are already friends
	if($stmt = $mysqli->prepare("SELECT COUNT(*) FROM friendship WHERE (friender_id = ? AND friendee_id = ?) OR (friender_id = ? AND friendee_id = ?) AND accepted = ?"))
	{
		if(!$stmt->bind_param("iiiii", $friendID, $_SESSION['user_id'], $_SESSION['user_id'], $friendID, $acceptedTrue))
		{
			echo '<h1>Error on select bind</h1>';
			$mysqli->close();
			exit();
		} else {
			if($stmt->execute()){
				$stmt->bind_result($count);
			} else {
				echo '<h1>Error on execute</h1>';
				$mysqli->close();
				exit();
			}
			$stmt->fetch();
			$stmt->close();
			if($count>0){
				echo 'Friends';
				$mysqli->close();
				exit();
			}
		}
	}
	
	//If you've made it here, the two aren't associated yet
	$stmt = $mysqli->prepare("INSERT INTO friendship (friender_id, friendee_id, invited, accepted) VALUES (?, ?, ?, ?)");
	
	if(!$stmt->bind_param("iiii", $_SESSION['user_id'], $friendID, $invited, $accepted))
	{
		echo "Binding failed: (".$stmt->errno.") ".$stmt->error;
		$mysqli->close();
		exit();
	}

	if ($stmt->execute()) {

		echo "Friend Request Sent";
        BumppUtility::Log("sent friend request to ".$friendID);
		$mysqli->close();
		exit();
	}
	$mysqli->close();
	
?>