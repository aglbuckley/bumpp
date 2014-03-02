<?php
	session_start();
	if(!isset($_SESSION['email']) || !isset($_SESSION['user_id']) || !isset($_SESSION['fname']))
	{
		header('Location: login.html');
		exit();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="css/foundation.css" />
    	<script src="js/vendor/modernizr.js"></script>
	</head>
	<body>
		<?php
			$_SESSION['friend_user_id'] = $_SESSION['user_id'];
			$_SESSION['friendship_accepted'] = 1;
			$_SESSION['friendship_id'] = 0;
			$_SESSION['friend_first_name'] = $_SESSION['fname'];
			$_SESSION['friend_last_name'] = $_SESSION['lname'];
			include('topBar.php');
		?>
		<ul id="drop1" class="f-dropdown" data-dropdown-content>
								  
		</ul>
	
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
				<div id="blogHeaderDiv">
					<hr>
					<h1 id="blogHeader"><?php
						if(!isset($_SESSION['blog_name']) || $_SESSION['blog_name']==''){
							$blogName = "";	
							$blod_id = -1;				
							$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

							if ($mysqli->connect_errno) {
								echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
							}

							/* create a prepared statement */
							if ($stmt = $mysqli->prepare("SELECT name, blog_id FROM blog WHERE user_id=?")) {
						
								if(!$stmt->bind_param("s", $_SESSION['user_id']))
								{
									echo '<h1>Error on select bind</h1>';
									exit();
			
								} else {
									if($stmt->execute()){
										$stmt->bind_result($blogName, $blog_id);
			
									} else {
										echo '<h1>Error on execute</h1>';
										exit();
									}
		
									$stmt->fetch();
				
									$stmt->close();
								}
							}
							$mysqli->close();
							$_SESSION['friend_blog_name'] = stripslashes($blogName);
							$_SESSION['friend_blog_id'] = $blog_id;
							$_SESSION['blog_name'] = stripslashes($blogName);
							$_SESSION['blog_id'] = $blog_id;
						}
						echo $_SESSION['blog_name'];
					?></h1>
					<small><a href="javascript:blogNameUpdate()" id="renameBlog">Rename Blog</a></small>
				</div>
				
				<?php
					if(!isset($_SESSION['info_id'])){
						$info_id = -1;
						$dob = 0;
						$currentLoc = '';
						$phoneNumb = '';
						$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");
	
						if ($mysqli->connect_errno) {
							echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
						}
						
						if ($stmt = $mysqli->prepare("SELECT dob, currentLoc, phoneNumb FROM userinformation WHERE user_id=?")) {
						
							if(!$stmt->bind_param("s", $_SESSION['user_id']))
							{
								echo '<h1>Error on select bind</h1>';
								exit();
		
							} else {
								if($stmt->execute()){
									$stmt->bind_result($dob, $currentLoc, $phoneNumb);
								} else {
									exit();
								}
								$stmt->fetch();
								$stmt->close();
							}
						}
						$mysqli->close();
						$_SESSION['dob'] = $dob;
						$_SESSION['currentLoc'] = $currentLoc;
						$_SESSION['phoneNumb'] = $phoneNumb;
					}
							
				?>
				
				<a href="#" data-dropdown="drop" class="tiny button dropdown" align="right">Information</a><br>
				<ul id="drop" data-dropdown-content class="f-dropdown">
					<li>
						<div id=infoTable>
							<table>
								<thead>
								</thead>
								<tbody>
								 <tr>
								   <td>Date of Birth</td>
								   <td><?php echo $_SESSION['dob'];  ?></td>
								 </tr>
								 <tr>
								   <td>Lives in:</td>
								     <td><?php echo $_SESSION['currentLoc'];  ?></td>
								 </tr>
								 <tr>
								   <td>Phone #:</td>
								     <td><?php echo $_SESSION['phoneNumb'];  ?></td>
								 </tr>
								</tbody>
							</table>
						</div>
					</li>
					 <a href="javascript:editInformation()" id="editInfo">Edit Information</a>
				  </ul>
				
				

				
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
					
						if(!isset($_SESSION['blog_id']) || empty($_SESSION['blog_id']) || $_SESSION['blog_id'] <0)
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
		
									$_SESSION['blog_id'] = $blog_id;

									$stmt->close();
								}
							}
						}

						if ($stmt = $mysqli->prepare("SELECT name, content, timestamp FROM blogpost WHERE blog_id=? ORDER BY timestamp DESC")) {
						
							$postName = "";
							$postContent = "";
							$timestamp = 0;

							if(!$stmt->bind_param("i", $_SESSION['blog_id']))
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
		
		<script src="js/vendor/jquery.js"></script>
    	<script src="js/foundation.min.js"></script>
    	<script src="js/foundation/foundation.reveal.js"></script>
    	<script src="js/foundation/foundation.dropdown.js"></script>
    	<script src="js/foundation/foundation.topbar.js"></script>
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
			xmlhttp.open("GET","liveSearch.php?input="+str,true);
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
						xmlhttp.open("GET","checkFriendRequests.php",true);
						xmlhttp.send();
    	  			},3000);
    	  
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
			
			function editInformation() {
				
			}
   		</script> 
	</body>
</html>