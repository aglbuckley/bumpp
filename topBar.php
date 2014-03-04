<?php
	echo '<div class="fixed">
			<nav class="top-bar" data-topbar=""> 
				<ul class="title-area"> 
					<li class="name"> 
						<h1><a href="/">bumpp</a></h1> 
					</li> 
					<li class="toggle-topbar menu-icon"><a href="#">Menu</a></li>
				</ul> 
		
				<section class="top-bar-section"> 
					<!-- Right Nav Section --> 
					<ul class="right"> 
						<li class="has-dropdown"> 
							<a href="#">Settings</a> 
							<ul class="dropdown">
								<li><a id="friendRequests" href="#">Friend Requests (0)</a></li>
								<li><a href="/logout.php">Logout</a></li>
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
							<li><a href="#">'.$_SESSION['fname'].'</a></li>
						<li class="divider"></li>
						<li class="has-form"> 
							<div class="row collapse"> 
								<div class="large-8 small-9 columns"> 
									<input type="text" onkeyup="search(this.value)" placeholder="Search for Friends" data-dropdown="drop1"> 
								</div> 
								<div class="large-4 small-3 columns"> 
									<a href="#" class="button expand">Search</a> 
								</div>
							</div> 
						</li>
						<li class="divider"></li>
						<!--<li class="active"><a href="#">Andrew Buckley</a></li>-->
						<li class="has-dropdown">
							<a href="#">'.$_SESSION['friend_first_name'].' '.$_SESSION['friend_last_name'].'</a>
							<ul class="dropdown">
								<!--<li><a href="#" onclick="sendFriendRequest()">Send Friend Request</a></li>-->
							</ul>
						</li>
						<li class="divider"></li>
						<li id="friendRequestNav" class="has-form">';
						if($_SESSION['friendship_id'] > 0)
						{
							if($_SESSION['friendship_accepted']==0){
								echo '<a id="friendRequestButton" href="#" onclick="sendFriendRequest()" class="button disabled">Request Initiated</a>';	
							} else {
								echo '<a id="friendRequestButton" href="#" onclick="sendFriendRequest()" class="button disabled">Friends</a>';	
							}
						} else if($_SESSION['friendship_id'] == -1)
						{
							echo '<a id="friendRequestButton" href="#" onclick="sendFriendRequest()" class="button">Send Friend Request</a>';
						}
						echo '</li>
					</ul> 
				</section> 
			</nav>
		</div>';
?>