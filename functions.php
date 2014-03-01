<?php
function isEmail($email)
{
	return !empty($email) && preg_match(cleanNonUnicodeSupport('/^[a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z0-9]+$/ui'), $email);
}

function isPasswd($passwd, $size = 5)
{
	return (strlen($passwd) >= $size && strlen($passwd) < 255);
}
function  isSACSID($secsid){
	
	
	return preg_match('/^[a-zA-Z0-9-_]+$/', $secsid) AND is_int(stripos($secsid,'AJKiYc')) AND stripos($secsid,'AJKiYc') ==0;
}

function  isRegCode($code){
	
	
	return preg_match('/^[a-zA-Z0-9]+$/', $code) AND strlen($code)==7;
}

function isPasscode($code){
	return preg_match('/^[a-zA-Z0-9]+$/', $code);
}

function isGuid($guid) {
	return preg_match('/^[0-9a-f.]+$/', $guid);
}
function cleanNonUnicodeSupport($pattern)
	{
		if (!defined('PREG_BAD_UTF8_OFFSET'))
			return $pattern;
		return preg_replace('/\\\[px]\{[a-z]\}{1,2}|(\/[a-z]*)u([a-z]*)$/i', "$1$2", $pattern);
	}
	
	function getValue($key, $default_value = false)
	{
		if (!isset($key) || empty($key) || !is_string($key))
			return false;
		$ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $default_value));

		if (is_string($ret) === true)
			$ret = urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret)));
		return !is_string($ret)? $ret : stripslashes($ret);
	}
	
	
	function isSubmit($submit)
	{
		return (
			isset($_POST[$submit]) || isset($_POST[$submit.'_x']) || isset($_POST[$submit.'_y'])
			|| isset($_GET[$submit]) || isset($_GET[$submit.'_x']) || isset($_GET[$submit.'_y'])
		);
	}
	
	function showErrors($errors){
		echo '
			<div class="alert alert-danger col-md-6 col-md-offset-3">';
		foreach ($errors as $e){
			echo '<p class="text-center">'.$e.'</p>';
		}
		echo '
			</div>';
	}
	
	function getLevelColor($level){
		
			switch ($level){
				case 1:
					return '#fece5a';
				case 2:
					return '#ffa630';
				case 3:
					return '#ff7315';
				case 4:
					return '#e40000';
				case 5:
					return '#fd2992';
				case 6:
					return '#eb26cd';
				case 7:
					return '#c124e0';
				case 8:
					return '#9627f4';
				case 'FLIP_CARD':
					return '#FFFFFF';
			}
	}
	
	function getRarityColor($rarity){
		switch ($rarity){
			case 'COMMON':
				return '#84FBBD';
			case 'RARE' :
				return '#AD8EFF';
			case 'VERY_RARE':
				return '#F78AF7';
		}
	}
	function getFactionColor($faction){
		switch ($faction){
			case 'ALIENS':
				return '#00F170';
			case 'RESISTANCE':
				return '#00C2FF';

			default:
				return '#FFFFFF';
		}
	}
//	
//.aliens { color: rgb(0,241,112);  text-shadow: 0 0 2px rgb(0,241,112);}
//.resistance { color:rgb(0,194,255); text-shadow: 0 0 2px rgb(0,194,255);}

function dump($var){
	if(DEV_MODE)
		var_dump($var);

}


function friendly_name ($item){
	switch ($item){
		case 'EMITTER_A' : return 'Resonator';
		case 'RES_SHIELD' : return 'Portal Shield';
		case 'FORCE_AMP' : return 'Force Amp';
		case 'HEATSINK' : return 'Heat Sink';
		case 'LINK_AMPLIFIER' : return 'Link Amp';
		case 'MULTIHACK' : return 'Multi-hack';
		case 'TURRET' : return 'Turret';
		case 'EMP_BURSTER' : return 'XMP Burster';
		case 'POWER_CUBE' : return 'Power Cube';
		case 'FLIP_CARD' : return 'Alignment virus';
		case 'ADA' : return 'ADA Refractor';
		case 'JARVIS' : return 'Jarvis Virus';
		
		default : return $item;
		
	}
	
	
}
function getCoordinates($address){
 
	$address = str_replace(" ", "+", $address); // replace all the white space with "+" sign to match with google search pattern
	 
	$url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";
	 
	$response = file_get_contents($url);

	$json = json_decode($response,TRUE); //generate array object from the response from the web

	 if($json['status'] =='ZERO_RESULTS') return false;
	return array('lat' => $json['results'][0]['geometry']['location']['lat'], 'lng' => $json['results'][0]['geometry']['location']['lng']);
	 
}
?>