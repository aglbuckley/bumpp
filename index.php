<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="css/foundation.css" />
    	<script src="js/vendor/modernizr.js"></script>
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
						<a href="#" class="button">New Story</a> 
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
	
		 <?php 
		 	session_start();
		 	if(!isset($_SESSION['email']) || !isset($_SESSION['user_id']) || !isset($_SESSION['fname']))
		 	{
		 		header('Location: login.html');
		 		exit();
		 	}
		 	
		 	$fname = $_SESSION['fname'];
		 	$lname = $_SESSION['lname'];
		 	
		 	echo '<div id="welcomeModal" class="reveal-modal" data-reveal> 
			<h1>Welcome to bumpp, '.$fname.'!</h1> 
			<p>More info coming soon...</p> 
			<a class="close-reveal-modal">&#215;</a> 
			</div>';
		 	
		 ?>
		<section role="main">
			<div class="row">
				<h1 id="blogHeader"><?php echo $_SESSION['fname']."'s Blog"; ?></h1>
				<h3 class="subheader"> It's really easy to customize your very own blog!</h3>
				<hr></hr>
				<ul class="example-orbit" data-orbit> 
					<li> 
						<img src="images/london_sm.jpg" alt="slide 1" /> 
						<div class="orbit-caption"> 
							London. 
						</div> 
					</li> 
					<li> 
						<img src="images/lake_sm.jpg" alt="slide 2" /> 
						<div class="orbit-caption"> 
							Lake. 
						</div> 
					</li> 
					<li> 
						<img src="images/mountain_sm.jpg" alt="slide 3" /> 
						<div class="orbit-caption"> 
							Mountain. 
						</div> 
					</li>
				</ul>
			</div>
		</section>
		
		<!-- Joyride stuff -->
		<ol class="joyride-list" data-joyride>
  			<li data-id="blogHeader" data-button="End" data-options="tip_location:top">
    			<h4>Stop #1</h4>
    			<p>You can control all the details for you tour stop. Any valid HTML will work inside of Joyride.</p>
  			</li>
		</ol>
		
		<script src="js/vendor/jquery.js"></script>
    	<script src="js/foundation.min.js"></script>
    	<script src="js/foundation/foundation.joyride.js"></script>
    	<script src="js/vendor/jquery.cookie.js"></script>
    	<script src="js/foundation/foundation.reveal.js"></script>
    	<script>
			$(document).foundation();
			if ($('[data-joyride]')) {
				$(document).foundation('joyride', 'start');
			} 
    	  $( document ).ready(function() {
    	  
    	  	$('#welcomeModal').foundation('reveal', 'open');
    	  
			});
   		</script> 
	</body>
</html>