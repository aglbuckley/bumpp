<?php

require_once("BumppUtility.php");

$groupID = $_POST["circleID"];
$groupName = $_POST["circleName"];

$response = "";
$username = "";
$fname = "";
$lname = "";
$imageLocation = "";
$userID = -1;
$mysqli = BumppUtility::mySqlConnect();

$response = "<h1>".$groupName."</h1>";
$response = $response.'<a href="#">Add Friend</a><br><br>';

if($stmt = $mysqli->prepare("SELECT user.username, user.user_id, user.first_name, user.last_name, photo.image FROM user, photo, groupmember WHERE user.user_id = groupmember.user_id AND groupmember.group_id = ? AND photo.photo_id = user.profile_image_id"))
{
    if(!$stmt->bind_param("i", $groupID))
    {
        echo '<h1>Uh oh. Something went wrong</h1>';
        exit();
    } else {
        if($stmt->execute())
        {
            if(!$stmt->bind_result($username, $userID, $fname, $lname, $imageLocation))
            {
                echo '<h1>Uh oh!</h1>';
                exit();
            } else {
                $stmt->store_result();
                if(mysqli_stmt_num_rows($stmt)==0)
                {
                    echo '<h1>Did you select the right thing?</h1>';
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
                    $response = $response.'<small><a href="removeFriendFromCircle.php?userID='.$userID.'&groupID='.$groupID.'">Remove From Circle</a></small>';
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

?>