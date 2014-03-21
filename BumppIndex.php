<?php

require_once('BumppUtility.php');
require_once('BumppPageBase.php');

class BumppIndex extends BumppPageBase{
    private $title, $fname, $lname, $username, $userID, $friendshipAccepted;
    private $friendshipID, $email, $blogName, $blogID, $infoID, $dob, $currentLoc, $phone;
    private $profileImageID, $profileImageLocation;
    public function __construct($pageTitle, $username){
        $this->username = $username;
        $this->title = $pageTitle;
        parent::__construct();
    }

    public function title(){
        return $this->title;
    }

    public function processIncomingFormData(){
        //No incoming form data, so just generate the body
        if($this->username == $_SESSION['username']){
            $this->userID = $_SESSION['user_id'];
            $this->friendshipAccepted = 1;
            $this->friendshipID = 0;
            $this->fname = $_SESSION['fname'];
            $this->lname = $_SESSION['lname'];
            $this->email = $_SESSION['email'];
            $this->username = $_SESSION['username'];
            $this->blogName = $_SESSION['blog_name'];
            $this->blogID = $_SESSION['blog_id'];
            $this->profileImageLocation = $_SESSION['profile_image_location'];
            $this->profileImageID = $_SESSION['profile_image_id'];
        } else {
            if($_SESSION['username'] == $_GET['username']){
                header('Location: /');
            }
            $mysqli = BumppUtility::mySqlConnect();

            if($stmt = $mysqli->prepare("SELECT user.user_id, user.first_name, user.last_name, user.email, photo.image, photo.photo_id FROM user, photo WHERE photo.photo_id=user.profile_image_id AND username=?"))
            {
                if(!$stmt->bind_param("s", $this->username))
                {
                    echo '<h1>Error on select bind</h1>';
                    exit();
                } else {
                    if($stmt->execute()){
                        $stmt->bind_result($this->userID, $this->fname, $this->lname, $this->email, $this->profileImageLocation, $this->profileImageID);
                    } else {
                        echo '<h1>Error on execute</h1>';
                        exit();
                    }
                    $stmt->fetch();
                    $stmt->close();
                }
            }
            if ($stmt = $mysqli->prepare("SELECT blog.name, blog.blog_id, friendship.friendship_id, friendship.accepted FROM blog, friendship WHERE ((friendship.friender_id = ? AND friendship.friendee_id = ?) OR (friendship.friender_id = ? AND friendship.friendee_id = ?)) AND blog.user_id=?")) {

                if(!$stmt->bind_param("iiiii", $this->userID, $_SESSION['user_id'], $_SESSION['user_id'], $this->userID, $this->userID))
                {
                    echo '<h1>Error on select bind</h1>';
                    exit();

                } else {
                    if($stmt->execute()){
                        $stmt->bind_result($this->blogName, $this->blogID, $this->friendshipID, $this->friendshipAccepted);

                    } else {
                        echo '<h1>Error on execute</h1>';
                        exit();
                    }

                    $stmt->fetch();

                    $stmt->close();
                }
            }
            if($this->friendshipID == null){
                $this->friendshipID = -1;
            }
            $mysqli->close();
        }
        //probably put this somewhere else
        $this->fetchMoreInfo();

        $this->generateBody();
    }

    public function emitPageHeader($fname, $lname, $friendID, $friendAccepted){
        parent::emitPageHeader($fname, $lname, $friendID, $friendAccepted);
    }

    public function generateBody(){
        echo '<!DOCTYPE html>';
        echo '<html>';
        $this->generateHead();
        echo '<body>';
        $this->emitPageHeader($this->fname, $this->lname, $this->friendshipID, $this->friendshipAccepted);
        parent::emitSidebar();
        if($this->userID == $_SESSION['user_id']){
            $this->welcomeModal();
        }
        $this->generateMainSection();

        $this->generateScripts();
        echo '</body>';
        echo '</html>';

    }

