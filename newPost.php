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
	
		<nav class="top-bar" data-topbar> 
			<ul class="title-area"> 
				<li class="name"> 
					<h1><a href="./">bumpp</a></h1> 
				</li> 
				<li class="toggle-topbar menu-icon"><a href="#">Menu</a></li> 
			</ul> 
		
			<section class="top-bar-section"> 
				<!-- Right Nav Section --> 
				<ul class="right"> 
					<li class="has-dropdown"> 
						<a href="#">Settings</a> 
						<ul class="dropdown"> 
							<li><a href="logout.php">Logout</a></li> 
						</ul> 
					</li>
					<li class="divider"></li>
					<li id="newPostNav" class="has-form"> 
						<a href="newPost.php" class="button">New Story</a> 
					</li>
				</ul>
			
				<!-- Left Nav Section --> 
				<ul class="left"> 
					<li class="divider"></li>
					<?php
						echo '<li><a href="#">'.$_SESSION['fname'].'</a></li>';
					?>
					<li class="divider"></li>
					<li class="has-form"> 
						<div class="row collapse"> 
							<div class="large-8 small-9 columns"> 
								<input type="text" placeholder="Search for Friends"> 
							</div> 
							<div class="large-4 small-3 columns"> 
								<a href="#" class="button">Search</a> 
							</div> 
						</div> 
					</li>
				</ul> 
			</section> 
		</nav>
	
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