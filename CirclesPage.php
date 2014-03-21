<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 3/19/14
 * Time: 10:14 PM
 */
require_once("BumppUtility.php");
require_once("BumppPageBase.php");
class CirclesPage extends BumppPageBase {

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
        echo '<div id="createCircleModal" class="reveal-modal" data-reveal>
                        <h1>Create Circle</h1>
                        <input id="circleNameInput" type="text" placeholder="circle name">
                        <input id="circleMembersInput" type="search" onkeyup="searchCircleFriends(this.value)" placeholder="members" data-dropdown="circle-dropdown">
                        <fieldset id="circleCreateFieldset">
                            <legend>Friends in Circle</legend>
                        </fieldset>
                        <a href="#" id="circleCreateButton" onclick="createCircle(true)" class="button expand">Create</a>
                    </div>

                    <ul id="circle-dropdown" class="f-dropdown" data-dropdown-content>

                    </ul>';
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
            <h1>Your Circles</h1>
            <hr>';

        $mysqli = BumppUtility::mySqlConnect();

        if($stmt = $mysqli->prepare("SELECT group_id,  name FROM groups WHERE owner_id = ?")){
            $group_id = -1;
            $name = "";
            $group_ids = array();
            $names = array();

            if(!$stmt->bind_param("i", $_SESSION['user_id']))
            {
                echo '<h1>Error on select bind</h1>';
                exit();

            } else {
                if($stmt->execute()){

                    if($stmt->bind_result($group_id, $name))
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
                    echo '<h3 class="subheader">You have not created any Circles yet. Create one now!</h3>';
                }

                while($stmt->fetch())
                {
                    array_push($group_ids, $group_id);
                    array_push($names, $name);
                }

                $stmt->close();

                $circleCount = 0;
                echo '<div id="circles">';
                for($i = 0; $i<count($names)+1; $i++){
                    $circleCount++;
                    if($i%3==0){
                        echo '<div class="row" data-equalizer>';
                    }
                    if($i == count($names)){
                        echo '<div class="medium-4 columns">
                                <a href="#" onclick="openCreateCircleModal()"><div class="panel" data-equalizer-watch>
                                    <h2>New Circle</h2>
                                </div></a>
                              </div>';
                    } else {
                        echo '<div class="medium-4 columns">
                                <a href="#" onclick="selectCircle('.$group_ids[$i].')">';
                        /*if($i%2==0){
                            echo '<div class="panel" data-equalizer-watch>';
                        } else {
                            echo '<div class="panel callout" data-equalizer-watch>';
                        }*/
                        echo '<div id="circle'.$group_ids[$i].'" class="panel" data-equalizer-watch>';
                        echo '<h2>'.$names[$i].'</h2>';
                        echo '</div></a>
                        </div>';
                    }

                    if($circleCount==2 && $i == count($names))
                    {
                        echo '<div class="medium-4 columns"></div>';
                    }

                    if($circleCount==3){
                        echo '</div>';
                        $circleCount = 0;
                    }
                }
                echo '</div>';
            }
        }
        $mysqli->close();

        echo '<hr>
        <div id="circleInformation">
            <h1>No Circle Selected</h1>
        </div>';

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
            /*function showRemoveOption(divID)
            {
                var div = document.getElementById(divID);
                var removeOption = document.createElement("small");
                var link = document.createElement("a");
                link.setAttribute("href", "#");
                link.innerHTML = "Remove From Circle";
                removeOption.appendChild(link);
                removeOption.setAttribute("id", divID+"Small");
                div.appendChild(removeOption);
            }

            function hideRemoveOption(divID)
            {
                var div = document.getElementById(divID);
                var smallElement = document.getElementById(divID+"Small");
                div.removeChild(smallElement);
            }*/

            function selectCircle(num)
            {
                if(document.getElementById("circle"+num).className == "panel callout")
                {
                    document.getElementById("circle"+num).setAttribute("class", "panel");
                    document.getElementById("circleInformation").innerHTML = "<h1>No Circle Selected</h1>";
                    //clear stuff
                } else {
                    var circlesDiv = document.getElementById("circles");
                    var circleRows = circlesDiv.children;

                    for(var i=0; i<circleRows.length; i++)
                    {
                        var rowDivs = circleRows[i].children;
                        for(var j=0; j<rowDivs.length; j++)
                        {
                            if(rowDivs[j].children.length>0){
                                rowDivs[j].children[0].children[0].setAttribute("class", "panel");
                            }
                        }
                    }
                    document.getElementById("circle"+num).setAttribute("class", "panel callout");
                    var name = document.getElementById("circle"+num).children[0].innerHTML;
                    displayCircleInformation(num, name);
                }
            }

