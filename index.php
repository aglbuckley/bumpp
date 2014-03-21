<?php
    require_once("BumppIndex.php");
    session_start();
    $index = new BumppIndex("Home", $_SESSION['username']);
?>