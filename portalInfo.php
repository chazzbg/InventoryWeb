<?php
require 'config.php';
require 'functions.php';
session_start();
require 'API.php';
if (!isset($_COOKIE['PHPSESSID'])) {
	die(json_encode(array('error'=>'You must be logged in!')));
} else {

	$res = mysql_query('SELECT * FROM sessions s 
	JOIN users u ON (u.id_user = s.id_user)		
	WHERE s.sess_id = \'' . $_COOKIE['PHPSESSID'] . '\'');
	


	if (!mysql_num_rows($res)) {
		
		die(json_encode(array('error'=>'You must be logged in!')));
	} else {
		
		$_SESSION = mysql_fetch_assoc($res);
		$_SESSION['logged'] = true;
		
		$guid = getValue('guid');
		$timestamp = (int)getValue('timestamp');
		if(!isGuid($guid))
			die(json_encode(array('error'=>'Invalid game entity!')));
		
		
		
		
		
		$api = new API();
		$api->setSACSID($_SESSION['sacsid']);
		if($api->handshake()){
			
			
			$portal = $api->getModifiedEntities(getValue('guid'),$timestamp);
			if(is_null($portal) OR $portal==false) {
					$portal = $api->getModifiedEntities(getValue('guid'),0);
					if(is_null($portal) OR $portal==false) {
						die(json_encode(array('error'=>'Server Error!')));
					}
			}
			if(!isSubmit('ajax'))	
				var_dump($portal);

			$portal_data = array();
			
			$portal_data['controllingTeam'] = $portal[0][2]['controllingTeam']['team'];
			
			

			if($portal[0][2]['controllingTeam']['team'] == 'NEUTRAL'){
				$portal_data['level'] = 1;
				
			} else {
				$portal_data['owner'] = $api->getNicknameFromUserGUID($portal[0][2]['captured']['capturingPlayerId']);
				$portal_data['ownerSince'] = @date ('M j, Y H:i', $portal[0][2]['captured']['capturedTime']/1000);
				
				$portal_data['links'] = count($portal[0][2]['portalV2']['linkedEdges']);
				
				foreach ($portal[0][2]['portalV2']['linkedModArray'] as $slot => $mod){
					$portal_data['mods'][$slot] = is_null($mod)? false : array(
						'type'=>$mod['type'],
						'rarity' => $mod['rarity'],
						'owner' => $api->getNicknameFromUserGUID($mod['installingUser']),
						'stats' => $mod['stats'],
						'name'=>$mod['displayName'],
					);
				}
				
				
				foreach ($portal[0][2]['resonatorArray']['resonators'] as $slot => $resonator){
					$portal_data['resonators'][$slot] = is_null($resonator) ? false : array(
						'level' =>$resonator['level'],
						'distanceToPortal' => $resonator['distanceToPortal'],
						'owner' => $api->getNicknameFromUserGUID($resonator['ownerGuid']),
						'energyTotal' => $resonator['energyTotal'],
						'energyMax' => $api->getResonatorEnergyLevel($resonator['level']),
					);
					if(!is_null($resonator)){
						@$portal_data['energy'] += $resonator['energyTotal'];
						@$portal_data['level'] += $resonator['level'];
						@$portal_data['energyMax'] += $api->getResonatorEnergyLevel($resonator['level']);
					}  
					
				}
				$portal_data['level'] = $portal_data['level']/8 < 1.00 ? 1: floor($portal_data['level']/8);
			}
			
			echo json_encode($portal_data);
			
			
		} else {
			die(json_encode(array('error'=>'Expired Auth code!')));
		}
		
	}
}

?>