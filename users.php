<?php
	session_start();
	if(!isset($_SESSION['email']) || !isset($_SESSION['user_id']) || !isset($_SESSION['fname']))
	{
		header('Location: login.html');
		exit();
	}
	
	//code to set friend blog info of the current page you are visiting
	$friendUserID = -1;
	$fBlogName = "";	
	$fBlog_id = -1;				
	$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	/* create a prepared statement */
	if($stmt = $mysqli->prepare("SELECT user_id FROM users WHERE username=?"))
	{
		if(!$stmt->bind_param("s", $_GET['username']))
		{
			echo '<h1>Error on select bind</h1>';
			exit();
		} else {
			if($stmt->execute()){
				$stmt->bind_result($friendUserID);
			} else {
				echo '<h1>Error on execute</h1>';
				exit();
			}
			$stmt->fetch();
			$stmt->close();
		}
	}
	if ($stmt = $mysqli->prepare("SELECT name, blog_id FROM blog WHERE user_id=?")) {

		if(!$stmt->bind_param("i", $friendUserID))
		{
			echo '<h1>Error on select bind</h1>';
			exit();

		} else {
			if($stmt->execute()){
				$stmt->bind_result($fBlogName, $fBlog_id);

			} else {
				echo '<h1>Error on execute</h1>';
				exit();
			}

			$stmt->fetch();

			$stmt->close();
		}
	}
	$_SESSION['friend_blog_name'] = stripslashes($fBlogName);
	$_SESSION['friend_blog_id'] = $fBlog_id;
