<?php
define('_VERSION_', '207');
require 'config.php';
require 'functions.php';
require_once 'gauth/src/Google_Client.php';
require_once 'gauth/src/contrib/Google_Oauth2Service.php';
session_start();
require 'API.php';
$force_sync = false;
$client = new Google_Client();
$client->setApplicationName('Ingress Inventory');

$client->setRedirectUri('http://localhost/ingress/index.php');

$oauth = new Google_Oauth2Service($client);
if (isset($_GET['code'])) {
	$client->authenticate($_GET['code']);
	if ($token = $client->getAccessToken()) {
		$user = $oauth->userinfo->get();
		$email = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
		$res = mysql_query('SELECT * FROM users WHERE email = \'' . $email . '\'');
		

		$sess_id = session_id();
		if (mysql_num_rows($res)==0) {

			$ins = mysql_query('INSERT INTO users (email,last_login) VALUES (\'' . $email . '\', NOW())  ')or die(mysql_error());
			if ($ins)
				$res = mysql_query('INSERT INTO sessions (id_user, sess_id, last_login, token) VALUES (' . (int) mysql_insert_id() . ',  \'' . $sess_id . '\' ,NOW(), \'' . $token . '\')')or die(mysql_error());
		} else {
			$row = mysql_fetch_assoc($res);
			mysql_query('INSERT INTO sessions (id_user, sess_id, last_login, token) VALUES (' . (int) $row['id_user'] . ',  \'' . $sess_id . '\' ,NOW(), \'' . $token . '\')');
		}
	}
	//header('Location: index.php');
}

$inventoryapp = 'Inventory_v2.0.1.apk';
if (!isset($_COOKIE['PHPSESSID'])) { // clean hit 
	$sessid = session_id();
	setcookie('PHPSESSID', $sessid, time() + (365 * 24 * 60 * 60), '/');
	$authUrl = $client->createAuthUrl();
	$page = 'login';
} else if (!preg_match('/^[a-zA-Z0-9]+$/', $_COOKIE['PHPSESSID'])) { // hacking attempt
	$sessid = session_id();
	setcookie('PHPSESSID', $sessid, time() + (365 * 24 * 60 * 60), '/');
	$authUrl = $client->createAuthUrl();
	$page = 'login';
} else { // logged user
	$res = mysql_query('SELECT * FROM sessions s 
	JOIN users u ON (u.id_user = s.id_user)		
	WHERE s.sess_id = \'' . $_COOKIE['PHPSESSID'] . '\' ORDER BY id_session DESC LIMIT 1');
	
	// if session exists everything is ok 
	if ($res AND mysql_num_rows($res) == 1) {
		$row = mysql_fetch_assoc($res);

		setcookie('is_loged', "true", time() + (365 * 24 * 60 * 60), '/');
		$_SESSION = $row;
		$_SESSION['logged'] = true;
	} else { // else go to login page
		$authUrl = $client->createAuthUrl();
		$page = 'login';
	}
}





if (isSubmit('logout')) {

	mysql_query('DELETE FROM sessions WHERE sess_id = \'' . $_SESSION['sess_id'] . '\' LIMIT 1');


	session_destroy();
	unset($_SESSION);
	unset($_COOKIE);
	$client->revokeToken();
	header('Location: index.php');
	exit;
}
if (!isset($_SESSION['logged'])) {
	$authUrl = $client->createAuthUrl();
	$page = 'login';
} else {
	if (isset($_GET['page'])) {
		switch ($_GET['page']) {
			case 'resonators':
				$page = 'resonators';
				break;
			case 'weapons':
				$page = 'weapons';
				break;
			case 'mods':
				$page = 'mods';
				break;
			case 'cubes':
				$page = 'cubes';
				break;
			case 'keys':
				$page = 'keys';
				break;
			case 'media':
				$page = 'media';
				break;
			case 'reauth':
				$page = 'reauth';
				break;
			case 'tools':
				$page = 'tools';
				break;
			case 'badges':
				$page ='badges';
				break;
		}
	} else {
		$page = 'home';
	}
}

date_default_timezone_set('Europe/Athens');
require 'header.php';
require $page . '.php';
require 'footer.php';
?>
