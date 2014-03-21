<?php

require_once("BumppUtility.php");

try{
    session_start();
    $circleName = $_POST['circleName'];
    $members = json_decode($_POST['members']);
    $response = "";
    $mysqli = BumppUtility::mySqlConnect();

    if($stmt = $mysqli->prepare("INSERT INTO groups (name, owner_id) VALUES (?, ?)"))
    {
        if(!$stmt->bind_param("ss", $circleName, $_SESSION['user_id']))
        {
            $response = 'error1';
            echo $response;
            exit();
        } else {
            if($stmt->execute())
            {
                $insertID = $mysqli->insert_id;

                array_push($members, $_SESSION['username']);

                $username = $_SESSION['username'];
                if($stmt = $mysqli->prepare("INSERT INTO groupmember (group_id, user_id) SELECT ? as group_id, user_id as user_id FROM user WHERE username = ?"))
                {
                    if(!$stmt->bind_param("is", $insertID, $username))
                    {
                        $response = "error";
                        echo $response;
                        exit();
                    } else {
                        for($i = 0; $i < count($members); $i++)
                        {
                            $username = $members[$i];
                            if(!$stmt->execute())
                            {
                                $response = "error";
                                echo $response;
                                exit();
                            }
                            $response = 'Circle Created';
                        }
                        BumppUtility::Log("created circle '".$circleName."'.");
                        $stmt->close();
                    }
                } else {
                    echo 'prepare error';
                    exit();
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