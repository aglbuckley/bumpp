<?php

session_start();

if(!isset($_SESSION['user_id']) || empty($_SESSION['user_id']) || !isset($_POST['post_name']) || empty($_POST['post_name']) || !isset($_POST['post_content']) || empty($_POST['post_content']))
{
	header('Location: newPost.php');
	exit();
}

$postName = $_POST['post_name'];
$postContent = $_POST['post_content'];
$blog_id = -1;

//$mysqli = new mysqli("localhost", "root", "root", "main_test_db");
$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

//session_start();
if(!isset($_SESSION['blog_id']) || empty($_SESSION['blog_id']) || $_SESSION['blog_id'] <0)
{
	if ($stmt = $mysqli->prepare("SELECT blog_id FROM blog WHERE user_id=?")) {
		if(!$stmt->bind_param("s", $_SESSION['user_id']))
		{
			echo '<h1>Error on select bind</h1>';
			exit();

		} else {
			if($stmt->execute()){
				$stmt->bind_result($blog_id);

			} else {
				echo '<h1>Error on execute</h1>';
				exit();
			}

			$stmt->fetch();
		
			$_SESSION['blog_id'] = $blog_id;

			$stmt->close();
		}
	}
}

$stmt = $mysqli->prepare("INSERT INTO blogpost (blog_id, name, content) VALUES (?, ?, ?)");

if(!$stmt->bind_param("sss", $mysqli->real_escape_string($_SESSION['blog_id']), $mysqli->real_escape_string($postName), $mysqli->real_escape_string($postContent)))
{
	echo "Binding failed: (".$stmt->errno.") ".$stmt->error;
}

if ($stmt->execute()) {
	header('Location: index.php');
	exit();
} else {
   echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}
$mysqli->close();
?>