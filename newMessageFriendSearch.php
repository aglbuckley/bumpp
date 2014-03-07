<?php

try{
    session_start();
    $input = $_GET['input'];
    $myid = $_SESSION['user_id'];
    $response = "";

    $mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        throw new Exception('Failed to connect');
    }
    if($stmt = $mysqli->prepare("SELECT user.first_name, user.last_name, user.username FROM user, friendship WHERE ((friendship.friender_id = ? AND friendship.friendee_id = user.user_id) OR (friendship.friendee_id = ? AND friendship.friender_id = user.user_id)) AND (user.first_name LIKE CONCAT('%', ?, '%') OR user.last_name LIKE CONCAT('%', ?, '%'))"))
    {
        if(!$stmt->bind_param("iiss", $myid, $myid, $input, $input))
        {
            $response = 'error';
            echo $response;
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
                    echo $response;
                    exit();
                }
            } else {
                $response = 'error';
            }

            $stmt->store_result();
            if(mysqli_stmt_num_rows($stmt)<1)
            {
                $response = '<li><a href="#">Nada</a></li>';
                echo $response;
                exit();
            }

            $i=0;
            while($stmt->fetch())
            {
                $response = $response.'<li><a href="#" onclick="insertUsername(\''.$username.'\');">'.$fname.' '.$lname.'</a></li>';
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