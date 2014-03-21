<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 3/21/14
 * Time: 11:08 AM
 */

require_once("BumppUtility.php");
class MessageUser {
    public $userID, $conversationID, $conversationMemberID, $fname, $lname, $profileImageID;
    public $messages = array();

    public function __construct($userID, $convID, $convMemID, $fname, $lname, $profID)
    {
        $this->userID = $userID;
        $this->conversationID = $convID;
        $this->conversationMemberID = $convMemID;
        $this->fname = $fname;
        $this->lname = $lname;
        $this->profileImageID = $profID;
    }

    public function addMessage($message)
    {
        array_push($this->messages, $message);
    }
}

class Message {
    public $content, $timestamp, $sender;
    public function __construct($content, $timestamp, $sender)
    {
        $this->content = $content;
        $this->timestamp = $timestamp;
        $this->sender = $sender;
    }
}

class Messaging {

    public static function FetchMessages($conversationID)
    {
        $mysqli = BumppUtility::mySqlConnect();
        $conversationMemberID = -1;
        $userID = -1;
        $fname = "";
        $lname = "";
        $imageID = -1;
        $conversationMemberIDs = array();
        $userIDs = array();
        $conversationMemberToUserId = array();
        $members = array();
        $jsonResponse = array();

        //First Retrieve all conversation members
        if($stmt = $mysqli->prepare("SELECT conversationmember.conversation_member_id, user.user_id, user.first_name, user.last_name, user.profile_image_id FROM conversationmember, user WHERE conversationmember.conversation_id = ? AND user.user_id = conversationmember.user_id"))
        {
            if(!$stmt->bind_param("i", $conversationID)){
                echo '<h1>Bind Param Error</h1>';
                $mysqli->close();
                exit();
            } else {
                if(!$stmt->execute())
                {
                    echo '<h1>Execute error.</h1>';
                    $mysqli->close();
                    exit();
                } else {
                    if(!$stmt->bind_result($conversationMemberID, $userID, $fname, $lname, $imageID))
                    {
                        echo '<h1>Uh oh!</h1>';
                        $mysqli->close();
                        exit();
                    } else {
                        $stmt->store_result();
                        if(mysqli_stmt_num_rows($stmt)==0)
                        {
                            echo '<h1>Couldn\'t retrieve members</h1>';
                            echo '<h1>Oops!</h1>';
                            $mysqli->close();
                            exit();
                        }

                        $i=0;
                        $jsonResponse['Members'] = array();
                        while($stmt->fetch())
                        {
                            $messageMember = new MessageUser($userID, $conversationID, $conversationMemberID, $fname, $lname, $imageID);
                            $memberArray = array(
                                'userID' => $userID,
                                'conversationID' => $conversationID,
                                'conversationMemberID' => $conversationMemberID,
                                'fname' => $fname,
                                'lname' => $lname,
                                'imageID' => $imageID
                            );
                            array_push($jsonResponse['Members'], $memberArray);
                            $members[$conversationMemberID] = $messageMember;
                            //array_push($members, $messageMember);
                            array_push($conversationMemberIDs, $conversationID);
                            array_push($userIDs, $userID);
                            $conversationMemberToUserId[$conversationID] = $userID;
                            $i++;
                        }
                    }
                }
            }
            $stmt->close();
        }

        //Retrieve all messages
        $content = "";
        $timestamp = "";
        $sender = -1;
        $conversation = array();
        if($stmt = $mysqli->prepare("SELECT content, timestamp, sender FROM conversationmessage WHERE conversation_id = ?"))
        {
            if(!$stmt->bind_param("i", $conversationID)){
                echo '<h1>Bind Param Error</h1>';
                $mysqli->close();
                exit();
            } else {
                if(!$stmt->execute())
                {
                    echo '<h1>Execute error.</h1>';
                    $mysqli->close();
                    exit();
                } else {
                    if(!$stmt->bind_result($content, $timestamp, $sender))
                    {
                        echo '<h1>Uh oh!</h1>';
                        $mysqli->close();
                        exit();
                    } else {
                        $stmt->store_result();
                        if(mysqli_stmt_num_rows($stmt)==0)
                        {
                            echo '<h1>Couldn\'t retrieve messages</h1>';
                            echo '<h1>Oops!</h1>';
                            $mysqli->close();
                            exit();
                        }

                        $i=0;
                        while($stmt->fetch())
                        {
                            $message = new Message($content, strtotime($timestamp), $sender);
                            $members[$sender]->addMessage($message);
                            array_push($conversation, $message);
                        }
                    }
                }
            }
        }
        $mysqli->close();

        //Display Conversation
        $response = "";
        $jsonResponse['Messages'] = array();
        foreach ($conversation as $index => $convMessage)
        {
            $message = array(
                'sender' => $convMessage->sender,
                'content' => $convMessage->content,
                'timestamp' => $convMessage->timestamp
            );
            array_push($jsonResponse['Messages'], $message);
            $member = $members[$convMessage->sender];
            $response = $response. $member->fname.' Sent Message: '.$convMessage->content.'<br>';
        }
        $json = json_encode($jsonResponse);
        //echo $json;
        return $json;
    }

