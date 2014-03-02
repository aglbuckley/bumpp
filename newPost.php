<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="css/foundation.css" />
    	<script src="js/vendor/modernizr.js"></script>
    	<script src="tinymce/js/tinymce/tinymce.min.js"></script>
		<script>
        	tinymce.init({selector:'textarea'});
		</script>

	</head>
	<body>
	
		<?php
			session_start();
			if(!isset($_SESSION['email']) || !isset($_SESSION['user_id']) || !isset($_SESSION['fname']))
			{
				header('Location: login.html');
				exit();
			}
		?>
	
		<?php
			include('topBar.php');
		?>
	
		<section role="main">
			<div class="row">
				<div id="blogHeader">
					<h1 id="blogHeader">
					<?php
						if(!isset($_SESSION['blog_name']) || $_SESSION['blog_name']==''){
							$blogName = "";					
							$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

							if ($mysqli->connect_errno) {
								echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
							}

							/* create a prepared statement */
							if ($stmt = $mysqli->prepare("SELECT name FROM blog WHERE user_id=?")) {
						
								if(!$stmt->bind_param("s", $_SESSION['user_id']))
								{
									echo '<h1>Error on select bind</h1>';
									exit();
			
								} else {
									if($stmt->execute()){
										$stmt->bind_result($blogName);
			
									} else {
										echo '<h1>Error on execute</h1>';
										exit();
									}
		
									$stmt->fetch();
				
									$stmt->close();
								}
							}
							$_SESSION['blog_name'] = stripslashes($blogName);
						}
						echo $_SESSION['blog_name'];

						//$mysqli->close();
					?></h1>
					<small><a href="#">Rename Blog</a></small>
				</div>
				<br></br>
				<form name="input" action="submitPost.php" method="post">
					<input type="text" name="post_name" placeholder="New Post">
					<textarea name="post_content"></textarea>
					</br>
					<button type="submit">Post!</button>
				</form>
			</div>
		</section>
		
		<script src="js/vendor/jquery.js"></script>
    	<script src="js/foundation.min.js"></script>
    	<script>
			$(document).foundation();
   		</script> 
	</body>
</html>