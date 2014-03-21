<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 3/15/14
 * Time: 2:03 PM
 */
require_once("Messaging.php");
abstract class BumppPageBase {
    /**
     *
     */
    public function __construct(){
        //Initialize any necessary variables.
        $this->processRequest();
    }

    /**
     *
     */
    public function processRequest(){
        //Get and validate the form data
        session_start();
        $this->checkLoggedIn();
        //if(isset($_POST) and count($_POST) != 0){
            $this->processIncomingFormData();
        //}
    }

    public function generateHead(){
        echo '<head>
		        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
		        <link rel="stylesheet" href="/css/foundation.css" />
    	        <script src="/js/vendor/modernizr.js"></script>
	        </head>';
    }

    public function checkLoggedIn(){
        if(!isset($_SESSION['email']) || !isset($_SESSION['user_id']) || !isset($_SESSION['fname']))
        {
            header('Location: login.html');
            exit();
        }
    }

    public function emitSidebar(){
        echo'<div id="sidebar">
            <a href="#" onclick="openNewMessageModal()">+</a>
            <hr>';
        echo Messaging::FetchConversations($_SESSION['user_id']);
        echo '<hr>
        </div>';
        echo '<ul id="conversation-drop" class="small f-dropdown" style = "position: fixed;" data-dropdown-content>
                <div class="row">
                    <div class = "medium-1 columns"></div>
                    <div id="messages" class="medium-10 columns" style="width: 100%; height: 100px; overflow-y: scroll;">

                    </div>
                    <div class = "medium-1 columns"></div>
                </div>
                <hr>
            <div id = "messageSendForm" class="row collapse">
                <!--<form action="sendMessage.php" method="post">-->
                    <div class = "medium-1 columns"></div>
                    <div class="small-7 columns">
                        <input id = "message" name="message" type="text" placeholder="Message">
                    </div>
                    <input type="hidden" name="conversationID" id="conversationIDHidden">
                    <div class="small-3 columns">
                        <button type="submit" onclick="sendMessageAjax()" class="button postfix">Send</button>
                    </div>
                    <div class = "medium-1 columns"></div>
                <!--</form>-->
            </div>
            </ul>';
        echo '<div id="newMessageModal" class="reveal-modal" data-reveal>
            <h1>New Message</h1>
            <input id="messageNameInput" type="text" placeholder="conversation name">
            <input id="messageToInput" type="search" onkeyup="searchMessageFriends(this.value)" placeholder="to" data-dropdown="friends-dropdown">
            <textarea id="messageContent" rows="60" placeholder="message content"></textarea>
            <a href="#" id="messageSendButton" onclick="sendNewMessage(true)" class="button expand">Send</a>
        </div>

        <ul id="friends-dropdown" class="f-dropdown" data-dropdown-content>

        </ul>';
    }

    public function emitPageHeader($fname, $lname, $friendID, $friendAccepted){
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
							<li class="has-dropdown">
							    <a href="#">'.$_SESSION['fname'].'</a>
							    <ul class="dropdown">
							        <li><a href="circles.php">Circles</a></li>
							    </ul>
							</li>
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
							<a href="#">'.$fname.' '.$lname.'</a>
							<ul class="dropdown">
								<!--<li><a href="#" onclick="sendFriendRequest()">Send Friend Request</a></li>-->
							</ul>
						</li>
						<li class="divider"></li>
						<li id="friendRequestNav" class="has-form">';
        if($friendID > 0)
        {
            if($friendAccepted==0){
                echo '<a id="friendRequestButton" href="#" onclick="sendFriendRequest()" class="button disabled">Request Initiated</a>';
            } else {
                echo '<a id="friendRequestButton" href="#" onclick="sendFriendRequest()" class="button disabled">Friends</a>';
            }
        } else if($friendID == -1)
        {
            echo '<a id="friendRequestButton" href="#" onclick="sendFriendRequest()" class="button">Send Friend Request</a>';
        }
        echo '</li>
					</ul>
				</section>
			</nav>
		</div>
		<ul id="drop1" class="f-dropdown" data-dropdown-content>

		</ul>';
    }

    public function emitPageHeaderDefault(){
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
							<li class="has-dropdown">
							    <a href="#">'.$_SESSION['fname'].'</a>
							    <ul class="dropdown">
							        <li><a href="circles.php">Circles</a></li>
							    </ul>
							</li>
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

					</ul>
				</section>
			</nav>
		</div>
		<ul id="drop1" class="f-dropdown" data-dropdown-content>

		</ul>';
    }


    public function generateScripts(){
        echo '<script src="js/vendor/jquery.js"></script>
    	<script src="js/foundation.min.js"></script>';
    }

    public function generateMessagingScript()
    {
        echo '<script>

                function sendMessageAjax()
                {
                    var convID = document.getElementById("conversationIDHidden").value;
                    var message = document.getElementById("message").value;
                    if (window.XMLHttpRequest){
                        xmlhttp=new XMLHttpRequest();
                    }

                    xmlhttp.onreadystatechange=function()
                    {
                        if (xmlhttp.readyState==4 && xmlhttp.status==200){
                            document.getElementById("message").value = "";
                            fetchConversation(convID);
                        }
                    }
                    xmlhttp.open("POST","sendMessage.php",true);
                    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                    xmlhttp.send("conversationID="+convID+"&message="+message);
                }

                function fetchConversation(num)
                {
                    var drop = document.getElementById("conversation-drop");
                    if (window.XMLHttpRequest){
                        xmlhttp=new XMLHttpRequest();
                    }

                    xmlhttp.onreadystatechange=function()
                    {
                        if (xmlhttp.readyState==4 && xmlhttp.status==200){
                            var response = xmlhttp.responseText;
                            var html = generateHTMLFromJSON(JSON.parse(xmlhttp.responseText));
                            var messagesArea = document.getElementById("messages");
                            messagesArea.innerHTML = html;
                            document.getElementById("conversationIDHidden").setAttribute("value", num);
                        }
                    }
                    xmlhttp.open("GET","fetchMessage.php?messageID="+num,true);
                    xmlhttp.send();
                }

                function generateHTMLFromJSON(jsonObject)
                {
                    var members = {};
                    var messages = {};
                    var row = "";
                    for(var obj in jsonObject){
                        var level1 = jsonObject[obj];
                        if(obj == "Members"){
                            for(var indexItem in level1)
                            {
                                var level2 = level1[indexItem];
                                var sender = 0;
                                var data = {};
                                for(var key in level2){
                                    if(key == \'conversationMemberID\'){
                                        sender = level2[key];
                                    } else {
                                        data[key] = level2[key];
                                    }
                                    //alert(key + ": "+level2[key]);
                                }
                                members[sender] = data;
                            }
                        } else {
                            for(var indexItem in level1)
                            {
                                var level2 = level1[indexItem];
                                var sender = level2[\'sender\'];
                                row = row + "<h2><b>" + members[sender][\'fname\'] + "</b><h2>" + "<p>" + level2[\'content\'] + "</p>";
                            }
                        }
                    }
                    return row;
                }
        </script>';
    }

    /**
     * @return mixed
     */
    public abstract function title();

    /**
     * @return mixed
     */
    public abstract function processIncomingFormData();

    /**
     * @return mixed
     */
    public abstract function generateBody();

    public abstract function generateMainSection();
}

?>