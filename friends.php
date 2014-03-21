<?php
session_start();
require_once("FriendsPage.php");
$id = 0;
if(!isset($_GET['id'])){
    $id = $_SESSION['user_id'];
}
$page = new FriendsPage($id);
?>