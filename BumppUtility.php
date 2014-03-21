<?php
/**
 * BumppUtility class.
 * User: andrew
 * Date: 3/15/14
 * Time: 1:19 PM
 */

session_start();

class BumppUtility {

    public static function mySqlConnect()
    {
        $mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

        if ($mysqli->connect_errno) {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            $mysqli->close();
        }
        return $mysqli;
    }

    public static function Log($stringToLog, $public=false)
    {
        //Open up the file
        $logFile = '/users/'.$_SESSION['user_id'].'/logFile.txt';

        if(!file_exists($logFile))
        {
            $handle = fopen($logFile, 'w') or die('Couldn\'t open the file: '.$logFile);
        }

        $fileHandle = fopen($logFile, 'a') or die('Couldn\'t open the file: '.$logFile);
        $timeStamp = date('Y-m-d H:i:s');
        $fileData = $timeStamp."\t||\t".$_SESSION['user_id']." (".$_SESSION['fname']." ".$_SESSION['lname'].") ".$stringToLog."\n";
        fwrite($fileHandle, $fileData);
        fclose($fileHandle);

        if($public){
            $logFilePublic = 'users/'.$_SESSION['user_id'].'/logFilePub.txt';

            if(!file_exists($logFilePublic))
            {
                $handle = fopen($logFilePublic, 'w') or die('Couldn\'t open the file: '.$logFilePublic);
            }

            $fileHandle = fopen($logFilePublic, 'a') or die('Couldn\'t open the file: '.$logFilePublic);
            fwrite($fileHandle, $fileData);
            fclose($fileHandle);
        }
//
//        if($fileData = file_get_contents($logFile))
//        {
//            $timeStamp = date('d-m-Y H:i:s');
//            $fileData = $fileData.'\n'.$timeStamp.'\t\t||\t'.$_SESSION['user_id'].' ('.$_SESSION['fname'].' '.$_SESSION['lname'].') '.$stringToLog;
//            file_put_contents($logFile, $fileData);
//        }
    }

    public static function FetchLogContents($public=false, $user_id)
    {
        if(!$public){
            $logFile = '/users/'.$user_id.'/logFile.txt';
        } else {
            $logFile = '/users/'.$user_id.'/logFilePub.txt';
        }
        /*$handle = fopen($logFile, 'r');
        $contents = "";
        while(!feof($handle))
        {
            $contents = $contents.fgets($handle)."\n";
        }
        fclose($handle);
        return $contents;*/
        return file($logFile);
    }

    public static function RetrieveProfilePic($userID, $imageID=-1)
    {
        $mysqli = BumppUtility::mySqlConnect();
        $imageString = "";
        if($imageID>0){
            if($stmt = $mysqli->prepare("SELECT image FROM photo WHERE photo_id=?"))
            {
                if(!$stmt->bind_param("i", $imageID))
                {
                    echo "Binding failed: (".$stmt->errno.")".$stmt->error;
                    exit();
                }

                if(!$stmt->execute()){
                    echo "Execute failed: (".$stmt->errno.")".$stmt->error;
                    exit();
                }

                $stmt->bind_result($imageString);

                $result = $stmt->fetch();

                if ($result==null){
                    echo 'No Image!';
                    exit();
                }
                $stmt->close();
            }
        } else {
            if($stmt = $mysqli->prepare("SELECT photo.image FROM user, photo WHERE photo.photo_id = user.profile_image_id AND user.user_id = ?"))
            {
                if(!$stmt->bind_param("i", $userID))
                {
                    echo "Binding failed: (".$stmt->errno.")".$stmt->error;
                    exit();
                }

                if(!$stmt->execute()){
                    echo "Execute failed: (".$stmt->errno.")".$stmt->error;
                    exit();
                }

                $stmt->bind_result($imageString);

                $result = $stmt->fetch();

                if ($result==null){
                    echo 'No Image!';
                    exit();
                }
                $stmt->close();
            }
        }
        $mysqli->close();

        return $imageString;
    }
} 