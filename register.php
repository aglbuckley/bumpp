<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="css/foundation.css" />
    	<script src="js/vendor/modernizr.js"></script>
	</head>
	
	<body>
		<nav class="top-bar" data-topbar> 
			<ul class="title-area"> 
				<li class="name"> 
					<h1><a href="./">bumpp</a></h1> 
				</li> 
				<li class="toggle-topbar menu-icon"><a href="#">Menu</a></li> 
			</ul> 
		
			<section class="top-bar-section"> 
				<!-- Right Nav Section --> 
				<ul class="right"> 
					<li class="has-dropdown"> 
						<a href="#">Settings</a> 
						<ul class="dropdown"> 
							<li><a href="login.html">Login</a></li> 
						</ul> 
					</li> 
				</ul> 
			
				<!-- Left Nav Section --> 
				<ul class="left"> 
					<li><a href="login.html">Login</a></li>
				</ul> 
			</section> 
		</nav>
		<section role="main">
			<div class="row">
				<?php
                require_once("BumppUtility.php");
				require_once 'mandrill-api-php/src/Mandrill.php';
				error_reporting(E_ALL);
				ini_set('display_errors',1);
				$fname = "";
				$lname = "";
				$email = "";
				$password = "";
				$user_id=-1;

				//Eventually check to see if the user is already registered.

				if(isset($_POST['first_name']) && !empty($_POST['first_name']) && isset($_POST['last_name']) && !empty($_POST['last_name']) && isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['password']) && !empty($_POST['password']))
				{
					$fname = $_POST['first_name'];
					$lname = $_POST['last_name'];
					$email = $_POST['email'];
					$password = $_POST['password'];
				} else {
					header('Location: login.html');
					exit();
				}

				//$mysqli = new mysqli("localhost", "root", "root", "main_test_db");
				$mysqli = new mysqli("eu-cdbr-azure-north-b.cloudapp.net", "b4076f65ff0228", "50c893e0", "bumppAdwhDiig5M6");

				if ($mysqli->connect_errno) {
					echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
					exit();
				}

				session_start();
				$salt = uniqid(mt_rand(), true);
				$salt = substr($salt,0,23);
				$hash = hash('sha512', $password.$salt);
				//possibly change later
				$verification = md5(rand(0,1000));
				$verified = 0;
				$profileImageID = 1;

				$generatedUsername = strstr($email, '@', true).rand(0,100);

				$stmt = $mysqli->prepare("INSERT INTO user (first_name, last_name, email, username, password, salt, profile_image_id, verification, verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
				
				if(!$stmt->bind_param("ssssssisi", $mysqli->real_escape_string($fname), $mysqli->real_escape_string($lname), $mysqli->real_escape_string($email), $generatedUsername, $hash, $salt, $profileImageID, $mysqli->real_escape_string($verification), $verified))
				{
					echo "Binding failed: (".$stmt->errno.") ".$stmt->error;
					exit();
				}

				if ($stmt->execute()) {

					$stmt = $mysqli->prepare("SELECT user_id FROM user WHERE email = ? AND salt = ?");
		
					if(!$stmt->bind_param("ss", $mysqli->real_escape_string($email), $salt))
					{
						echo '<h1>Error on second select bind</h1>';
						exit();
			
					} else {
						if($stmt->execute()){
							$stmt->bind_result($user_id);
			
						} else {
							echo '<h1>Error on execute</h1>';
							exit();
						}
		
						$stmt->fetch();
				
						$stmt->close();
				
						$stmt = $mysqli->prepare("INSERT INTO blog (name, user_id) VALUES (?, ?)");	
		
						$blog_name = $mysqli->real_escape_string($fname.'\'s Blog');

						if(!$stmt->bind_param("ss", $blog_name, $user_id))
						{
							echo '<h1>Error on insert bind</h1>';
							exit();
						} else {
							if(!$stmt->execute()){
								echo '<h1>Error on execution of insert</h1>';
								exit();
							}
						}

                        $stmt->close();

                        if($stmt = $mysqli->prepare("INSERT INTO userinformation (dob, currentloc, phoneNumb, user_id) VALUES (?, ?, ?, ?)"))
                        {
                            $dob = date('Y-m-d');

                            $currentloc = "London, UK";
                            $phoneNumb = "0";
                            if(!$stmt->bind_param("sssi", $dob, $currentloc, $phoneNumb, $user_id)){
                                echo '<h1>Error on bind</h1>';
                                $stmt->close();
                                $mysqli->close();
                                exit();
                            } else {
                                if(!$stmt->execute()){
                                    echo '<h1>Error on execution of insert</h1>';
                                    $stmt->close();
                                    $mysqli->close();
                                    exit();
                                }
                            }
                        }
					}

                    BumppUtility::Log("registered");
					header('Location: http://bumpphelper.cloudapp.net/sendEmail.php?email='.$email.'&first_name='.$fname.'&last_name='.$lname);
					/*$req = new HttpRequest('http://bumpphelper.cloudapp.net/sendEmail.php', HttpRequest::METH_POST);
					$req->addPostFields(array('first_name' => $fname, 'last_name' => $lname, 'email' => $email, 'verification' => $verification));
					try {
						echo $req->send()->getBody();
					} catch (HttpException $ex) {
						echo $ex;
					}*/

					
					//header('Location: http://bumphelper.cloudapp.net/sendEmail.php');
					//session_destroy();
					//exit();
					
					//Send Verification Email
					/*$to = $email;
					$subject = 'Welcome to bumpp, '.$fname.'!';
					$message = '

					Welcome to bumpp, '.$fname.'!
					We have successfully created an account for you. All you have to do is verify you account by clicking the link below and you will be good to go!

					http://bumpp.azurewebsites.net/verifyAccount.php?email='.$email.'&verification='.$verification.'

					See you soon!
					';			

					$headers = 'From: noreply@bumpp.azurewebsites.net'."\r\n".'Reply-To: '.$inserted_text_email."\r\n".'X-Mailer: PHP/'.phpversion();

					if(mail($to, $subject, $message, $headers)){
						echo '<h1>Check your email!</h1>';
						echo '<h2 class="subheader">Please verify your account by clicking on the verification link we sent you</h2>';
					} else {
						echo '<h1>Uh oh!</h1>';
						echo '<h2 class="subheader">Something went wrong with the email</h2>';
					}*/

				} else {
				   echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
				}
				session_destroy();
				?>
			</div>
		</section>

		<script src="js/vendor/jquery.js"></script>
    	<script src="js/foundation.min.js"></script>
    	<script src="js/foundation/foundation.abide.js"></script>
    	<script>
    	  $(document).foundation();
   		</script> 
	</body>
</html>