<?php
session_start();
require_once("BumppUtility.php");

$email = $_POST['newEmail'];
$username = $_POST['newUsername'];
$phone = $_POST['newPhone'];
$birthday = $_POST['newBirthday'];
$location = $_POST['newLocation'];
$columns = array();
$columns2 = array();
$query = "";
$suffix = "";
$query2 = "";
$suffix2 = "";
$mysqli = BumppUtility::mySqlConnect();

//File Upload Stuff
if($_FILES["newProfilePhoto"]["error"] != UPLOAD_ERR_NO_FILE)
{
    $types = array("jpeg", "jpg", "JPG", "png");
    $list = explode(".", $_FILES["newProfilePhoto"]["name"]);
    $exts = end($list);
    if ((($_FILES["newProfilePhoto"]["type"] == "image/jpeg")
            || ($_FILES["newProfilePhoto"]["type"] == "image/jpg")
            || ($_FILES["newProfilePhoto"]["type"] == "image/pjpeg")
            || ($_FILES["newProfilePhoto"]["type"] == "image/x-png")
            || ($_FILES["newProfilePhoto"]["type"] == "image/png"))
        && in_array($exts, $types)){
        if ($_FILES["newProfilePhoto"]["error"] > 0)
        {
            echo "Error: ".$_FILES["newProfilePhoto"]["error"];
        }
        else
        {
            if (file_exists("images/profilepictures/".$_FILES["newProfilePhoto"]["name"]))
            {
                echo $_FILES["newProfilePhoto"]["name"]." already exists.";
            }
            else
            {
                $filename = hash("sha1", $_FILES["newProfilePhoto"]["name"]).".".strtolower($exts);
                $fileLocation = "images/profilepictures/".$filename;
                move_uploaded_file($_FILES["newProfilePhoto"]["tmp_name"], $fileLocation);
                if($stmt = $mysqli->prepare("INSERT INTO photo (owner_id, image) VALUES (?, ?)"))
                {
                    if($stmt->bind_param("is", $_SESSION['user_id'], $fileLocation))
                    {
                        if(!$stmt->execute()){
                            echo $stmt->error();
                            echo $mysqli->error();
                            $stmt->close();
                            $mysqli->close();
                            exit();
                        } else {
                            $insertID = $mysqli->insert_id;
                            if($stmt = $mysqli->prepare("UPDATE user SET profile_image_id = ? WHERE user_id = ?"))
                            {
                                if($stmt->bind_param("ii", $insertID, $_SESSION['user_id']))
                                {
                                    if(!$stmt->execute())
                                    {
                                        echo $stmt->error();
                                        echo $mysqli->error();
                                        $stmt->close();
                                        $mysqli->close();
                                        exit();
                                    } else {
                                        $_SESSION['profile_image_location'] = $fileLocation;
                                        $_SESSION['profile_image_id'] = $insertID;
                                        BumppUtility::Log(" updated profile picture.", true);
                                    }
                                }
                            }
                        }
                    }
                }
                $stmt->close();
            }
        }
    } else {
        echo "File not allowed";
        exit();
    }
}

//Check form data
if(isset($email) && $email != "" && filter_var($email, FILTER_VALIDATE_EMAIL)){
    $columns['email'] = "'".$email."'";
}
if(isset($username) && $username != ""){
    $columns['username'] = $username;
}
if(isset($phone) && $phone != ""){
    $columns2['phoneNumb'] = $phone;
}
if(isset($birthday) && $birthday != ""){
    $bd = strtotime($birthday);

    $newBirthday = date('Y-m-d',$bd);

    $columns2['dob'] = "'".$newBirthday."'";
}
if(isset($location) && $location != ""){
    $columns2['currentLoc'] = "'".$location."'";
}

if(count($columns)>0){
    $query = "UPDATE user SET ";
    $suffix = "WHERE user_id = ?";
}

//Build query
$count = 0;
foreach ($columns as $key => $value) {
    $query = $query.$key.' = '.$value.' ';
    if($count<count($columns)-1){
        $query = $query.', ';
    }
    $count++;
}

$query = $query.$suffix;

if(count($columns2)>0){
    $query2 = "UPDATE userinformation SET ";
    $suffix2 = "WHERE user_id = ?";
}

$count = 0;
foreach ($columns2 as $key => $value) {
    $query2 = $query2.$key.' = '.$value.' ';
    if($count<count($columns2)-1){
        $query2 = $query2.', ';
    }
    $count++;
}
$query2 = $query2.$suffix2;

//Make sql queries
if($query != ""){
    if($stmt = $mysqli->prepare($query))
    {
        if($stmt->bind_param("i", $_SESSION['user_id']))
        {
            if(!$stmt->execute()){
                echo "Execute error";
                echo $stmt->error;
                echo $mysqli->error;
                $mysqli->close();
                exit();
            }
        } else {
            echo "Bind error";
            $mysqli->close();
            exit();
        }
    } else {
        echo $mysqli->error;
        echo $stmt->errorno;
    }
    $stmt->close();
    //$mysqli->close();
}

if($query2 != ""){
    if($stmt = $mysqli->prepare($query2))
    {
        if($stmt->bind_param("i", $_SESSION['user_id']))
        {
            if(!$stmt->execute()){
                echo "Execute error";
                echo $stmt->error;
                echo $mysqli->error;
                $mysqli->close();
                exit();
            }
        } else {
            echo "Bind error";
            $mysqli->close();
            exit();
        }
    } else {
        echo $mysqli->error;
        echo $stmt->errorno;
    }
    $stmt->close();
    //$mysqli->close();
}
if($query != "" || $query2 != ""){
    BumppUtility::Log(" made profile changes.", true);
}
if(isset($email) && filter_var($email, FILTER_VALIDATE_EMAIL)){
    $_SESSION['email'] = $email;
    BumppUtility::Log(" updated email address.", true);
}
if(isset($username) && $username!=""){
    $_SESSION['username'] = $username;
    BumppUtility::Log(" updated username.", true);
}
if(isset($phone) && $phone != ""){
    BumppUtility::Log(" updated phone number.", true);
}
if(isset($birthday) && $birthday != ""){
    BumppUtility::Log(" updated birthday.", true);
}
if(isset($location) && $location != ""){
    BumppUtility::Log(" updated location.", true);
}

$mysqli->close();
header('Location: ./');

?>