            function displayCircleInformation(num, name)
            {
                if (window.XMLHttpRequest){
                        xmlhttp=new XMLHttpRequest();
                    }

                    xmlhttp.onreadystatechange=function()
                    {
                        if (xmlhttp.readyState==4 && xmlhttp.status==200){
                            var response = xmlhttp.responseText;
                            document.getElementById("circleInformation").innerHTML = response;
                        }
                    }


                    xmlhttp.open("POST","fetchCircleData.php",true);
                    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                    xmlhttp.send("circleID="+num+"&circleName="+name);
            }

            function openCreateCircleModal()
            {
                $(\'#createCircleModal\').foundation(\'reveal\', \'open\');
            }

            function searchCircleFriends(str)
                {
                    if (str.length==0)
                    {
                        document.getElementById("circle-dropdown").innerHTML="";
                        $(document).foundation(\'circle-dropdown\', {
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
                            document.getElementById("circle-dropdown").innerHTML=xmlhttp.responseText;
                            $(document).foundation(\'circle-dropdown\', {
                                activeClass: \'open\'
                            });
                        }
                    }
                    xmlhttp.open("GET","newCircleFriendSearch.php?input="+str,true);
                    xmlhttp.send();
                }

            function insertUsernameIntoCircle(str)
            {
                //TODO: check to make sure that username isnt already in the list
                document.getElementById("circleMembersInput").value = "";
                document.getElementById("circle-dropdown").innerHTML="";
                        $(document).foundation(\'circle-dropdown\', {
                            activeClass: \'close\'
                        });
                var newLabel = document.createElement(\'a\');
                newLabel.setAttribute("id", str+"Label");
                newLabel.setAttribute("onclick", "removeFriendFromCircleForm(\'"+str+"Label\')");
                var newSpan = document.createElement(\'span\');
                newSpan.setAttribute("class", "label");
                newSpan.innerHTML = str;
                newLabel.appendChild(newSpan);
                var fieldset = document.getElementById("circleCreateFieldset");
                fieldset.appendChild(newLabel);
            }

            function removeFriendFromCircleForm(str)
            {
                var fieldset = document.getElementById("circleCreateFieldset");
                var elementToRemove = document.getElementById(str);
                fieldset.removeChild(elementToRemove);
            }

            function createCircle(canCreate)
            {
                if(canCreate){
                    var circleName = document.getElementById(\'circleNameInput\');
                    var circleMembers = document.getElementById(\'circleCreateFieldset\'); //$(\'#messageToInput\').value;
                    var circleCreateButton = document.getElementById(\'circleCreateButton\');

                    circleCreateButton.innerHTML = "Creating...";
                    circleCreateButton.setAttribute("onclick", "createCircle(false)");// = sendNewMessage(false);

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
                    var target = circleCreateButton;
                    var spinner = new Spinner(opts).spin(target);

                    if (window.XMLHttpRequest){
                        xmlhttp=new XMLHttpRequest();
                    }

                    xmlhttp.onreadystatechange=function()
                    {
                        if (xmlhttp.readyState==4 && xmlhttp.status==200){
                            var response = xmlhttp.responseText;
                            if(response != "Circle Created"){
                                circleCreateButton.innerHTML = "Failed Create Circle";
                            } else {
                                circleCreateButton.innerHTML = response;
                                setTimeout(function(){
                                    location.reload();
                                    setTimeout(function() {
                                        $(\'#createCircleModal\').foundation(\'reveal\', \'close\');
                                        setTimeout(function(){
                                            circleName.value = "";
                                            circleMembers.value = ""; //$(\'#messageToInput\').value;
                                            circleCreateButton.innerHTML = "Create";
                                            circleCreateButton.setAttribute("onclick", "createCircle(true)");// = sendNewMessage(false);
                                        }, 400);

                                    }, 1000);
                                }, 1500);
                            }
                        }
                    }

                    var usernamesToAdd = new Array();
                    for(var i = 0; i < circleMembers.children.length; i++)
                    {
                        if(circleMembers.children[i].nodeName == "A")
                        {
                            usernamesToAdd.push(circleMembers.children[i].children[0].innerHTML);
                        }
                    }
                    xmlhttp.open("POST","createCircle.php",true);
                    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                    xmlhttp.send("circleName="+circleName.value+"&members="+JSON.stringify(usernamesToAdd));
                }
            }

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