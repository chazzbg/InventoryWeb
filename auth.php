<?php
require 'config.php';
require 'functions.php';
session_start();
require 'API.php';
require 'kml.class.php';
if (!isset($_COOKIE['PHPSESSID']) OR (isset($_COOKIE['PHPSESSID']) AND count($_SESSION) ==0)) {
	header("Location:index.php");
} else {

	mysql_query('UPDATE users SET sacsid = \'' . getValue('SACSID'). '\' , last_login = NOW() WHERE id_user = ' . (int)$_SESSION['id_user'] . ' LIMIT 1');
	header('Location: index.php?forcesync');
}


?>