    public function generateMainSection(){
        echo '<section role="main">
			<div class="row">';
        echo '<dl class="tabs" data-tab>
                <dd class="active"><a href="#panel1">Profile</a></dd>
                <dd><a href="#panel2">Activity</a></dd>
                </dl>';
        echo '<div class="tabs-content">
                <div class="content active" id="panel1" style="width: 100%;">';

        $this->generateBlogHeaderSection();
        echo'<h3 class="subheader"> It\'s really easy to customize your very own blog!</h3>
				<hr>

				<!--Orbit Stuff-->

				<ul class="example-orbit" data-orbit>
					<li>
						<img src="/images/london1.jpg" alt="slide 1" />
						<div class="orbit-caption">
                            London.
						</div>
					</li>
				</ul>';
        $this->generateBlogPosts();
        //close this panel
        echo '</div>';
        echo '<div class="content" id="panel2" style="width: 100%;">';
        $this->generateActivity();
        //close second panel
        echo '</div>';
        //close tabs content div
        echo '</div>';
        //Maybe switch these
        echo '</section>
              </div>';
    }

    public function generateActivity()
    {
        echo "<h1>Activity</h1>
                <hr>";
        //$activityTextArray = array();
        if($this->userID == $_SESSION['user_id']){
            //returns array
            $activityTextArray = $this->fetchLogInfo(false, $this->userID);
        } else {
            $activityTextArray = $this->fetchLogInfo(true, $this->userID);
        }

        foreach($activityTextArray as $line)
        {
            $explodedLine = explode("||", $line);
            $timeText = trim($explodedLine[0]);
            $time = date("d F Y", strtotime($timeText));
            $activity = trim($explodedLine[1]);
            $explodedActivity = explode("(", $activity);
            $activity = str_replace(")", "", $explodedActivity[1]);
            echo '<h3>'.$time.'</h3>';
            echo '<p>'.$activity.'</p>';
        }
    }

    public function fetchLogInfo($public, $user_id){
        return BumppUtility::FetchLogContents($public, $user_id);
    }

    public function generateBlogPosts(){
        ini_set('memory_limit', '-1');
        try{
            $mysqli = BumppUtility::mySqlConnect();
//            if(!isset($_SESSION['blog_id']) || empty($_SESSION['blog_id']) || $_SESSION['blog_id'] <0)
            //if(!isset($this->blogID) || empty($this->blogID) || $this->blogID <0)
            //{
                $blog_id = -1;
                if($stmt = $mysqli->prepare("SELECT blog_id FROM blog WHERE user_id=?")){
                    if(!$stmt->bind_param("s", $this->userID))
                    {
                        echo '<h1>Error on select bing</h1>';
                        exit();
                    } else {
                        if($stmt->execute()){
                            $stmt->bind_result($blog_id);
                        } else {
                            echo '<h1>Error on execute</h1>';
                            exit();
                        }

                        $stmt->fetch();
                        $this->blogID = $blog_id;
                        if($this->userID == $_SESSION['user_id']){
                            $_SESSION['blog_id'] = $this->blogID;
                        }
                        $stmt->close();
                    }
                }
            //}

            if($stmt = $mysqli->prepare("SELECT name, content, timestamp FROM blogpost WHERE blog_id = ? ORDER BY timestamp DESC")){
                $postName = "";
                $postContent = "";
                $timestamp = 0;

                if(!$stmt->bind_param("i", $this->blogID))
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
                throw new Exception("Error on Prepare");
            }
            $mysqli->close();
        } catch (Exception $e){
            echo '<h1>Uh oh!</h1>';
            echo '<h3 class="subheader">Something went wrong. Please reload the page.</h3>';
            $mysqli->close();
        }
    }

