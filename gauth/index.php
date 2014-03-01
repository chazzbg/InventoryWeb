<?php
require_once 'src/Google_Client.php';
require_once 'src/contrib/Google_Oauth2Service.php';
session_start();
$client= new Google_Client();
$client->setApplicationName('Ingress Inventory');

$oauth = new Google_Oauth2Service($client);
if(isset($_GET['code'])){
	try {
	$client->authenticate($_GET['code']);
	
	} catch (Exception $e){
		echo $e->getMessage();
	}
	
	$_SESSION['token'] = $client->getAccessToken();
	var_dump($oauth->userinfo->get());



	$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
//	header('Location: '.filter_var($redirect, FILTER_SANITIZE_URL));
	exit;
}
if (isset($_SESSION['token'])) {
	$client->setAccessToken($_SESSION['token']);
}

if(isset($_REQUEST['logout'])){
	unset($_SESSION['token']);
	
	$client->revokeToken();
}

if($client->getAccessToken()){
	

	$user = $oauth->userinfo->get();
	
	$email= $user['email'];
	$img = $user['picture'];
	$personMarkup = $email.'<div><img src="'.$img.'?sz=50"></div>';
	$_SESSION['token'] = $client->getAccessToken();
} else {
	$authUrl = $client->createAuthUrl();
}

if(isset($personMarkup)):
	print $personMarkup;
	endif;
	
	
if(isset($authUrl)) {
    print "<a class='login' href='$authUrl'>Connect Me!</a>";
  } else {
   print "<a class='logout' href='?logout'>Logout</a>";
  }
  
  var_dump($_SESSION);

?>
