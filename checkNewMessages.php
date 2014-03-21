 <?php
				 
header('Content-Type: application/json');
				 
$conversation_id = $_GET['c_id'];
$sender = $_GET['sender'];

 
    
$mysqli=mysqli_connect("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

if (mysqli_connect_errno()) {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$sql = "SELECT * FROM conversationmessage WHERE conversation_id = ".$conversation_id ." AND sender != " .$sender." AND received = 0 LIMIT 1";
			
$result = mysqli_query($mysqli,$sql);

$checkArray = array();		    
while($tableRow=mysqli_fetch_array($result)){
     $checkArray[] = $tableRow; 
}

echo json_encode($checkArray);


$mysqli->close();


?> 
				