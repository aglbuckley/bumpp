<?php

require_once("Messaging.php");
Messaging::sendMessage($_POST['conversationID'], $_POST['message']);

?>