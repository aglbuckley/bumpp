<?php

require_once("Messaging.php");
echo Messaging::FetchMessages($_GET['messageID']);

?>