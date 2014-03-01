<?php
require 'config.php';
require 'functions.php';
session_start();
require 'API.php';
require 'kml.class.php';
if (!isset($_COOKIE['PHPSESSID'])) {
	die();
} else {

	$res = mysql_query('SELECT * FROM sessions s 
	JOIN users u ON (u.id_user = s.id_user)		
	WHERE s.sess_id = \'' . $_COOKIE['PHPSESSID'] . '\'');
	


	if (!@mysql_numrows($res)) {
		die();
	} else {
		
		$_SESSION = mysql_fetch_assoc($res);
		$_SESSION['logged'] = true;
		$force_sync = false;
		require 'datafetch.php';
		$keys = $player_inventory['PORTAL_LINK_KEY'];
		
		//var_dump($keys);
		$i=1;
		$kml = new KML($player_data['name']. ' portal keys');

		$document = new KMLDocument('myportals', $player_data['name']. ' portal keys',@date('Y-m-d H:m:s'), 'Portal keys list of '.$player_data['name']);
		foreach ($keys as $k){
			$style = new KMLStyle('style'.$i);
			$style->setIconStyle('http://inventory.worldofchazz.net/img/portal_marker_sq.png','','normal',0.5,true, 17,2);
			$document->addStyle($style);
			$placeMark = new KMLPlaceMark('', $k['title'].' ['.$k['count'].']', $k['address'],$k['address'].'<img src="'.$k['image'].'" />');
			$placeMark->setGeometry(new KMLPoint($k['location']['long'], $k['location']['lat']));
			$placeMark->setStyleUrl('#style'.$i);
			$document->addFeature($placeMark);
			
			$i++;
		}
		$kml->setFeature($document);
	    $kml->output('A',$player_data['name'].'_keys_'.@date('Y-m-d_H:m:s').'.kml');
	}
}
