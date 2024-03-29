<?php
require_once("BumppUtility.php");
//Code taken and adapted from the php manual
//URL: http://uk3.php.net/session_destroy

// Initialize the session.
// If you are using session_name("something"), don't forget it now!
session_start();

//Cloes the mysqli connection manually. Not sure if this is necessary
if(isset($_SESSION['mysqli']))
{
	$mysqli = $_SESSION['mysqli'];
	$mysqli->close();
	$mysqli = null;
}
BumppUtility::Log("logged out");
// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();
header('Location: login.php');

?>