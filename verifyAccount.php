<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="css/foundation.css" />
    	<script src="js/vendor/modernizr.js"></script>
	</head>
	
	<body>
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
							<li><a href="login.html">Login</a></li> 
						</ul> 
					</li> 
				</ul> 
			
				<!-- Left Nav Section --> 
				<ul class="left"> 
					<li><a href="login.html">Login</a></li>
				</ul> 
			</section> 
		</nav>
		<section role="main">
			<div class="row">
				<?php
				require_once("BumppUtility.php");
				if(isset($_GET['email']) && isset($_GET['verification']) && !empty($_GET['email']) && !empty($_GET['verification']))
				{
					$user_id = -1;
					$username = "";
					$email = $_GET['email'];
					$verification = $_GET['verification'];
					//$mysqli = new mysqli("localhost", "root", "root", "main_test_db");
					$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

					if ($mysqli->connect_errno) {
						echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
						exit();
					}
					
					$stmt = $mysqli->prepare("SELECT user_id, username FROM user WHERE email = ? AND verification = ?");
					//echo $mysqli->real_escape_string($email).' '.$mysqli->real_escape_string($verification);
					
					if(!$stmt->bind_param("ss", $mysqli->real_escape_string($email), $mysqli->real_escape_string($verification)))
					{
						echo "Binding failed: (".$stms->errno.") ".$stmt->error;
						exit();
					}
					
					if($stmt->execute())
					{
						if(!$stmt->bind_result($user_id, $username))
						{
							echo "Error binding";
							exit();
						}
						$stmt->store_result();
						$stmt->fetch();
						if(mysqli_stmt_num_rows($stmt)==1)
						{
							$stmt = $mysqli->prepare("UPDATE user SET verified = ? WHERE user_id = ?");
							$verified = 1;
							if(!$stmt->bind_param("ii", $verified, $user_id))
							{
								echo "Second binding failed";
								exit();
							}
							
							if($stmt->execute() && $user_id>0)
							{
								echo '<h1>Congratulations</h1>';
								echo '<h2 class="subheader">You may now login</h2>';
								$location = 'users/'.$user_id;
								if(!mkdir($location, 0700, true))
								{
									die('failed to create');
								}
								exit();
							} else {
								echo '<h1>We could not verify your account. Sorry.</h1>';
								//header('Location: ./');
								exit();
							}
						} else {
							echo '<h1>We could not verify your account. Sorry.</h1>';
							//header('Location: ./');
							exit();
						}
					}
				} else {
					echo '<h1>We could not verify your account. Sorry.</h1>';
					header('Location: ./');
					exit();
				}
				?>
				
			</div>
		</section>

		<script src="js/vendor/jquery.js"></script>
    	<script src="js/foundation.min.js"></script>
    	<script src="js/foundation/foundation.abide.js"></script>
    	<script>
    	  $(document).foundation();
   		</script> 
	</body>
</html>