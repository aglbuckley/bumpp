<?php
    require_once("BumppUtility.php");
    require_once('BumppIndex.php');
    $userpage = new BumppIndex('User', $_GET['username']);
    BumppUtility::Log("visited ".$_GET['username']);
?>