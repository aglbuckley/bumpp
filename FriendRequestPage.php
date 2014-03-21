<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 3/19/14
 * Time: 10:14 PM
 */
require_once("BumppUtility.php");
require_once("BumppPageBase.php");
class FriendRequestPage extends BumppPageBase {

    /**
     * @return mixed
     */
    public function title()
    {
        // TODO: Implement title() method.
    }

    /**
     * @return mixed
     */
    public function processIncomingFormData()
    {
        // TODO: Implement processIncomingFormData() method.
        //No incoming form data
        $this->generateBody();
    }

    /**
     * @return mixed
     */
    public function generateBody()
    {
        // TODO: Implement generateBody() method.
        echo '<!DOCTYPE html>';
        echo '<html>';
        $this->generateHead();
        echo '<body>';

        $this->emitPageHeaderDefault();
        parent::emitSidebar();

        $this->generateMainSection();

        $this->generateScripts();
        echo '</body>';
        echo '</html>';
    }

    public function generateMainSection()
    {
        // TODO: Implement generateMainSection() method.
        echo '<section role="main">
			<div class="row">

            <hr>
            <h1>Your Friend Requests</h1>
            <hr>';

        $mysqli = BumppUtility::mySqlConnect();
        $username = "";
        $userID = 0;
        $friendshipID = 0;
        $fname = "";
        $lname = "";
        $imageLocation = "";
        $response = "";

        if($stmt = $mysqli->prepare("SELECT user.username, user.user_id, user.first_name, user.last_name, photo.image, friendship.friendship_id FROM user, photo, friendship WHERE friendship.friendee_id = ? AND friendship.accepted = 0 AND user.user_id = friendship.friender_id AND photo.photo_id = user.profile_image_id;"))
        {
            session_start();
            if(!$stmt->bind_param("i", $_SESSION['user_id']))
            {
                echo '<h1>Uh oh. Something went wrong</h1>';
                exit();
            } else {
                if($stmt->execute())
                {
                    if(!$stmt->bind_result($username, $userID, $fname, $lname, $imageLocation, $friendshipID))
                    {
                        echo '<h1>Uh oh!</h1>';
                        exit();
                    } else {
                        $stmt->store_result();
                        if(mysqli_stmt_num_rows($stmt)==0)
                        {
                            echo '<h1>No Requests</h1>';
                            exit();
                        }

                        $i=0;
                        $memberCount = 0;
                        while($stmt->fetch())
                        {
                            $memberCount++;
                            if($i%3 == 0){
                                $response = $response.'<div class="row" data-equalizer>';
                            }
                            $response = $response.'<div id = "'.$username.'Cell" class="medium-4 column">';
                            $response = $response.'<a id = "profilePic" class="th" href="users/'.$username.'">
                    <img src="'.$imageLocation.'">
					</a>';
                            $response = $response.'<h4><b>'.$fname.'</b>'.$lname.'</h4>';
                            //$response = $response.'<h4>'.$lname.'</h4>';
                            $response = $response.'<h5><em>'.$username.'</em></h5>';
                            $response = $response.'<small><a href="acceptFriendRequest.php?friendshipID='.$friendshipID.'">Accept Friend Request</a></small>';
                            $response = $response.'</div>';

                            if($memberCount==2 && $i == mysqli_stmt_num_rows($stmt)-1)
                            {
                                $response = $response.'<div class="medium-4 columns"></div>';
                            }

                            if($memberCount==3){
                                $response = $response.'</div>';
                                $memberCount = 0;
                            }
                        }
                    }
                }
                $stmt->close();
            }
        }
        $mysqli->close();
        echo $response;

        echo '</section>
              </div>';
    }

    //Overwritted Functions
    public function generateScripts(){
        parent::generateScripts();
        echo '<script src="js/foundation/foundation.reveal.js"></script>
    	<script src="js/foundation/foundation.dropdown.js"></script>
    	<script src="js/foundation/foundation.topbar.js"></script>
        <script src="js/spin.min.js"></script>';

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
        </script>';
        parent::generateMessagingScript();
    }
}