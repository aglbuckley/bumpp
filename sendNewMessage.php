<?php

try{
    session_start();
    require_once("BumppUtility.php");
    $to = $_POST['to'];
    $messageName = $_POST['messageName'];
    $content = $_POST['content'];

    $numTo = 1;
    $response = "";
    $toID = -1;
    //$insertID = 0;

    $recipients = array($_SESSION['user_id']);

    $mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        throw new Exception('Failed to connect');
    }
    if($stmt = $mysqli->prepare("INSERT INTO conversation (name) VALUES (?)"))
    {
        if(!$stmt->bind_param("s", $messageName))
        {
            $response = 'error1';
            echo $reponse;
            exit();
        } else {
            if($stmt->execute())
            {
                $insertID = $mysqli->insert_id;

                if($stmt = $mysqli->prepare("SELECT user_id FROM user WHERE username = ?"))
                {
                    if(!$stmt->bind_param("s", $to))
                    {
                        $response = 'error2';
                        echo $response;
                        exit();
                    } else {
                        if($stmt->execute())
                        {
                            if(!$stmt->bind_result($toID))
                            {
                                $reponse = 'error3';
                                echo $response;
                                exit();
                            }
                            $stmt->fetch();
                            array_push($recipients, $toID);
                            $stmt->close();
                        } else {
                            $response = 'error4';
                        }
                    }
                }

                $senderConvoId = -1;

                if($stmt = $mysqli->prepare("INSERT INTO conversationmember (conversation_id, user_id) VALUES (?, ?)"))
                {
                    $ID = $recipients[0];
                    if(!$stmt->bind_param("ii", $insertID, $ID))
                    {
                        $response = 'error5';
                        echo $response;
                        exit();
                    } else {
                        for($i=0; $i<($numTo+1); $i++){
                            $ID = $recipients[$i];
                            if(!$stmt->execute()){
                                echo $ID;
                                $response = 'error6';
                                echo $response;
                                exit();
                            }
                            if($i==0)
                                $senderConvoId = $mysqli->insert_id;
                        }
                        $stmt->close();
                    }
                } else {
                    echo 'prepare error '.$mysqli->error;
                    exit();
                }

                if($stmt = $mysqli->prepare("INSERT INTO conversationmessage (sender, conversation_id, content) VALUES (?, ?, ?)"))
                {
                    $sender = $recipients[0];
                    if(!$stmt->bind_param("iis", $senderConvoId, $insertID, $content))
                    {
                        $response = 'error7';
                        echo $response;
                        exit();
                    } else {
                        if(!$stmt->execute()){
                            $response = 'error8 '.$stmt->error;
                            echo $response;
                            exit();
                        }
                        BumppUtility::Log("sent message '".$content."' to ".$to);
                        $response = 'Message Sent';
                    }
                }

            } else {
                $response = 'error9';
                echo $response;
                exit();
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