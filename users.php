<?php
	session_start();
	if(!isset($_SESSION['email']) || !isset($_SESSION['user_id']) || !isset($_SESSION['fname']))
	{
		header('Location: /login.html');
		exit();
	}
	
	if($_SESSION['username'] == $_GET['username']){
		header('Location: /');
	}
	
	//code to set friend blog info of the current page you are visiting
	$friendUserID = -1;
	$fBlogName = "";
	$firstName = "";
	$lastName = "";
	$friendshipAccepted = 0;
	$friendshipID = -1;
	$fBlog_id = -1;				
	$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

	/* create a prepared statement */
	if($stmt = $mysqli->prepare("SELECT user_id, first_name, last_name FROM user WHERE username=?"))
	{
		if(!$stmt->bind_param("s", $_GET['username']))
		{
			echo '<h1>Error on select bind</h1>';
			exit();
		} else {
			if($stmt->execute()){
				$stmt->bind_result($friendUserID, $firstName, $lastName);
			} else {
				echo '<h1>Error on execute</h1>';
				exit();
			}
			$stmt->fetch();
			$stmt->close();
		}
	}
	if ($stmt = $mysqli->prepare("SELECT blog.name, blog.blog_id, friendship.friendship_id, friendship.accepted FROM blog, friendship WHERE ((friendship.friender_id = ? AND friendship.friendee_id = ?) OR (friendship.friender_id = ? AND friendship.friendee_id = ?)) AND blog.user_id=?")) {

		if(!$stmt->bind_param("iiiii", $friendUserID, $_SESSION['user_id'], $_SESSION['user_id'], $friendUserID, $friendUserID))
		{
			echo '<h1>Error on select bind</h1>';
			exit();

		} else {
			if($stmt->execute()){
				$stmt->bind_result($fBlogName, $fBlog_id, $friendshipID, $friendshipAccepted);

			} else {
				echo '<h1>Error on execute</h1>';
				exit();
			}

			$stmt->fetch();

			$stmt->close();
		}
	}
	$_SESSION['friend_user_id'] = $friendUserID;
	$_SESSION['friendship_accepted'] = $friendshipAccepted;
	if($friendshipID == null){
		$friendshipID = -1;
	}
	$_SESSION['friendship_id'] = $friendshipID;
	$_SESSION['friend_first_name'] = $firstName;
	$_SESSION['friend_last_name'] = $lastName;
	$_SESSION['friend_blog_name'] = stripslashes($fBlogName);
	$_SESSION['friend_blog_id'] = $fBlog_id;
	$mysqli->close();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<?php
			echo '<link rel="stylesheet" href="/css/foundation.css" />
    			<script src="/js/vendor/modernizr.js"></script>';
    	?>
	</head>
	<body>
		<?php
			include('topBar.php');
		?>

        <ul id="drop1" class="f-dropdown" data-dropdown-content>

        </ul>

		<section role="main">
			<div class="row">
				<div id="blogHeaderDiv">
					<hr>
					<a id = "profilePic" class="th" href="#">
						<?php
							include('retrieveProfilePic.php');
							echo '<img src="'.$_SESSION['profile_image_location'].'">';
						?>
					</a>
					<h1 id="blogHeader"><?php
						if(!isset($_SESSION['friend_blog_name']) || $_SESSION['friend_blog_name']==''){
							$friendUserID = -1;
							$fBlogName = "";	
							$fBlog_id = -1;				
							$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

							if ($mysqli->connect_errno) {
								echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
							}

							/* create a prepared statement */
							if($stmt = $mysqli->prepare("SELECT user_id FROM user WHERE username=?"))
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
							$mysqli->close();
						}
						echo $_SESSION['friend_blog_name'];
					?></h1>
					<?php
						if($_SESSION['friend_user_id']==$_SESSION['user_id']){
							echo '<small><a href="javascript:blogNameUpdate()" id="renameBlog">Rename Blog</a></small>';
						}
					?>
				</div>
				
				<h3 class="subheader"> It's really easy to customize your very own blog!</h3>
				<hr>
				
				<!--Orbit Stuff-->
				
				<ul class="example-orbit" data-orbit> 
					<li> 
						<?php 
							echo '<img src="/images/london1.jpg" alt="slide 1" />'
						?>
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
							echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
							throw new Exception('Failed to connect');
						}
					
						if(!isset($_SESSION['friend_blog_id']) || empty($_SESSION['friend_blog_id']) || $_SESSION['friend_blog_id'] <0)
						{
							$fBlog_id = -1;
							if ($stmt = $mysqli->prepare("SELECT blog_id FROM blog WHERE user_id=?")) {
								if(!$stmt->bind_param("s", $_SESSION['friend_user_id']))
								{
									echo '<h1>Error on select bind</h1>';
									exit();

								} else {
									if($stmt->execute()){
										$stmt->bind_result($fBlog_id);

									} else {
										echo '<h1>Error on execute</h1>';
										exit();
									}

									$stmt->fetch();
		
									$_SESSION['friend_blog_id'] = $gBlog_id;

									$stmt->close();
								}
							}
						}

						if ($stmt = $mysqli->prepare("SELECT name, content, timestamp FROM blogpost WHERE blog_id=? ORDER BY timestamp DESC")) {
						
							$postName = "";
							$postContent = "";
							$timestamp = 0;

							if(!$stmt->bind_param("i", $_SESSION['friend_blog_id']))
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
						echo $e;
						$mysqli->close();
						//exit();
					}
					
				?>
				
			</div>
		</section>
		<?php
			echo '<script src="/js/vendor/jquery.js"></script>
    			<script src="/js/foundation.min.js"></script>
    			<script src="/js/foundation/foundation.reveal.js"></script>';
		?>
        <script>
            //Code modified from w3 schools
            function search(str)
            {
                if (str.length==0)
                {
                    document.getElementById("drop1").innerHTML="";
                    $(document).foundation('dropdown', {
                        activeClass: 'close'
                    });
                    return;
                }
                if (window.XMLHttpRequest){
                    xmlhttp=new XMLHttpRequest();
                }

                xmlhttp.onreadystatechange=function()
                {
                    if (xmlhttp.readyState==4 && xmlhttp.status==200){
                        document.getElementById("drop1").innerHTML=xmlhttp.responseText;
                        $(document).foundation('dropdown', {
                            activeClass: 'open'
                        });
                    }
                }
                xmlhttp.open("GET","/liveSearch.php?input="+str,true);
                xmlhttp.send();
            }
        </script>
    	<script>
			$(document).foundation();
			
    	  	$(document).ready(function() {
    	  
    	  		$('#welcomeModal').foundation('reveal', 'open');
    	  		
    	  		
    	  		setInterval(
    	  			function(){
    	  				//Code modified from w3 schools

						if (window.XMLHttpRequest){
							xmlhttp=new XMLHttpRequest();
						}

						xmlhttp.onreadystatechange=function()
						{
							if (xmlhttp.readyState==4 && xmlhttp.status==200){
								document.getElementById("friendRequests").innerHTML=xmlhttp.responseText;
							}
						}
						xmlhttp.open("GET","/checkFriendRequests.php",true);
						xmlhttp.send();
    	  			},3000);
    	  
			});
			
			function sendFriendRequest()
			{
				//Code modified from w3 schools

				var reqButton = document.getElementById("friendRequestButton");

				if (window.XMLHttpRequest){
					xmlhttp=new XMLHttpRequest();
				}

				xmlhttp.onreadystatechange=function()
				{
					if (xmlhttp.readyState==4 && xmlhttp.status==200){
						document.getElementById("friendRequestButton").innerHTML=xmlhttp.responseText;
						if(xmlhttp.responseText != "Send Friend Request")
						{
							document.getElementById("friendRequestButton").className = "button disabled";
						}
					}
				}
				if(reqButton.className != "button disabled"){
					xmlhttp.open("GET","/sendFriendRequest.php",true);
					xmlhttp.send();
				}
			}
			
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