    public static function FetchConversations($userID)
    {
        $mysqli = BumppUtility::mySqlConnect();
        $conversationID = 0;
        $conversations = array();
        $response = "";
        //First get all conversations
        if($stmt = $mysqli->prepare("SELECT conversationmember.conversation_id FROM conversationmember WHERE conversationmember.user_id = ?"))
        {
            if(!$stmt->bind_param("i", $userID)){
                echo '<h1>Bind Param Error</h1>';
                $mysqli->close();
                exit();
            } else {
                if(!$stmt->execute())
                {
                    echo '<h1>Execute error.</h1>';
                    $mysqli->close();
                    exit();
                } else {
                    if(!$stmt->bind_result($conversationID))
                    {
                        echo '<h1>Uh oh!</h1>';
                        $mysqli->close();
                        exit();
                    } else {
                        $stmt->store_result();
                        if(mysqli_stmt_num_rows($stmt)==0)
                        {
                            echo '<h1>Couldn\'t retrieve conversations</h1>';
                            echo '<h1>Oops!</h1>';
                            $mysqli->close();
                            exit();
                        }

                        while($stmt->fetch())
                        {
                            array_push($conversations, $conversationID);
                        }
                    }
                }
            }
            $stmt->close();
        }

        if($stmt = $mysqli->prepare("SELECT photo.image FROM photo, user, conversationmember WHERE conversationmember.conversation_id = ? AND conversationmember.user_id != ? AND user.user_id = conversationmember.user_id AND photo.photo_id = user.profile_image_id"))
        {
            for($i=0; $i<count($conversations); $i++){
                if(!$stmt->bind_param("ii", $conversations[0], $userID)){
                    echo '<h1>Bind Param Error</h1>';
                    $mysqli->close();
                    exit();
                } else {
                    if(!$stmt->execute())
                    {
                        echo '<h1>Execute error.</h1>';
                        $mysqli->close();
                        exit();
                    } else {
                        if(!$stmt->bind_result($photoLocation))
                        {
                            echo '<h1>Uh oh!</h1>';
                            $mysqli->close();
                            exit();
                        } else {
                            $stmt->store_result();
                            if(mysqli_stmt_num_rows($stmt)==0)
                            {
                                echo '<h1>Couldn\'t retrieve conversations</h1>';
                                echo '<h1>Oops!</h1>';
                                $mysqli->close();
                                exit();
                            }

                            while($stmt->fetch())
                            {
                                $response = $response.'<a class="th" id = '.$conversationID.' data-options="align:right" data-dropdown="conversation-drop" onclick="fetchConversation('.$conversations[$i].')" href="#">
                                <img src="'.$photoLocation.'"></a>';
                            }
                        }
                    }
                }
            }
            $stmt->close();
        }
        $mysqli->close();
        return $response;
    }

    public static function sendMessage($conversationID, $content)
    {
        $mysqli = BumppUtility::mySqlConnect();

        if($stmt = $mysqli->prepare("INSERT INTO conversationmessage (sender, conversation_id, content) SELECT conversationmember.conversation_member_id as sender, ? as conversation_id, ? as content FROM conversationmember WHERE user_id = ? AND conversation_id = ?"))
        {
            if(!$stmt->bind_param("isii", $conversationID, $content, $_SESSION['user_id'], $conversationID))
            {
                $response = "error";
                echo $response;
                exit();
            } else {
                if(!$stmt->execute())
                {
                    $response = "error";
                    echo $response;
                    exit();
                }
            }
            BumppUtility::Log(" sent message '".$content."'.");
            $stmt->close();
        }
        $mysqli->close();
        header("Location: ./");
    }

} 