?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="/css/foundation.css" />
    	<script src="/js/vendor/modernizr.js"></script>
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
				<div id="blogHeaderDiv">
					<hr>
					<h1 id="blogHeader"><?php
						if(!isset($fBlogName) || $fBlogName==''){
							$friendUserID = -1;
							$fBlogName = "";	
							$fBlog_id = -1;				
							$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

							if ($mysqli->connect_errno) {
								echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
							}

							/* create a prepared statement */
							if($stmt = $mysqli->prepare("SELECT user_id FROM users WHERE username=?"))
							{
								if(!$stmt->bind_param("s", $_GET['username']))
								{
									echo '<h1>Error on select bind</h1>';
									exit();
								} else {
									if($stmt->execute()){
										$stmt->bind_result($friendUserID);
									} else {
										echo '<h1>Error on execute</h1>';
										exit();
									}
									$stmt->fetch();
									$stmt->close();
								}
							}
							if ($stmt = $mysqli->prepare("SELECT name, blog_id FROM blog WHERE user_id=?")) {

								if(!$stmt->bind_param("i", $friendUserID))
								{
									echo '<h1>Error on select bind</h1>';
									exit();

								} else {
									if($stmt->execute()){
										$stmt->bind_result($fBlogName, $fBlog_id);

									} else {
										echo '<h1>Error on execute</h1>';
										exit();
									}

									$stmt->fetch();

									$stmt->close();
								}
							}
							$_SESSION['friend_blog_name'] = stripslashes($fBlogName);
							$_SESSION['friend_blog_id'] = $fBlog_id;
						}
						echo $fBlogName;
					?></h1>
					<small><a href="javascript:blogNameUpdate()" id="renameBlog">Rename Blog</a></small>
				</div>
				<h3 class="subheader"> It's really easy to customize your very own blog!</h3>
				<hr>
				
				<!--Orbit Stuff-->
				
				<ul class="example-orbit" data-orbit> 
					<li> 
						<img src="images/london1.jpg" alt="slide 1" /> 
						<div class="orbit-caption"> 
							London. 
						</div> 
					</li> 
					<!--<li> 
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
					</li>-->
				</ul>
				
				<!--End Orbit Stuff -->
				
				<hr>
				
				<?php
					//error_reporting(E_ALL);
					//ini_set('display_errors',1);
					ini_set('memory_limit', '-1');
					try{
						$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

						if ($mysqli->connect_errno) {
							//echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
							throw new Exception('Failed to connect');
						}
					
						if(!isset($fBlog_id) || empty($fBlog_id) || $fBlog_id <0)
						{
							$blog_id = -1;
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
		
									$fBlog_id = $blog_id;

									$stmt->close();
								}
							}
						}

						if ($stmt = $mysqli->prepare("SELECT name, content, timestamp FROM blogpost WHERE blog_id=? ORDER BY timestamp DESC")) {
						
							$postName = "";
							$postContent = "";
							$timestamp = 0;

							if(!$stmt->bind_param("i", $fBlog_id))
							{
								echo '<h1>Error on select bind</h1>';
								exit();
		
							} else {
								if($stmt->execute()){

									if($stmt->bind_result($postName, $postContent, $timestamp))
									{
									} else {
										exit();
									}

								} else {
									echo '<h1>Error on execute</h1>';
									exit();
								}
								$stmt->store_result();
								if(mysqli_stmt_num_rows($stmt)<1)
								{
									echo '<h3 class="subheader">You have no blog entries yet. Click the new story button above to make your first one!</h3>';
								}
								
								$i = 0;
								while($stmt->fetch())
								{
									$timestamp = strtotime($timestamp);
									$timestamp = date("l d F Y \a\\t h:i a", $timestamp);
									echo '<h3 class="subheader">'.$postName.'</h3>';
									echo '<h5>Posted: '.$timestamp.'</h5><br>';
									echo stripslashes(str_replace("\\r\\n",'',$postContent))."\n";
									echo '<hr>'."\n".'<div id="postActionsDiv'.$i.'">'."\n";
									echo "\t".'<p><a href="javascript:commentReveal('.$i.')">Comment (0)</a>&nbsp;&nbsp; | &nbsp;&nbsp;<a href="#">bumpp up</a>&nbsp;&nbsp; | &nbsp;&nbsp;<a href="#">bumpp down</a></p>';
									echo '</div>'."\n".'<hr><hr>';
									$i++;
								}
			
								$stmt->close();
							}
						} else {
							throw new Exception('Error on Prepare');
						}
						$mysqli->close();
					} catch(Exception $e) {
						echo '<h1>Uh oh!</h1>';
						echo '<h3 class="subheader">Something went wrong. Please reload the page.</h3>';
						$mysqli->close();
						//exit();
					}
					
				?>
				
			</div>
		</section>
		
		<script src="/js/vendor/jquery.js"></script>
    	<script src="/js/foundation.min.js"></script>
    	<script src="/js/foundation/foundation.reveal.js"></script>
    	<script>
			$(document).foundation();
			
    	  	$(document).ready(function() {
    	  
    	  	$('#welcomeModal').foundation('reveal', 'open');
    	  
			});
			
			function commentReveal(index)
			{
				var postActionsDiv = document.getElementById('postActionsDiv'+index);
				postActionsDiv.parentNode.removeChild(postActionsDiv);
			}
			
			function blogNameUpdate()
			{
				var changeNameForm = document.createElement('form');
				changeNameForm.setAttribute('name', 'input');
				changeNameForm.setAttribute('action', 'changeName.php');
				changeNameForm.setAttribute('method', 'post');
				var blogHeaderContainer = document.getElementById('blogHeaderDiv');

				var blogHeaderSubContainer = document.createElement('div');
				blogHeaderSubContainer.setAttribute('class', 'row collapse');
				var blogHeaderInputContainer = document.createElement('div');
				blogHeaderInputContainer.setAttribute('class', 'small-10 columns');
				var blogHeaderButtonContainer = document.createElement('div');
				blogHeaderButtonContainer.setAttribute('class', 'small-2 columns');
				var blogHeader = document.getElementById('blogHeader');
				var blogName = blogHeader.innerHTML;
				var blogNameInput = document.createElement('input');
				blogNameInput.setAttribute('type', 'text');
				blogNameInput.setAttribute('name', 'new_name');
				blogNameInput.setAttribute('value', blogName);
				blogNameInput.setAttribute('id', 'blogHeaderInput');

				var blogNameButton = document.createElement('button');
				blogNameButton.setAttribute('name', 'submitBlogNameButton');
				blogNameButton.setAttribute('id', 'submitBlogNameButton');
				blogNameButton.setAttribute('class', 'button prefix');
				blogNameButton.setAttribute('type', 'submit');
				blogNameButton.innerHTML = "Change Name";
				blogHeader.parentNode.removeChild(blogHeader);
				blogHeaderContainer.appendChild(document.createElement('br'));
				
				blogHeaderInputContainer.appendChild(blogNameInput);
				blogHeaderButtonContainer.appendChild(blogNameButton);
				blogHeaderSubContainer.appendChild(blogHeaderInputContainer);
				blogHeaderSubContainer.appendChild(blogHeaderButtonContainer);
				changeNameForm.appendChild(blogHeaderSubContainer);
				blogHeaderContainer.appendChild(changeNameForm); 

				var blogRenameButton = document.getElementById('renameBlog');
				blogRenameButton.parentNode.removeChild(blogRenameButton);
			}
   		</script> 
	</body>
</html>