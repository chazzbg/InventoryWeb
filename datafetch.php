<?php

set_time_limit(0);
ignore_user_abort(true);
$api = new API();
$api->setSACSID($_SESSION['sacsid']);
$res = mysql_query('SELECT *, UNIX_TIMESTAMP(date) as date_unix FROM data WHERE id_user = '.(int)$_SESSION['id_user'].' ORDER BY date DESC LIMIT 12')or die(mysql_error());
if (isSubmit('forcesync'))
			$force_sync = true;
if(!mysql_num_rows($res) OR ($force_sync)){
	
	if($api->handshake()){
		$player_data = array(
			'name'=>$api->getPlayerName(),
			'team'=>$api->getPlayerTeam(),
			'level'=>$api->getPlayerLevel(),
			'ap'=>$api->getPlayerAP(),
			'diffAp'=>$api->getPlayerLevel() <8 ? $api->getPlayerDiffAp(): 0,
			'apPercent'=>$api->getPlayerApPercents(),
			'energy' => $api->getPlayerEnergy(),
			'invites'=> $api->getInvitesNum(),
		);
		$player_inventory = $api->getInventory();
		$player_profile = $api->getPlayerProfile($player_data['name']);

		
		if($player_inventory != false){
			

			mysql_query('INSERT INTO data (id_user,date) VALUES ( '.(int)$_SESSION['id_user'].', NOW() )');

			$id = mysql_insert_id();
			
			mysql_query('UPDATE data SET player_data = \''.mysql_real_escape_string(json_encode($player_data)).'\' WHERE id_data = '.$id);
			mysql_query('UPDATE data SET  player_inventory = \''.  mysql_real_escape_string(json_encode($player_inventory)).'\' WHERE id_data = '.$id);
			mysql_query('UPDATE data SET  player_profile = \''.  mysql_real_escape_string(json_encode($player_profile)).'\' WHERE id_data = '.$id);

			mysql_query('UPDATE users SET sacsid = \''.$api->getSACSID().'\',last_login = NOW() WHERE id_user = '.(int)$_SESSION['id_user']);
			// remove older entries
			
			
			if(mysql_num_rows($res) ==12 ){
				mysql_data_seek($res, 11);

				$last = (mysql_fetch_assoc($res));

				mysql_query('DELETE FROM data WHERE id_data <= '.$last['id_data'].' AND  id_user = '.(int)$_SESSION['id_user']) or die(mysql_error());
			}
			
			
			header('Location: index.php');

		} else {
			$error = true;
			showErrors(array('Problem with server connection'));
		}
		
	} else {
		
		
		header('Location: index.php?page=reauth');
		die();
	}
	
	
	
} else {
	mysql_data_seek($res, 0);
	$data = mysql_fetch_assoc($res);


	$player_data = json_decode($data['player_data'],true);
	$player_inventory = json_decode($data['player_inventory'],true);
	$player_profile = json_decode($data['player_profile'],true);
	$last_sync = $data['date'];


	$history = array();
	mysql_data_seek($res, 0);
	while($row = mysql_fetch_assoc($res)){
		$history[$row['date_unix']]['player_data'] = json_decode($row['player_data'],true);
		$history[$row['date_unix']]['player_inventory'] = json_decode($row['player_inventory'],true);
		$history[$row['date_unix']]['player_profile'] = json_decode($row['player_profile'],true);
	}
	
	$history =	array_reverse($history,true);
	
	end($history);
	$last = prev($history);
	reset($history);

	

}
?>
