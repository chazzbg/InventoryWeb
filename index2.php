<?php
require 'config.php';
require 'API.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
		<link href='http://fonts.googleapis.com/css?family=Coda:400,700' rel='stylesheet' type='text/css'>
		<link href="style.css" rel='stylesheet' type='text/css'>
		
		
    </head>
    <body>
		<?php

		
		$api = new API();
		$api->setSACSID('AJKiYcFYtHt4JrSIVyXBrWrrZETttpuU7Smxn6T5rdh86KpnsKterEQpmoSMIyhgVDsV67IMYpNfB3w4Uyj7beQAlmCzVSu-b_gPdkCbAMSv--GVtYnV3-goixlefkjMm6cQxJwrKjr9Cidq5KLbzIOuPP1quWX-J6_MiYcQEZyUaKQY1DoGdZvtfoYMWt6NYNj9E1_RiBG9ySTCrPtnGFqEJfm1qNYZvlZhfklRx2Aeyb0PsfAB1RU246pIFxq-eONcrYgSUbtAt0p6i8y92DtEFA-4mtCTGe-4u2Bmuds4LXrktMpgF8ibJI-rW4lHZyoKq70H0NWGkzS1ifXfk2inXTGDV70I0bWt52jZ_HyKohA9e9adESgQcdQWETQW3hKgDQVWif2t9binlG8EIs3ozn3tKsGW8W7iUmg2j2NUtJGn-fz8DzeDqZuHyMoTBGaj5mJX_bRVEyiSu6wxI3y3_1SF1B6UmVIrcvPyVYGP_YSj_9ugeLdrgvTgdksiYTxHSYl6Yes_RpnkP1qOWOm5EBjdopB6IlopUTsWV9sFc2gNCUacHQwUWOnilBhSPLdvDD6aQ25CK0klh5A7CQpFlgWgBDPq9lL6UgO4lRJJ7-zyuI-RhK0YDCchedMSyp3o7dfyzB69');
		if($api->handshake()){


			$cellid = sprintf("%04x", 4);
			$cell = $api->getRegionScore('40a9');

			if($cell != false ){
				$id = $cell['card']['cellIdToken'];
				$name = $cell['card']['regionName'];

				$vertices = $cell['regionVertices'];
				$vert = array();
				foreach($vertices as $v){
					$temp = explode(',',$v);
					$vert[] =  array('lat' => hexdec($temp[0])/1e6, 'lng' => hexdec($temp[1])/1e6);
				}

				$minLat = $maxLat = $vert[0]['lat'];
				$minLng = $maxLng = $vert[0]['lng'];

				foreach($vert as $v){
					if($v['lat'] < $minLat)
						$minLat = $v['lat'];

					if($v['lat'] > $maxLat)
						$maxLat = $v['lat'];

					if($v['lng'] < $minLng)
						$minLng = $v['lng'];

					if($v['lng'] > $maxLng)
						$maxLng = $v['lng'];

				}
				$res =  mysql_query("INSERT IGNORE INTO regions (`id_cell`, `region_name`,`vert_1_lat`,`vert_1_lng`,`vert_2_lat`,`vert_2_lng`,`vert_3_lat`,`vert_3_lng`,`vert_4_lat`,`vert_4_lng`, `max_lat`    , `min_lat`    , `max_lng`    , `min_lng`  )
							VALUES ('$id','$name',
									'".$vert[0]['lat']."','".$vert[0]['lng']."',
									'".$vert[1]['lat']."','".$vert[1]['lng']."',
									'".$vert[2]['lat']."','".$vert[2]['lng']."',
									'".$vert[3]['lat']."','".$vert[3]['lng']."',
									'$maxLat','$minLat','$maxLng','$minLng')");

			} else  {
				echo $api->getError()."\n";


			}
		} else {
			echo 'need reauth!';
		}
		?>
		
</html>
