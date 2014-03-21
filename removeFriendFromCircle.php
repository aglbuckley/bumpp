<?php

require_once("BumppUtility.php");

$userID = $_GET['userID'];
$groupID = $_GET['groupID'];
$username = "";
$groupName = "";

$mysqli = BumppUtility::mySqlConnect();
if($stmt = $mysqli->prepare("SELECT user.username, groups.name FROM user, groups WHERE user.user_id = ? AND groups.group_id = ?"))
{
    if(!$stmt->bind_param("ii", $userID, $groupID))
    {
        echo '<h1>Uh oh. Something went wrong</h1>';
        exit();
    } else {
        if(!$stmt->execute())
        {
            echo '<h1>Uh oh. Something went wrong</h1>';
            exit();
        } else {
            if(!$stmt->bind_result($username, $groupName))
            {
                echo '<h1>Error on result bind</h1>';
                exit();
            } else {
                $stmt->fetch();
                $stmt->close();
            }
        }
    }
}

if($stmt = $mysqli->prepare("DELETE FROM groupmember WHERE user_id = ? AND group_id = ?"))
{
    if(!$stmt->bind_param("ii", $userID, $groupID))
    {
        echo '<h1>Uh oh. Something went wrong</h1>';
        exit();
    } else {
        if(!$stmt->execute())
        {
            echo '<h1>Uh oh. Something went wrong</h1>';
        } else {
            $stmt->close();
            BumppUtility::Log("removed ".$username." from ".$groupName);
            $mysqli->close();
            header('Location: circles.php');
        }
    }
}

?>
