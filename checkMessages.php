<?php

try{

    $response = "";
    $allConversationToMemberIDs = array();
    $allConversations = array();
    $allConversationMemberIDs = array();

    $mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        throw new Exception('Failed to connect');
    }

    //First get all conversations that I am a part of
    if($stmt = $mysqli->prepare("SELECT conversation_id, conversation_member_id FROM conversationmember WHERE user_id = ?"))
    {
        if(!$stmt->bind_param("i", $_SESSION['user_id']))
        {
            echo 'error on bind';
            exit();
        }

        if($stmt->execute())
        {
            $convID = -1;
            $convMemID = -1;
            if(!$stmt->bind_result($convID, $convMemID))
            {
                echo 'error on result bind'
                exit();
            }

            while($stmt->fetch())
            {
                $allConversationToMemberIDs[$convID] = $convMemID;
                //array_push($allConversations, $convID);
                //array_push($allConversationMemberIDs, $convMemID);
            }
        }
        $stmt->close();
    }

    //Check if any of those conversations have messages that have not been received
    if(count($allConversationToMemberIDs) > 0)
    {
        if($stmt = $mysqli->prepare("SELECT conversationmessage.conversation_id, conversationmessage.sender, conversationmessage.content, user.first_name, user.last_name, user.username FROM conversationmessage, user WHERE "))
    } else {
        echo 'No Conversations';
        exit();
    }

    //If so, get the conversation, messages, and names of senders

    if($stmt = $mysqli->prepare("SELECT first_name, last_name, username FROM user WHERE first_name LIKE CONCAT('%', ?, '%') OR last_name LIKE CONCAT('%', ?, '%')"))
    {
        if(!$stmt->bind_param("ss", $input, $input))
        {
            $response = 'error';
            exit();
        } else {
            if($stmt->execute())
            {
                $fname = "";
                $lname = "";
                $username = "";
                if(!$stmt->bind_result($fname, $lname, $username))
                {
                    $reponse = 'error';
                    exit();
                }
            } else {
                $response = 'error';
            }

            $stmt->store_result();
            if(mysqli_stmt_num_rows($stmt)<1)
            {
                $response = '<li><a href="#">Nada</a></li>';
                exit();
            }

            $i=0;
            while($stmt->fetch())
            {
                $response = $response.'<li><a href="/users/'.$username.'">'.$fname.' '.$lname.'</a></li>';
                $i++;
            }

            $stmt->close();
        }
        echo $response;
    } else {
        echo 'prepare error';
    }

    $mysqli->close();
} catch(Exception $e) {
    echo '<h1>Uh oh!</h1>';
    echo '<h3 class="subheader">Something went wrong. Please reload the page.</h3>';
    echo $e;
    $mysqli->close();
    //exit();
}

?>