    public function fetchMoreInfo(){
        if(!isset($this->infoID)){
            $info_id = -1;
            $dob = 0;
            $currentLoc = '';
            $phoneNumb = '';
            $mysqli = BumppUtility::mySqlConnect();

            if($stmt = $mysqli->prepare("SELECT dob, currentLoc, phoneNumb FROM userinformation WHERE user_id=?")){
                if(!$stmt->bind_param("s", $this->userID))
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
            $this->dob = $dob;
            $this->currentLoc = $currentLoc;
            $this->phone = $phoneNumb;
            if($this->userID == $_SESSION['user_id']){
                $_SESSION['dob'] = $dob;
                $_SESSION['currentLoc'] = $currentLoc;
                $_SESSION['phoneNumb'] = $phoneNumb;
            }
        }
    }

    public function generateProfileInfo()
    {
        echo '<fieldset class="row">
            <legend><h4><b>'.$this->fname.'</b>'.$this->lname.'</h4></legend>
            <div class="small-1 large-2 columns">
        <a id = "profilePic" class="th" href="#">';
		include('retrieveProfilePic.php');
		echo '<img src="'.$this->profileImageLocation.'">
					</a>
					<br>
		';
        if($this->username == $_SESSION['username']){
            echo '<a id="profileEditButton" data-options="align:down" data-dropdown="profile-edit-drop" href="#" class="button">Edit</a>';
        }
		echo '</div>
		<div class="small-11 large-10 columns">
		<h4>Email: <small>'.$this->email.'</small></h4>
		<h4>Username: <small>'.$this->username.'</small></h4>
		<h4>Birthday: <small>'.date("d F Y", strtotime($this->dob)).'</small></h4>
		<h4>Phone Number: <small>'.$this->phone.'</small></h4>
		<h4>Location: <small>'.$this->currentLoc.'</small></h4>
		</div>
		</fieldset>';

        if($this->username == $_SESSION['username']){
            echo '<ul id="profile-edit-drop" class="large f-dropdown" data-dropdown-content>
		    <form action="updateProfile.php" method="post" enctype="multipart/form-data">
                <fieldset>
                    <legend><h4><b>'.$this->fname.'</b>'.$this->lname.'</h4></legend>
                    <div class="row collapse">
                        <input type="file" name="newProfilePhoto" placeholder="profile photo">
                    </div>
                    <div class="row collapse">
                        <div class="medium-9 columns">
                          <input type="email" name="newEmail" placeholder="'.$this->email.'">
                        </div>
                        <div class="medium-3 columns">
                          <span class="postfix radius">Email</span>
                        </div>
                    </div>
                    <div class="row collapse">
                        <div class="medium-9 columns">
                          <input type="text" name="newUsername" placeholder="'.$this->username.'">
                        </div>
                        <div class="medium-3 columns">
                          <span class="postfix radius">Username</span>
                        </div>
                    </div>
                    <div class="row collapse">
                        <div class="medium-9 columns">
                          <input type="date" name="newBirthday" placeholder="'.date("d F Y", strtotime($this->dob)).'">
                        </div>
                        <div class="medium-3 columns">
                          <span class="postfix radius">Birthday</span>
                        </div>
                    </div>
                    <div class="row collapse">
                        <div class="medium-9 columns">
                          <input type="tel" name="newPhone" placeholder="'.$this->phone.'">
                        </div>
                        <div class="medium-3 columns">
                          <span class="postfix radius">Phone</span>
                        </div>
                    </div>
                    <div class="row collapse">
                        <div class="medium-9 columns">
                          <input type="text" name="newLocation" placeholder="'.$this->currentLoc.'">
                        </div>
                        <div class="medium-3 columns">
                          <span class="postfix radius">Location</span>
                        </div>
                    </div>
                    <button type="submit">Save</button>
                </fieldset>
            </form>
        </ul>';
        }
    }

    public function generateBlogHeaderSection(){
        echo '<div id="blogHeaderDiv">
                <hr>';

        $this->generateProfileInfo();

        echo'<hr>
        <h1 id="blogHeader">';

        //if(!isset($this->blogName) || $this->blogName == ''){
            $blogName = "";
            $blog_id = "";
            $mysqli = BumppUtility::mySqlConnect();
            /* create a prepared statement */
            if ($stmt = $mysqli->prepare("SELECT name, blog_id FROM blog WHERE user_id=?")) {

                if(!$stmt->bind_param("s", $this->userID))
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
            //$_SESSION['friend_blog_name'] = stripslashes($blogName);
            //$_SESSION['friend_blog_id'] = $blog_id;
            //$_SESSION['blog_name'] = stripslashes($blogName);
            //$_SESSION['blog_id'] = $blog_id;
            $this->blogID = $blog_id;
            $this->blogName = stripslashes($blogName);
            if($this->userID == $_SESSION['user_id']){
                $_SESSION['blog_id'] = $this->blogID;
                $_SESSION['blog_name'] = $this->blogName;
            }
        //}
        echo $this->blogName;

        echo '</h1>';
        if($this->userID==$_SESSION['user_id']){
            echo '<small><a href="javascript:blogNameUpdate()" id="renameBlog">Rename Blog</a></small>';
        }
        echo '</div>';
    }

    public function generateScripts(){
        parent::generateScripts();
        echo '<script src="/js/foundation/foundation.reveal.js"></script>
    	<script src="/js/foundation/foundation.dropdown.js"></script>
    	<script src="/js/foundation/foundation.topbar.js"></script>
    	<script src="/js/foundation/foundation.tab.js"></script>
        <script src="/js/spin.min.js"></script>';

        echo '<script>
            function openNewMessageModal()
            {
                $(\'#newMessageModal\').foundation(\'reveal\', \'open\');
            }

            function sendNewMessage(canSend)
            {
                if(canSend){
                    var messageName = document.getElementById(\'messageNameInput\').value;
                    var messageTo = document.getElementById(\'messageToInput\').value; //$(\'#messageToInput\').value;
                    var messageContent = document.getElementById(\'messageContent\').value;
                    var messageSendButton = document.getElementById(\'messageSendButton\');

                    messageSendButton.innerHTML = "Sending...";
                    messageSendButton.setAttribute("onclick", "sendNewMessage(false)");// = sendNewMessage(false);

                    //From spin.min.js http://fgnass.github.io/spin.js/
                    var opts = {
                        lines: 13, // The number of lines to draw
                        length: 0, // The length of each line
                        width: 6, // The line thickness
                        radius: 20, // The radius of the inner circle
                        corners: 1, // Corner roundness (0..1)
                        rotate: 50, // The rotation offset
                        direction: 1, // 1: clockwise, -1: counterclockwise
                        color: \'#000\', // #rgb or #rrggbb or array of colors
                        speed: 1, // Rounds per second
                        trail: 60, // Afterglow percentage
                        shadow: false, // Whether to render a shadow
                        hwaccel: false, // Whether to use hardware acceleration
                        className: \'spinner\', // The CSS class to assign to the spinner
                        zIndex: 2e9, // The z-index (defaults to 2000000000)
                        top: \'auto\', // Top position relative to parent in px
                        left: \'auto\' // Left position relative to parent in px
                    };
                    var target = messageSendButton;
                    var spinner = new Spinner(opts).spin(target);

                    if (window.XMLHttpRequest){
                        xmlhttp=new XMLHttpRequest();
                    }

                    xmlhttp.onreadystatechange=function()
                    {
                        if (xmlhttp.readyState==4 && xmlhttp.status==200){
                            var response = xmlhttp.responseText;
                            if(response != "Message Sent"){
                                document.getElementById(\'messageSendButton\').innerHTML = "Failed to Send";
                            } else {
                                document.getElementById(\'messageSendButton\').innerHTML = response;
                                setTimeout(function() {
                                    $(\'#newMessageModal\').foundation(\'reveal\', \'close\');
                                    setTimeout(function(){
                                        document.getElementById(\'messageNameInput\').value = "";
                                        document.getElementById(\'messageToInput\').value = ""; //$(\'#messageToInput\').value;
                                        document.getElementById(\'messageContent\').value = "";
                                        messageSendButton.innerHTML = "Send";
                                        messageSendButton.setAttribute("onclick", "sendNewMessage(true)");// = sendNewMessage(false);
                                    }, 400);

                                }, 1000);
                            }
                        }
                    }
                    xmlhttp.open("POST","sendNewMessage.php",true);
                    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                    xmlhttp.send("messageName="+messageName+"&to="+messageTo+"&content="+messageContent);
                }
            }
        </script>

        <script>
            //Code modified from w3 schools
            function search(str)
                {
                    if (str.length==0)
                    {
                        document.getElementById("drop1").innerHTML="";
                        $(document).foundation(\'dropdown\', {
                              activeClass: \'close\'
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
                            $(document).foundation(\'dropdown\', {
                              activeClass: \'open\'
                            });
                        }
                    }
                    xmlhttp.open("GET","liveSearch.php?input="+str,true);
                    xmlhttp.send();
                }

            function insertUsername(str)
                {
                    $(document).foundation(\'friends-dropdown\', {
                        activeClass: \'close\'
                    });
                    document.getElementById("messageToInput").value = str;
                }

            function searchMessageFriends(str)
                {
                    if (str.length==0)
                    {
                        document.getElementById("freinds-dropdown").innerHTML="";
                        $(document).foundation(\'friends-dropdown\', {
                            activeClass: \'close\'
                        });
                        return;
                    }
                    if (window.XMLHttpRequest){
                        xmlhttp=new XMLHttpRequest();
                    }

                    xmlhttp.onreadystatechange=function()
                    {
                        if (xmlhttp.readyState==4 && xmlhttp.status==200){
                            document.getElementById("friends-dropdown").innerHTML=xmlhttp.responseText;
                            $(document).foundation(\'friends-dropdown\', {
                                activeClass: \'open\'
                            });
                        }
                    }
                    xmlhttp.open("GET","newMessageFriendSearch.php?input="+str,true);
                    xmlhttp.send();
                }
        </script>
        <script>
        $(document).foundation();

        $(document).foundation({
            tab: {
                callback : function (tab) {
                    console.log(tab);
                }
            }
        });

        $(document).ready(function() {

            var sidebar = $(\'#sidebar\');

            var barTop = sidebar.offset().top;

            $(window).scroll(function(){

                if (barTop < $(window).scrollTop()) {
                    sidebar.css({
                                    position: \'fixed\'
                                });
                            } else {
                    sidebar.css(\'position\',\'absolute\');
                }

            });

            $(\'#welcomeModal\').foundation(\'reveal\', \'open\');
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
                            },15000);

        });

            function commentReveal(index)
                    {
                        var postActionsDiv = document.getElementById(\'postActionsDiv\'+index);
                        postActionsDiv.parentNode.removeChild(postActionsDiv);
                    }

            function profileUpdate()
            {

            }

            function blogNameUpdate()
            {
                var changeNameForm = document.createElement(\'form\');
                changeNameForm.setAttribute(\'name\', \'input\');
                changeNameForm.setAttribute(\'action\', \'changeName.php\');
                changeNameForm.setAttribute(\'method\', \'post\');
                var blogHeaderContainer = document.getElementById(\'blogHeaderDiv\');

                var blogHeaderSubContainer = document.createElement(\'div\');
                blogHeaderSubContainer.setAttribute(\'class\', \'row collapse\');
                var blogHeaderInputContainer = document.createElement(\'div\');
                blogHeaderInputContainer.setAttribute(\'class\', \'small-10 columns\');
                var blogHeaderButtonContainer = document.createElement(\'div\');
                blogHeaderButtonContainer.setAttribute(\'class\', \'small-2 columns\');
                var blogHeader = document.getElementById(\'blogHeader\');
                var blogName = blogHeader.innerHTML;
                var blogNameInput = document.createElement(\'input\');
                blogNameInput.setAttribute(\'type\', \'text\');
                blogNameInput.setAttribute(\'name\', \'new_name\');
                blogNameInput.setAttribute(\'value\', blogName);
                blogNameInput.setAttribute(\'id\', \'blogHeaderInput\');

                var blogNameButton = document.createElement(\'button\');
                blogNameButton.setAttribute(\'name\', \'submitBlogNameButton\');
                blogNameButton.setAttribute(\'id\', \'submitBlogNameButton\');
                blogNameButton.setAttribute(\'class\', \'button prefix\');
                blogNameButton.setAttribute(\'type\', \'submit\');
                blogNameButton.innerHTML = "Change Name";
                blogHeader.parentNode.removeChild(blogHeader);
                blogHeaderContainer.appendChild(document.createElement(\'br\'));

                blogHeaderInputContainer.appendChild(blogNameInput);
                blogHeaderButtonContainer.appendChild(blogNameButton);
                blogHeaderSubContainer.appendChild(blogHeaderInputContainer);
                blogHeaderSubContainer.appendChild(blogHeaderButtonContainer);
                changeNameForm.appendChild(blogHeaderSubContainer);
                blogHeaderContainer.appendChild(changeNameForm);

                var blogRenameButton = document.getElementById(\'renameBlog\');
                blogRenameButton.parentNode.removeChild(blogRenameButton);
            }

            function editInformation() {

            }
        </script>';
        parent::generateMessagingScript();
    }

    private function welcomeModal(){
        parent::checkLoggedIn();
        $this->fname = $_SESSION['fname'];
        $this->lname = $_SESSION['lname'];

        echo '<div id="welcomeModal" class="reveal-modal" data-reveal>
			<h1>Welcome to bumpp, '.$this->fname.'!</h1>
			<p>More info coming soon...</p>
			<a class="close-reveal-modal">&#215;</a>
			</div>';
    }
}

?>