<?php
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);
header('Content-Type: application/json');

$username = $_GET['username'];
$conversationIDArray = array();


 
$mysqli=mysqli_connect("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

if (mysqli_connect_errno()) {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$sql = "SELECT * FROM conversationmember WHERE user_id = (
        SELECT user_id FROM user WHERE username = '".$username."')";
$result = mysqli_query($mysqli,$sql);


while($tableRow=mysqli_fetch_assoc($result)){
   $conversationIDArray[] = $tableRow;

}

echo json_encode($conversationIDArray);

$mysqli->close();

?>