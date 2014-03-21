<?php

require_once("BumppUtility.php");
$friendshipID = $_GET['friendshipID'];

$mysqli = BumppUtility::mySqlConnect();

if($stmt = $mysqli->prepare("UPDATE friendship SET accepted = 1 WHERE friendship_id = ?"))
{
    if(!$stmt->bind_param("i", $friendshipID))
    {
        echo '<h1>Bind error</h1>';
        $stmt->close();
        $mysqli->close();
        exit();
    } else {
        if(!$stmt->execute())
        {
            echo '<h1>Execute error</h1>';
            $stmt->close();
            $mysqli->close();
            exit();
        }
    }
}
$stmt->close();
$mysqli->close();
header("Location: ./");

?>